<?php
/**
 * Plugin Name:       SI Project Tracker
 * Plugin URI:        https://example.com/si-project-tracker
 * Description:       A lightweight WordPress plugin to track personal developer projects with status, progress percentage, chronological notes, and Google Drive & OneDrive reference documents.
 * Version:           1.1.0
 * Author:            SI Developer Team
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       si-project-tracker
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('SI_PT_VERSION', '1.1.0');
define('SI_PT_FILE', __FILE__);
define('SI_PT_PATH', plugin_dir_path(__FILE__));
define('SI_PT_URL', plugin_dir_url(__FILE__));

// Autoload plugin classes
spl_autoload_register(function ($class) {
    $prefix = 'SI_Project_Tracker\\';
    $base_dir = SI_PT_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Activation Hook
 */
function activate_si_project_tracker() {
    require_once SI_PT_PATH . 'includes/Activator.php';
    SI_Project_Tracker\Activator::activate();
}

/**
 * Deactivation Hook
 */
function deactivate_si_project_tracker() {
    $timestamp = wp_next_scheduled('si_pt_check_reminders');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'si_pt_check_reminders');
    }
}

register_activation_hook(__FILE__, 'activate_si_project_tracker');
register_deactivation_hook(__FILE__, 'deactivate_si_project_tracker');

/**
 * Initialize Singleton Plugin Instance
 */
function run_si_project_tracker() {
    require_once SI_PT_PATH . 'includes/Activator.php';
    SI_Project_Tracker\Activator::maybe_upgrade();

    $plugin = SI_Project_Tracker\Plugin::get_instance();
    $plugin->run();
}
run_si_project_tracker();
