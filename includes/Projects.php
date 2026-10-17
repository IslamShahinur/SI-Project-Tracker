<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Projects Manager Class
 */
class Projects {

    private $db;

    /**
     * Built-in categories. Site owners can add more via Settings; those are
     * stored in the 'si_pt_custom_categories' option and merged in here.
     */
    public static function get_categories() {
        $builtin = array('Website', 'Plugin', 'Android App', 'iOS App', 'API/Backend', 'Desktop');
        $custom = get_option('si_pt_custom_categories', array());
        if (!is_array($custom)) {
            $custom = array();
        }
        return array_values(array_unique(array_merge($builtin, $custom)));
    }

    public static function get_custom_categories() {
        $custom = get_option('si_pt_custom_categories', array());
        return is_array($custom) ? $custom : array();
    }

    public static function add_custom_category($name) {
        $name = trim(sanitize_text_field($name));
        if (empty($name)) {
            return false;
        }
        $builtin = array('Website', 'Plugin', 'Android App', 'iOS App', 'API/Backend', 'Desktop');
        if (in_array($name, $builtin, true)) {
            return false;
        }
        $custom = self::get_custom_categories();
        if (in_array($name, $custom, true)) {
            return true; // already exists
        }
        $custom[] = $name;
        return update_option('si_pt_custom_categories', $custom);
    }

    public static function delete_custom_category($name) {
        $custom = self::get_custom_categories();
        $custom = array_values(array_filter($custom, function ($c) use ($name) {
            return $c !== $name;
        }));
        return update_option('si_pt_custom_categories', $custom);
    }

    public static function get_statuses() {
        return array('Planning', 'Active', 'On Hold', 'Completed');
    }

    /**
     * Distinct background/text color pair for a category badge. Built-in
     * categories get hand-picked colors; any custom category gets a
     * deterministic color from a rotating palette (same name always maps
     * to the same colors) so newly added categories are still visually
     * distinct from one another without any manual configuration.
     */
    public static function get_category_colors($category) {
        $map = array(
            'Website'     => array('bg' => '#dbeeff', 'text' => '#0b3d66'),
            'Plugin'      => array('bg' => '#fdf0b0', 'text' => '#1c2541'),
            'Android App' => array('bg' => '#dcf5e3', 'text' => '#155d34'),
            'iOS App'     => array('bg' => '#e6e6fa', 'text' => '#2f2f80'),
            'API/Backend' => array('bg' => '#f1e2fb', 'text' => '#5b21a6'),
            'Desktop'     => array('bg' => '#ffe3d9', 'text' => '#8a3b22'),
        );

        if (isset($map[$category])) {
            return $map[$category];
        }

        $palette = array(
            array('bg' => '#fde2e4', 'text' => '#9d174d'),
            array('bg' => '#d9f7f0', 'text' => '#0f6659'),
            array('bg' => '#fef3c7', 'text' => '#92400e'),
            array('bg' => '#e0e7ff', 'text' => '#3730a3'),
            array('bg' => '#dcfce7', 'text' => '#166534'),
            array('bg' => '#fae8ff', 'text' => '#86198f'),
            array('bg' => '#e2f5fe', 'text' => '#075985'),
            array('bg' => '#fee2d5', 'text' => '#9a3412'),
        );

        $index = abs(crc32((string) $category)) % count($palette);
        return $palette[$index];
    }

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function get_all($status = '', $category = '') {
        $table = $this->db->get_projects_table();
        $notes_table = $this->db->get_notes_table();
        $docs_table = $this->db->get_docs_table();

        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM $notes_table n WHERE n.project_id = p.id) AS note_count,
                    (SELECT COUNT(*) FROM $docs_table d WHERE d.project_id = p.id) AS doc_count
                FROM $table p WHERE 1=1";
        $args = array();

        if (!empty($status)) {
            $sql .= " AND p.status = %s";
            $args[] = $status;
        }

        if (!empty($category)) {
            $sql .= " AND p.category = %s";
            $args[] = $category;
        }

