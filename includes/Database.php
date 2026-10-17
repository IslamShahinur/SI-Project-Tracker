<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database Abstraction Class
 */
class Database {

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function get_projects_table() {
        return $this->wpdb->prefix . 'si_projects';
    }

    public function get_notes_table() {
        return $this->wpdb->prefix . 'si_project_notes';
    }

    public function get_docs_table() {
        return $this->wpdb->prefix . 'si_project_docs';
    }

    public function get_reminders_table() {
        return $this->wpdb->prefix . 'si_project_reminders';
    }

    public function query($sql, $args = array()) {
        if (!empty($args)) {
            $prepared = $this->wpdb->prepare($sql, $args);
            return $this->wpdb->get_results($prepared, ARRAY_A);
        }
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function insert($table, $data, $format = null) {
        $result = $this->wpdb->insert($table, $data, $format);
        if ($result) {
            return $this->wpdb->insert_id;
        }
        return false;
    }

    public function update($table, $data, $where, $format = null, $where_format = null) {
        return $this->wpdb->update($table, $data, $where, $format, $where_format);
    }

    public function delete($table, $where, $where_format = null) {
        return $this->wpdb->delete($table, $where, $where_format);
    }

    public function drop_tables() {
        $this->wpdb->query("DROP TABLE IF EXISTS " . $this->get_docs_table());
        $this->wpdb->query("DROP TABLE IF EXISTS " . $this->get_notes_table());
        $this->wpdb->query("DROP TABLE IF EXISTS " . $this->get_projects_table());
    }
}
