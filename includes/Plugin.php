<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin Singleton Class
 */
class Plugin {

    private static $instance = null;

    private $admin;
    private $database;
    private $projects;
    private $notes;
    private $dashboard;
    private $reminders;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->database  = new Database();
        $this->projects  = new Projects($this->database);
        $this->notes     = new Notes($this->database, $this->projects);
        $this->dashboard = new Dashboard($this->database);
        $this->reminders = new Reminders($this->database, $this->projects);

        if (is_admin()) {
            $this->admin = new Admin($this->projects, $this->notes, $this->dashboard, $this->reminders);
        }
    }

    public function run() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        add_filter('cron_schedules', array($this, 'add_cron_schedule'));
        add_action('si_pt_check_reminders', array($this->reminders, 'process_due_reminders'));

        if (!wp_next_scheduled('si_pt_check_reminders')) {
            wp_schedule_event(time(), 'si_pt_fifteen_minutes', 'si_pt_check_reminders');
        }

        if (is_admin()) {
            $this->admin->init();
        }
    }

    public function add_cron_schedule($schedules) {
        $schedules['si_pt_fifteen_minutes'] = array(
            'interval' => 15 * MINUTE_IN_SECONDS,
            'display'  => __('Every 15 Minutes (SI Project Tracker)', 'si-project-tracker'),
        );
        return $schedules;
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'si-project-tracker',
            false,
            dirname(plugin_basename(SI_PT_FILE)) . '/languages/'
        );
    }
}