        $sql .= " ORDER BY p.updated_at DESC";

        return $this->db->query($sql, $args);
    }

    public function get_by_id($id) {
        $table = $this->db->get_projects_table();
        $sql = "SELECT * FROM $table WHERE id = %d";
        $results = $this->db->query($sql, array(intval($id)));
        return !empty($results) ? $results[0] : null;
    }

    public function create($data) {
        // Required fields must be present; fail cleanly instead of
        // triggering PHP warnings on undefined array keys.
        if (empty($data['title']) || empty($data['category']) || empty($data['status'])) {
            return false;
        }

        $table = $this->db->get_projects_table();
        $insert_data = array(
            'title'       => sanitize_text_field($data['title']),
            'category'    => sanitize_text_field($data['category']),
            'status'      => sanitize_text_field($data['status']),
            'progress'    => min(100, max(0, intval($data['progress'] ?? 0))),
            'description' => wp_kses_post($data['description'] ?? ''),
            'repo_url'    => esc_url_raw($data['repo_url'] ?? ''),
            'live_url'    => esc_url_raw($data['live_url'] ?? ''),
            'target_date' => !empty($data['target_date']) ? sanitize_text_field($data['target_date']) : null,
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql')
        );

        return $this->db->insert($table, $insert_data);
    }

    public function update($id, $data) {
        $table = $this->db->get_projects_table();
        $update_data = array(
            'updated_at' => current_time('mysql')
        );

        if (isset($data['title'])) $update_data['title'] = sanitize_text_field($data['title']);
        if (isset($data['category'])) $update_data['category'] = sanitize_text_field($data['category']);
        if (isset($data['status'])) $update_data['status'] = sanitize_text_field($data['status']);
        if (isset($data['progress'])) $update_data['progress'] = min(100, max(0, intval($data['progress'])));
        if (isset($data['description'])) $update_data['description'] = wp_kses_post($data['description']);
        if (isset($data['repo_url'])) $update_data['repo_url'] = esc_url_raw($data['repo_url']);
        if (isset($data['live_url'])) $update_data['live_url'] = esc_url_raw($data['live_url']);
        if (isset($data['target_date'])) $update_data['target_date'] = !empty($data['target_date']) ? sanitize_text_field($data['target_date']) : null;

        return $this->db->update($table, $update_data, array('id' => intval($id)));
    }

    public function delete($id) {
        $project_id = intval($id);
        // Delete documents
        $this->db->delete($this->db->get_docs_table(), array('project_id' => $project_id));
        // Delete notes
        $this->db->delete($this->db->get_notes_table(), array('project_id' => $project_id));
        // Delete project
        return $this->db->delete($this->db->get_projects_table(), array('id' => $project_id));
    }

    // --- REFERENCE DOCUMENTS HANDLERS ---
    public function get_documents($project_id) {
        $table = $this->db->get_docs_table();
        $sql = "SELECT * FROM $table WHERE project_id = %d ORDER BY created_at DESC";
        return $this->db->query($sql, array(intval($project_id)));
    }

    public function add_document($project_id, $data) {
        if (empty($project_id) || empty($data['file_name']) || empty($data['file_url'])) {
            return false;
        }

        $table = $this->db->get_docs_table();
        $doc_data = array(
            'project_id'   => intval($project_id),
            'file_name'    => sanitize_text_field($data['file_name']),
            'description'  => sanitize_textarea_field($data['description'] ?? ''),
            'service_type' => sanitize_text_field($data['service_type'] ?? 'google_drive'),
            'file_url'     => esc_url_raw($data['file_url']),
            'file_size'    => sanitize_text_field($data['file_size'] ?? ''),
            'created_at'   => current_time('mysql')
        );
        return $this->db->insert($table, $doc_data);
    }

    public function delete_document($doc_id) {
        return $this->db->delete($this->db->get_docs_table(), array('id' => intval($doc_id)));
    }
}
