<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP Admin Interface Controller
 */
class Admin {

    private $projects;
    private $notes;
    private $dashboard;
    private $reminders;

    /** @var string 'modern' or 'classic' */
    private $view = 'modern';

    public function __construct(Projects $projects, Notes $notes, Dashboard $dashboard, Reminders $reminders) {
        $this->projects = $projects;
        $this->notes = $notes;
        $this->dashboard = $dashboard;
        $this->reminders = $reminders;
        $this->view = (isset($_GET['si_pt_view']) && $_GET['si_pt_view'] === 'classic') ? 'classic' : 'modern';
    }

    public function init() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_filter('admin_body_class', array($this, 'filter_admin_body_class'));

        // AJAX Endpoints
        add_action('wp_ajax_si_pt_save_project', array($this, 'ajax_save_project'));
        add_action('wp_ajax_si_pt_delete_project', array($this, 'ajax_delete_project'));
        add_action('wp_ajax_si_pt_add_note', array($this, 'ajax_add_note'));
        add_action('wp_ajax_si_pt_add_doc', array($this, 'ajax_add_doc'));
        add_action('wp_ajax_si_pt_delete_doc', array($this, 'ajax_delete_doc'));
        add_action('wp_ajax_si_pt_export_data', array($this, 'ajax_export_data'));
        add_action('wp_ajax_si_pt_import_data', array($this, 'ajax_import_data'));
        add_action('wp_ajax_si_pt_add_category', array($this, 'ajax_add_category'));
        add_action('wp_ajax_si_pt_delete_category', array($this, 'ajax_delete_category'));
        add_action('wp_ajax_si_pt_save_reminder', array($this, 'ajax_save_reminder'));
        add_action('wp_ajax_si_pt_delete_reminder', array($this, 'ajax_delete_reminder'));
    }

    /**
     * Is the current admin screen one of ours?
     */
    private function is_plugin_screen($hook = '') {
        if ($hook) {
            return strpos($hook, 'si-project-tracker') !== false || strpos($hook, 'si-pt') !== false;
        }
        if (!isset($_GET['page'])) {
            return false;
        }
        $page = sanitize_text_field(wp_unslash($_GET['page']));
        return strpos($page, 'si-project-tracker') !== false || strpos($page, 'si-pt') !== false;
    }

    /**
     * The modern view takes over the full admin screen (its own header and
     * sidebar), similar to page-builder style full-screen admin apps. This
     * body class lets modern.css hide the default WP admin chrome, but only
     * on our own screens and only when the modern view is active.
     */
    public function filter_admin_body_class($classes) {
        if ($this->is_plugin_screen() && $this->view === 'modern') {
            $classes .= ' si-pt-modern-view';
        }
        return $classes;
    }

    public function add_menu_pages() {
        add_menu_page(
            __('SI Project Tracker', 'si-project-tracker'),
            __('SI Projects', 'si-project-tracker'),
            'manage_options',
            'si-project-tracker',
            array($this, 'render_dashboard_page'),
            'dashicons-portfolio',
            30
        );

        add_submenu_page(
            'si-project-tracker',
            __('Dashboard', 'si-project-tracker'),
            __('Dashboard', 'si-project-tracker'),
            'manage_options',
            'si-project-tracker',
            array($this, 'render_dashboard_page')
        );

        add_submenu_page(
            'si-project-tracker',
            __('All Projects', 'si-project-tracker'),
            __('All Projects', 'si-project-tracker'),
            'manage_options',
            'si-pt-projects',
            array($this, 'render_projects_page')
        );

        add_submenu_page(
            'si-project-tracker',
            __('Reference Docs', 'si-project-tracker'),
            __('Reference Docs', 'si-project-tracker'),
            'manage_options',
            'si-pt-docs',
            array($this, 'render_docs_page')
        );

        add_submenu_page(
            'si-project-tracker',
            __('Settings', 'si-project-tracker'),
            __('Settings', 'si-project-tracker'),
            'manage_options',
            'si-pt-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'si-project-tracker',
            __('Reminders', 'si-project-tracker'),
            __('Reminders', 'si-project-tracker'),
            'manage_options',
            'si-pt-reminders',
            array($this, 'render_reminders_page')
        );
    }

    public function enqueue_assets($hook) {
        if (!$this->is_plugin_screen($hook)) {
            return;
        }

        if ($this->view === 'modern') {
            // The modern view has its own self-contained stylesheet/script;
            // deliberately NOT loading admin.css/admin.js alongside it,
            // since both files bind handlers to the same form IDs/classes
            // and loading both would fire every AJAX action twice.
            wp_enqueue_style(
                'si-pt-modern-css',
                SI_PT_URL . 'assets/css/modern.css',
                array(),
                SI_PT_VERSION
            );

            wp_enqueue_script(
                'si-pt-modern-js',
                SI_PT_URL . 'assets/js/modern.js',
                array('jquery'),
                SI_PT_VERSION,
                true
            );

            wp_localize_script('si-pt-modern-js', 'siPtData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('si_pt_admin_nonce')
            ));
        } else {
            wp_enqueue_style(
                'si-pt-admin-css',
                SI_PT_URL . 'assets/css/admin.css',
                array(),
                SI_PT_VERSION
            );

            wp_enqueue_script(
                'si-pt-admin-js',
                SI_PT_URL . 'assets/js/admin.js',
                array('jquery'),
                SI_PT_VERSION,
                true
            );

            wp_localize_script('si-pt-admin-js', 'siPtData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('si_pt_admin_nonce')
            ));
        }
    }

    /**
     * Build the "?si_pt_view=modern|classic" URL for the current admin page,
     * used by the view-switch toggle in the header.
     */
    private function view_url($view, $page = null) {
        $page = $page ? $page : (isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'si-project-tracker');
        return add_query_arg(array('page' => $page, 'si_pt_view' => $view), admin_url('admin.php'));
    }

    public function render_dashboard_page() {
        $stats = $this->dashboard->get_stats();
        $category_stats = $this->dashboard->get_category_stats();
        $projects = $this->projects->get_all();
        $view = $this->view;
        $view_urls = $this->nav_urls();

        if ($view === 'modern') {
            include SI_PT_PATH . 'views/modern/dashboard.php';
        } else {
            include SI_PT_PATH . 'views/dashboard.php';
        }
    }

    public function render_projects_page() {
        $projects = $this->projects->get_all();
        $view = $this->view;
        $view_urls = $this->nav_urls();

        if ($view === 'modern') {
            include SI_PT_PATH . 'views/modern/projects.php';
        } else {
            include SI_PT_PATH . 'views/projects.php';
        }
    }

    public function render_docs_page() {
        $projects = $this->projects->get_all();
        $documents = array();
        foreach ($projects as $project) {
            foreach ($this->projects->get_documents($project['id']) as $doc) {
                $doc['project_title'] = $project['title'];
                $documents[] = $doc;
            }
        }
        $view = $this->view;
        $view_urls = $this->nav_urls();

        if ($view === 'modern') {
            include SI_PT_PATH . 'views/modern/docs.php';
        } else {
            include SI_PT_PATH . 'views/docs.php';
        }
    }

    public function render_settings_page() {
        if (isset($_POST['si_pt_save_settings']) && check_admin_referer('si_pt_settings_nonce')) {
            $settings = array(
                'cleanup_on_uninstall'      => isset($_POST['cleanup_on_uninstall']) ? 1 : 0,
                'google_drive_client_id'    => sanitize_text_field($_POST['google_drive_client_id'] ?? ''),
                'onedrive_app_id'           => sanitize_text_field($_POST['onedrive_app_id'] ?? ''),
                'default_status'            => sanitize_text_field($_POST['default_status'] ?? 'Planning'),
                'enable_notifications'      => isset($_POST['enable_notifications']) ? 1 : 0,
                'reminder_email_enabled'    => isset($_POST['reminder_email_enabled']) ? 1 : 0,
                'reminder_email_address'    => sanitize_email($_POST['reminder_email_address'] ?? ''),
                'reminder_slack_enabled'    => isset($_POST['reminder_slack_enabled']) ? 1 : 0,
                'reminder_slack_webhook'    => esc_url_raw($_POST['reminder_slack_webhook'] ?? ''),
                'reminder_whatsapp_enabled' => isset($_POST['reminder_whatsapp_enabled']) ? 1 : 0,
                'reminder_whatsapp_webhook' => esc_url_raw($_POST['reminder_whatsapp_webhook'] ?? ''),
                'reminder_whatsapp_token'   => sanitize_text_field($_POST['reminder_whatsapp_token'] ?? ''),
            );
            update_option('si_pt_settings', $settings);
            $saved_notice = __('Settings updated successfully!', 'si-project-tracker');
        }
        $settings = get_option('si_pt_settings', array());
        $custom_categories = Projects::get_custom_categories();
        $view = $this->view;
        $view_urls = $this->nav_urls();

        if ($view === 'modern') {
            include SI_PT_PATH . 'views/modern/settings.php';
        } else {
            if (isset($saved_notice)) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($saved_notice) . '</p></div>';
            }
            include SI_PT_PATH . 'views/settings.php';
        }
    }

    public function render_reminders_page() {
        $reminders = $this->reminders->get_all();
        $projects = $this->projects->get_all();
        $view = $this->view;
        $view_urls = $this->nav_urls();

        if ($view === 'modern') {
            include SI_PT_PATH . 'views/modern/reminders.php';
        } else {
            include SI_PT_PATH . 'views/reminders.php';
        }
    }

    /**
     * Precomputed nav + view-toggle URLs, handed to every template so the
     * sidebar/header partials don't need to know about query-arg building.
     */
    private function nav_urls() {
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'si-project-tracker';
        return array(
            'dashboard' => add_query_arg(array('page' => 'si-project-tracker', 'si_pt_view' => $this->view), admin_url('admin.php')),
            'projects'  => add_query_arg(array('page' => 'si-pt-projects', 'si_pt_view' => $this->view), admin_url('admin.php')),
            'docs'      => add_query_arg(array('page' => 'si-pt-docs', 'si_pt_view' => $this->view), admin_url('admin.php')),
            'settings'  => add_query_arg(array('page' => 'si-pt-settings', 'si_pt_view' => $this->view), admin_url('admin.php')),
            'reminders' => add_query_arg(array('page' => 'si-pt-reminders', 'si_pt_view' => $this->view), admin_url('admin.php')),
            'to_modern' => $this->view_url('modern', $current_page),
            'to_classic'=> $this->view_url('classic', $current_page),
        );
    }

    /**
     * Every AJAX action in this plugin is admin-only. Bail out (with a 403)
     * if the current user doesn't hold the required capability. This must
     * be checked on every request, in addition to the nonce, because
     * wp_ajax_* hooks fire for *any* logged-in user, not just users who can
     * see the admin menu.
     */
    private function verify_permissions() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to do this.', 'si-project-tracker')), 403);
        }
    }

    // AJAX Handlers
    public function ajax_save_project() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            $result = $this->projects->update($id, $_POST);
            if (false === $result) {
                wp_send_json_error(array('message' => __('Could not update project. Please check the required fields.', 'si-project-tracker')));
            }
        } else {
            $id = $this->projects->create($_POST);
            if (false === $id) {
                wp_send_json_error(array('message' => __('Could not create project. Title, category, and status are required.', 'si-project-tracker')));
            }
        }

        wp_send_json_success(array('id' => $id, 'message' => __('Project saved successfully!', 'si-project-tracker')));
    }

    public function ajax_delete_project() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid project.', 'si-project-tracker')));
        }

        $this->projects->delete($id);
        wp_send_json_success(array('message' => __('Project deleted successfully!', 'si-project-tracker')));
    }

    public function ajax_add_note() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $note_id = $this->notes->add_note($project_id, $_POST);

        if (false === $note_id) {
            wp_send_json_error(array('message' => __('Could not add note. Please select a project and enter a note.', 'si-project-tracker')));
        }

        wp_send_json_success(array('note_id' => $note_id, 'message' => __('Note added and project updated!', 'si-project-tracker')));
    }

    public function ajax_add_doc() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $doc_id = $this->projects->add_document($project_id, $_POST);

        if (false === $doc_id) {
            wp_send_json_error(array('message' => __('Could not link document. Please select a project and fill in the required fields.', 'si-project-tracker')));
        }

        wp_send_json_success(array('doc_id' => $doc_id, 'message' => __('Document linked successfully!', 'si-project-tracker')));
    }

    public function ajax_delete_doc() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $doc_id = isset($_POST['doc_id']) ? intval($_POST['doc_id']) : 0;
        if ($doc_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid document.', 'si-project-tracker')));
        }

        $this->projects->delete_document($doc_id);
        wp_send_json_success(array('message' => __('Document deleted successfully!', 'si-project-tracker')));
    }

    /**
     * Bundle every project (with its notes and reference documents) into a
     * single JSON-able payload for the Settings > Export Total Data tool.
     */
    public function ajax_export_data() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $projects = $this->projects->get_all();
        $payload = array(
            'plugin'      => 'si-project-tracker',
            'version'     => defined('SI_PT_VERSION') ? SI_PT_VERSION : '1.0.0',
            'exported_at' => current_time('mysql'),
            'projects'    => array(),
        );

        foreach ($projects as $project) {
            $notes = $this->notes->get_project_notes($project['id']);
            $docs  = $this->projects->get_documents($project['id']);

            $payload['projects'][] = array(
                'title'       => $project['title'],
                'category'    => $project['category'],
                'status'      => $project['status'],
                'progress'    => intval($project['progress']),
                'description' => $project['description'],
                'repo_url'    => $project['repo_url'],
                'live_url'    => $project['live_url'],
                'target_date' => $project['target_date'],
                'notes'       => array_map(function ($n) {
                    return array(
                        'note'           => $n['note'],
                        'status_after'   => $n['status_after'],
                        'progress_after' => $n['progress_after'],
                        'note_date'      => $n['note_date'],
                    );
                }, $notes),
                'documents'   => array_map(function ($d) {
                    return array(
                        'file_name'    => $d['file_name'],
                        'description'  => $d['description'],
                        'service_type' => $d['service_type'],
                        'file_url'     => $d['file_url'],
                        'file_size'    => $d['file_size'],
                    );
                }, $docs),
            );
        }

        wp_send_json_success($payload);
    }

    /**
     * Restore/merge project data from a previously exported JSON payload.
     * Always inserts as new records (does not try to match/merge against
     * existing projects) so this is safe to run multiple times.
     */
    public function ajax_import_data() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $raw = isset($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
        if (empty($raw)) {
            wp_send_json_error(array('message' => __('No backup data received.', 'si-project-tracker')));
        }

        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['projects']) || !is_array($data['projects'])) {
            wp_send_json_error(array('message' => __('That file does not look like a valid SI Project Tracker backup.', 'si-project-tracker')));
        }

        $imported_projects = 0;
        $imported_notes = 0;
        $imported_docs = 0;

        foreach ($data['projects'] as $project_data) {
            if (empty($project_data['title'])) {
                continue;
            }

            $new_id = $this->projects->create(array(
                'title'       => $project_data['title'],
                'category'    => $project_data['category'] ?? 'Website',
                'status'      => $project_data['status'] ?? 'Planning',
                'progress'    => $project_data['progress'] ?? 0,
                'description' => $project_data['description'] ?? '',
                'repo_url'    => $project_data['repo_url'] ?? '',
                'live_url'    => $project_data['live_url'] ?? '',
                'target_date' => $project_data['target_date'] ?? '',
            ));

            if (!$new_id) {
                continue;
            }
            $imported_projects++;

            if (!empty($project_data['notes']) && is_array($project_data['notes'])) {
                foreach ($project_data['notes'] as $note_data) {
                    if (empty($note_data['note'])) {
                        continue;
                    }
                    $note_id = $this->notes->add_note($new_id, array(
                        'note'           => $note_data['note'],
                        'status_after'   => $note_data['status_after'] ?? '',
                        'progress_after' => $note_data['progress_after'] ?? '',
                    ));
                    if ($note_id) {
                        $imported_notes++;
                    }
                }
            }

            if (!empty($project_data['documents']) && is_array($project_data['documents'])) {
                foreach ($project_data['documents'] as $doc_data) {
                    if (empty($doc_data['file_name']) || empty($doc_data['file_url'])) {
                        continue;
                    }
                    $doc_id = $this->projects->add_document($new_id, array(
                        'file_name'    => $doc_data['file_name'],
                        'description'  => $doc_data['description'] ?? '',
                        'service_type' => $doc_data['service_type'] ?? 'google_drive',
                        'file_url'     => $doc_data['file_url'],
                        'file_size'    => $doc_data['file_size'] ?? '',
                    ));
                    if ($doc_id) {
                        $imported_docs++;
                    }
                }
            }

            // Re-apply the project's own saved status/progress, since
            // importing notes (which auto-sync the parent project) may
            // have overwritten it based on the last imported note.
            $this->projects->update($new_id, array(
                'status'   => $project_data['status'] ?? 'Planning',
                'progress' => $project_data['progress'] ?? 0,
            ));
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: 1: projects, 2: notes, 3: documents */
                __('Import complete: %1$d project(s), %2$d note(s), %3$d document(s).', 'si-project-tracker'),
                $imported_projects,
                $imported_notes,
                $imported_docs
            ),
        ));
    }

    public function ajax_add_category() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Please enter a category name.', 'si-project-tracker')));
        }

        $result = Projects::add_custom_category($name);
        if (false === $result) {
            wp_send_json_error(array('message' => __('That category already exists as a built-in category.', 'si-project-tracker')));
        }

        wp_send_json_success(array(
            'message'    => __('Category added.', 'si-project-tracker'),
            'categories' => Projects::get_categories(),
        ));
    }

    public function ajax_delete_category() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        Projects::delete_custom_category($name);

        wp_send_json_success(array(
            'message'    => __('Category removed.', 'si-project-tracker'),
            'categories' => Projects::get_categories(),
        ));
    }

    public function ajax_save_reminder() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $channels = isset($_POST['channels']) && is_array($_POST['channels'])
            ? array_map('sanitize_text_field', wp_unslash($_POST['channels']))
            : array();

        $reminder_id = $this->reminders->create(array(
            'project_id' => $_POST['project_id'] ?? 0,
            'remind_at'  => $_POST['remind_at'] ?? '',
            'channels'   => $channels,
            'message'    => $_POST['message'] ?? '',
        ));

        if (false === $reminder_id) {
            wp_send_json_error(array('message' => __('Could not save reminder. Please choose a project and a date/time.', 'si-project-tracker')));
        }

        wp_send_json_success(array('id' => $reminder_id, 'message' => __('Reminder scheduled!', 'si-project-tracker')));
    }

    public function ajax_delete_reminder() {
        check_ajax_referer('si_pt_admin_nonce', 'nonce');
        $this->verify_permissions();

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            wp_send_json_error(array('message' => __('Invalid reminder.', 'si-project-tracker')));
        }

        $this->reminders->delete($id);
        wp_send_json_success(array('message' => __('Reminder deleted.', 'si-project-tracker')));
    }
}
