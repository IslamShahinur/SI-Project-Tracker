<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Project Reminders Manager
 * Schedules reminders per-project and dispatches them via Email, Slack,
 * and/or WhatsApp when they come due (checked by WP-Cron).
 */
class Reminders {

    private $db;
    private $projects;

    public static function get_channels() {
        return array('email', 'slack', 'whatsapp');
    }

    public function __construct(Database $db, Projects $projects) {
        $this->db = $db;
        $this->projects = $projects;
    }

    public function get_all() {
        $table = $this->db->get_reminders_table();
        $projects_table = $this->db->get_projects_table();
        $sql = "SELECT r.*, p.title AS project_title
                FROM $table r
                LEFT JOIN $projects_table p ON r.project_id = p.id
                ORDER BY r.remind_at ASC";
        return $this->db->query($sql);
    }

    public function get_for_project($project_id) {
        $table = $this->db->get_reminders_table();
        $sql = "SELECT * FROM $table WHERE project_id = %d ORDER BY remind_at ASC";
        return $this->db->query($sql, array(intval($project_id)));
    }

    public function get_due() {
        $table = $this->db->get_reminders_table();
        $sql = "SELECT r.*, p.title AS project_title
                FROM $table r
                LEFT JOIN {$this->db->get_projects_table()} p ON r.project_id = p.id
                WHERE r.status = 'pending' AND r.remind_at <= %s";
        return $this->db->query($sql, array(current_time('mysql')));
    }

    public function create($data) {
        $project_id = isset($data['project_id']) ? intval($data['project_id']) : 0;
        if (!$project_id || empty($data['remind_at'])) {
            return false;
        }
        $project = $this->projects->get_by_id($project_id);
        if (!$project) {
            return false;
        }

        $channels = array();
        if (!empty($data['channels']) && is_array($data['channels'])) {
            $channels = array_intersect($data['channels'], self::get_channels());
        }
        if (empty($channels)) {
            $channels = array('email');
        }

        $table = $this->db->get_reminders_table();
        return $this->db->insert($table, array(
            'project_id' => $project_id,
            'remind_at'  => sanitize_text_field($data['remind_at']),
            'channels'   => implode(',', $channels),
            'message'    => sanitize_textarea_field($data['message'] ?? ''),
            'status'     => 'pending',
            'created_at' => current_time('mysql'),
        ));
    }

    public function delete($id) {
        return $this->db->delete($this->db->get_reminders_table(), array('id' => intval($id)));
    }

    public function mark_sent($id) {
        return $this->db->update(
            $this->db->get_reminders_table(),
            array('status' => 'sent', 'sent_at' => current_time('mysql')),
            array('id' => intval($id))
        );
    }

    /**
     * Called on the si_pt_check_reminders cron hook. Finds every pending
     * reminder whose time has come and dispatches it on each of its
     * configured channels, then marks it sent (even if a channel failed,
     * to avoid retry-spamming a broken webhook - failures are logged via
     * error_log so the site owner can fix credentials in Settings).
     */
    public function process_due_reminders() {
        $due = $this->get_due();
        if (empty($due)) {
            return;
        }

        $settings = get_option('si_pt_settings', array());

        foreach ($due as $reminder) {
            $channels = array_filter(explode(',', $reminder['channels']));
            foreach ($channels as $channel) {
                $this->dispatch($channel, $reminder, $settings);
            }
            $this->mark_sent($reminder['id']);
        }
    }

    private function dispatch($channel, $reminder, $settings) {
        $project_title = $reminder['project_title'] ?? __('Unknown project', 'si-project-tracker');
        $message = !empty($reminder['message'])
            ? $reminder['message']
            : sprintf(__('Reminder for project "%s".', 'si-project-tracker'), $project_title);

        switch ($channel) {
            case 'email':
                if (empty($settings['reminder_email_enabled'])) {
                    return;
                }
                $to = !empty($settings['reminder_email_address']) ? $settings['reminder_email_address'] : get_option('admin_email');
                $subject = sprintf(__('[SI Project Tracker] Reminder: %s', 'si-project-tracker'), $project_title);
                wp_mail($to, $subject, $message);
                break;

            case 'slack':
                if (empty($settings['reminder_slack_enabled']) || empty($settings['reminder_slack_webhook'])) {
                    return;
                }
                wp_remote_post($settings['reminder_slack_webhook'], array(
                    'headers' => array('Content-Type' => 'application/json'),
                    'body'    => wp_json_encode(array(
                        'text' => sprintf("*%s*\n%s", $project_title, $message),
                    )),
                    'timeout' => 10,
                ));
                break;

            case 'whatsapp':
                if (empty($settings['reminder_whatsapp_enabled']) || empty($settings['reminder_whatsapp_webhook'])) {
                    return;
                }
                $args = array(
                    'headers' => array('Content-Type' => 'application/json'),
                    'body'    => wp_json_encode(array(
                        'project' => $project_title,
                        'message' => $message,
                    )),
                    'timeout' => 10,
                );
                if (!empty($settings['reminder_whatsapp_token'])) {
                    $args['headers']['Authorization'] = 'Bearer ' . $settings['reminder_whatsapp_token'];
                }
                wp_remote_post($settings['reminder_whatsapp_webhook'], $args);
                break;
        }
    }
}
