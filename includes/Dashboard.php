<?php
namespace SI_Project_Tracker;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Aggregator Class
 * Calculates overview metrics, average progress, and activity feed
 */
class Dashboard {

    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function get_stats() {
        $projects_table = $this->db->get_projects_table();
        $notes_table = $this->db->get_notes_table();
        $docs_table = $this->db->get_docs_table();

        $all_projects = $this->db->query("SELECT status, progress FROM $projects_table");
        
        $total = count($all_projects);
        $active = 0;
        $on_hold = 0;
        $completed = 0;
        $planning = 0;
        $sum_progress = 0;

        foreach ($all_projects as $p) {
            $sum_progress += intval($p['progress']);
            switch ($p['status']) {
                case 'Active':
                    $active++;
                    break;
                case 'On Hold':
                    $on_hold++;
                    break;
                case 'Completed':
                    $completed++;
                    break;
                default:
                    $planning++;
                    break;
            }
        }

        $avg_progress = $total > 0 ? round($sum_progress / $total) : 0;

        $recent_notes = $this->db->query("SELECT n.*, p.title as project_title 
            FROM $notes_table n 
            JOIN $projects_table p ON n.project_id = p.id 
            ORDER BY n.note_date DESC LIMIT 5");

        $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM $docs_table");
        $doc_count = !empty($total_docs) ? intval($total_docs[0]['cnt']) : 0;

        return array(
            'total_projects'  => $total,
            'active_projects' => $active,
            'on_hold'         => $on_hold,
            'completed'       => $completed,
            'planning'        => $planning,
            'avg_progress'    => $avg_progress,
            'total_docs'      => $doc_count,
            'recent_updates'  => $recent_notes
        );
    }

    /**
     * Per-category project counts and average progress, for the
     * "Category Analytics" panel in the modern dashboard view.
     */
    public function get_category_stats() {
        $projects_table = $this->db->get_projects_table();
        $rows = $this->db->query("SELECT category, progress FROM $projects_table");

        $categories = Projects::get_categories();
        $stats = array();
        foreach ($categories as $cat) {
            $stats[$cat] = array('count' => 0, 'sum' => 0, 'avg' => 0);
        }

        foreach ($rows as $row) {
            $cat = $row['category'];
            if (!isset($stats[$cat])) {
                $stats[$cat] = array('count' => 0, 'sum' => 0, 'avg' => 0);
            }
            $stats[$cat]['count']++;
            $stats[$cat]['sum'] += intval($row['progress']);
        }

        foreach ($stats as $cat => $data) {
            $stats[$cat]['avg'] = $data['count'] > 0 ? round($data['sum'] / $data['count']) : 0;
        }

        return $stats;
    }
}
