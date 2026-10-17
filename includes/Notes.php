<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Developer Notes Manager Class
 * Handles chronological project logs and synchronizes status & progress updates
 */
class Notes {

    private $db;
    private $projects;

    public function __construct(Database $db, Projects $projects) {
        $this->db = $db;
        $this->projects = $projects;
    }

    public function get_project_notes($project_id) {
        $table = $this->db->get_notes_table();
        $sql = "SELECT n.*, u.display_name as author_name 
                FROM $table n 
                LEFT JOIN {$this->db->get_projects_table()} p ON n.project_id = p.id
                LEFT JOIN {$GLOBALS['wpdb']->users} u ON n.author_id = u.ID
                WHERE n.project_id = %d 
                ORDER BY n.note_date DESC";
        return $this->db->query($sql, array(intval($project_id)));
    }

    public function add_note($project_id, $data) {
        $table = $this->db->get_notes_table();
        $project = $this->projects->get_by_id($project_id);

        if (!$project || empty($data['note'])) {
            return false;
        }

        $note_text = sanitize_textarea_field($data['note']);
        $status_after = !empty($data['status_after']) ? sanitize_text_field($data['status_after']) : $project['status'];
        $progress_after = isset($data['progress_after']) && $data['progress_after'] !== '' 
            ? min(100, max(0, intval($data['progress_after']))) 
            : $project['progress'];

        $author_id = get_current_user_id() ? get_current_user_id() : 1;

        $note_id = $this->db->insert($table, array(
            'project_id'     => intval($project_id),
            'note'           => $note_text,
            'status_after'   => $status_after,
            'progress_after' => $progress_after,
            'author_id'      => $author_id,
            'note_date'      => current_time('mysql')
        ));

        // Automatically update the parent project's status and progress
        if ($note_id) {
            $this->projects->update($project_id, array(
                'status'   => $status_after,
                'progress' => $progress_after
            ));
        }

        return $note_id;
    }

    public function delete_note($note_id) {
        return $this->db->delete($this->db->get_notes_table(), array('id' => intval($note_id)));
    }
}
