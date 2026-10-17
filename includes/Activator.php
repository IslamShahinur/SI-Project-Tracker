<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Activator Class
 * Creates required DB tables: wp_si_projects, wp_si_project_notes,
 * wp_si_project_docs, wp_si_project_reminders
 */
class Activator {

    const DB_VERSION = '1.1.0';

    public static function activate() {
        self::create_tables();

        if (false === get_option('si_pt_settings')) {
            update_option('si_pt_settings', array(
                'cleanup_on_uninstall'    => 1,
                'google_drive_client_id'  => '',
                'onedrive_app_id'         => '',
                'default_status'          => 'Planning',
                'enable_notifications'    => 1,
                'reminder_email_enabled'  => 1,
                'reminder_email_address'  => get_option('admin_email'),
                'reminder_slack_enabled'  => 0,
                'reminder_slack_webhook'  => '',
                'reminder_whatsapp_enabled' => 0,
                'reminder_whatsapp_webhook' => '',
                'reminder_whatsapp_token'   => '',
            ));
        }

        update_option('si_pt_db_version', self::DB_VERSION);
    }

    /**
     * Runs dbDelta again if the installed schema is older than what this
     * version of the plugin expects (e.g. the reminders table didn't exist
     * yet for sites that installed an earlier version). dbDelta is safe to
     * re-run - it only creates missing tables/columns.
     */
    public static function maybe_upgrade() {
        if (get_option('si_pt_db_version') !== self::DB_VERSION) {
            self::create_tables();
            update_option('si_pt_db_version', self::DB_VERSION);
        }
    }

    private static function create_tables() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset_collate = $wpdb->get_charset_collate();

        // 1. Projects Table
        $table_projects = $wpdb->prefix . 'si_projects';
        $sql_projects = "CREATE TABLE $table_projects (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            category varchar(100) NOT NULL DEFAULT 'Website',
            status varchar(50) NOT NULL DEFAULT 'Planning',
            progress int(3) NOT NULL DEFAULT 0,
            description longtext NULL,
            repo_url varchar(255) NULL,
            live_url varchar(255) NULL,
            target_date date NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_projects);

        // 2. Developer Notes Table
        $table_notes = $wpdb->prefix . 'si_project_notes';
        $sql_notes = "CREATE TABLE $table_notes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            note text NOT NULL,
            status_after varchar(50) NULL,
            progress_after int(3) NULL,
            author_id bigint(20) NOT NULL,
            note_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY project_id (project_id)
        ) $charset_collate;";
        dbDelta($sql_notes);

        // 3. Reference Documents Table (Mandatory Feature)
        $table_docs = $wpdb->prefix . 'si_project_docs';
        $sql_docs = "CREATE TABLE $table_docs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            file_name varchar(255) NOT NULL,
            description text NULL,
            service_type varchar(50) NOT NULL DEFAULT 'google_drive',
            file_url text NOT NULL,
            file_size varchar(50) NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY project_id (project_id)
        ) $charset_collate;";
        dbDelta($sql_docs);

        // 4. Reminders Table
        $table_reminders = $wpdb->prefix . 'si_project_reminders';
        $sql_reminders = "CREATE TABLE $table_reminders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            remind_at datetime NOT NULL,
            channels varchar(100) NOT NULL DEFAULT 'email',
            message text NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            sent_at datetime NULL,
            PRIMARY KEY  (id),
            KEY project_id (project_id),
            KEY remind_at (remind_at)
        ) $charset_collate;";
        dbDelta($sql_reminders);
    }
}
