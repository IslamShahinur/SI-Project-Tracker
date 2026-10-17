<?php
/**
 * SI Project Tracker Uninstall Handler
 * Drops database tables if "cleanup_on_uninstall" setting is checked
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$settings = get_option('si_pt_settings', array());

if (!empty($settings['cleanup_on_uninstall'])) {
    global $wpdb;

    $table_docs = $wpdb->prefix . 'si_project_docs';
    $table_notes = $wpdb->prefix . 'si_project_notes';
    $table_projects = $wpdb->prefix . 'si_projects';
    $table_reminders = $wpdb->prefix . 'si_project_reminders';

    $wpdb->query("DROP TABLE IF EXISTS $table_docs");
    $wpdb->query("DROP TABLE IF EXISTS $table_notes");
    $wpdb->query("DROP TABLE IF EXISTS $table_projects");
    $wpdb->query("DROP TABLE IF EXISTS $table_reminders");

    delete_option('si_pt_settings');
    delete_option('si_pt_custom_categories');
    delete_option('si_pt_db_version');
}
