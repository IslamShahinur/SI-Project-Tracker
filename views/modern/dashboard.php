<?php
/**
 * Modern Dashboard
 * Expects: $stats, $category_stats, $projects, $view_urls
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = 'dashboard';
include SI_PT_PATH . 'views/modern/partials/shell-open.php';

$category_icons = array(
    'Website'      => '&#127760;',
    'Plugin'       => '&#128268;',
    'Android App'  => '&#128241;',
    'iOS App'      => '&#127825;',
    'API/Backend'  => '&#128737;',
    'Desktop'      => '&#128421;',
);
?>
<div class="si-pt-hero">
    <div class="si-pt-hero-eyebrow">&#10024; <?php _e('SI Project Tracker Dashboard', 'si-project-tracker'); ?></div>
    <div class="si-pt-hero-top">
        <div>
            <h1><?php _e('Developer Overview & Progress', 'si-project-tracker'); ?></h1>
            <p><?php _e('Track websites, WP plugins, Android & iOS apps, APIs, developer logs and cloud reference documents.', 'si-project-tracker'); ?></p>
        </div>
    </div>
    <div class="si-pt-hero-progress-row">
        <span>&#128200; <?php _e('Overall Average Completion Progress', 'si-project-tracker'); ?></span>
        <strong><?php echo esc_html($stats['avg_progress']); ?>%</strong>
    </div>
    <div class="si-pt-hero-progress-bar">
        <div class="si-pt-hero-progress-fill" style="width: <?php echo esc_attr($stats['avg_progress']); ?>%;"></div>
    </div>
</div>

<div class="si-pt-stat-grid">
    <div class="si-pt-stat-card">
        <div class="si-pt-stat-card-top"><span><?php _e('Total Projects', 'si-project-tracker'); ?></span> &#128193;</div>
        <div class="si-pt-stat-value"><?php echo esc_html($stats['total_projects']); ?></div>
        <div class="si-pt-stat-sub"><?php echo count(\SI_Project_Tracker\Projects::get_categories()); ?> <?php _e('categories tracked', 'si-project-tracker'); ?></div>
    </div>
    <div class="si-pt-stat-card">
        <div class="si-pt-stat-card-top"><span><?php _e('Active', 'si-project-tracker'); ?></span> &#9889;</div>
        <div class="si-pt-stat-value" style="color:var(--si-blue);"><?php echo esc_html($stats['active_projects']); ?></div>
        <div class="si-pt-stat-sub"><?php _e('In active development', 'si-project-tracker'); ?></div>
    </div>
    <div class="si-pt-stat-card">
        <div class="si-pt-stat-card-top"><span><?php _e('On Hold', 'si-project-tracker'); ?></span> &#9203;</div>
        <div class="si-pt-stat-value" style="color:var(--si-amber);"><?php echo esc_html($stats['on_hold']); ?></div>
        <div class="si-pt-stat-sub"><?php _e('Paused / awaiting feedback', 'si-project-tracker'); ?></div>
    </div>
    <div class="si-pt-stat-card">
        <div class="si-pt-stat-card-top"><span><?php _e('Completed', 'si-project-tracker'); ?></span> &#9989;</div>
        <div class="si-pt-stat-value" style="color:var(--si-green);"><?php echo esc_html($stats['completed']); ?></div>
        <div class="si-pt-stat-sub"><?php _e('Finished & deployed', 'si-project-tracker'); ?></div>
    </div>
    <div class="si-pt-stat-card">
        <div class="si-pt-stat-card-top"><span><?php _e('Reference Docs', 'si-project-tracker'); ?></span> &#128196;</div>
        <div class="si-pt-stat-value" style="color:var(--si-purple);"><?php echo esc_html($stats['total_docs']); ?></div>
        <div class="si-pt-stat-sub"><?php _e('Google Drive & OneDrive', 'si-project-tracker'); ?></div>
    </div>
</div>

<div class="si-pt-panel">
    <div class="si-pt-panel-head">
        <h2>&#128202; <?php _e('Category Analytics & Average Completion', 'si-project-tracker'); ?></h2>
    </div>
    <div class="si-pt-cat-grid">
        <?php foreach (\SI_Project_Tracker\Projects::get_categories() as $cat) :
            $c = $category_stats[$cat] ?? array('count' => 0, 'avg' => 0);
            $colors = \SI_Project_Tracker\Projects::get_category_colors($cat);
        ?>
            <div class="si-pt-cat-box" style="border-left: 4px solid <?php echo esc_attr($colors['text']); ?>; background: <?php echo esc_attr($colors['bg']); ?>1a;">
                <div class="si-pt-cat-box-top">
                    <span><?php echo $category_icons[$cat] ?? ''; ?> <?php echo esc_html($cat); ?></span>
                    <span class="count"><?php echo esc_html($c['count']); ?> <?php echo _n('project', 'projects', $c['count'], 'si-project-tracker'); ?></span>
                </div>
                <div class="si-pt-cat-box-meta">
                    <span><?php _e('Avg Progress', 'si-project-tracker'); ?></span>
                    <span><?php echo esc_html($c['avg']); ?>%</span>
                </div>
                <div class="si-pt-hero-progress-bar" style="background:#eef0f4;">
                    <div class="si-pt-hero-progress-fill" style="width: <?php echo esc_attr($c['avg']); ?>%; background: linear-gradient(90deg, var(--si-accent), var(--si-purple));"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="si-pt-two-col">
    <div class="si-pt-panel">
        <div class="si-pt-panel-head">
            <h2>&#128230; <?php _e('Projects Overview', 'si-project-tracker'); ?></h2>
            <a href="<?php echo esc_url($view_urls['projects']); ?>" class="si-pt-link"><?php _e('View All List', 'si-project-tracker'); ?> (<?php echo count($projects); ?>) &rarr;</a>
        </div>

        <div class="si-pt-filter-pills" id="si-pt-dash-filter-pills">
            <span class="si-pt-filter-pill active" data-cat="all"><?php _e('All', 'si-project-tracker'); ?> (<?php echo count($projects); ?>)</span>
            <?php foreach (\SI_Project_Tracker\Projects::get_categories() as $cat) :
                $c = $category_stats[$cat] ?? array('count' => 0);
            ?>
                <span class="si-pt-filter-pill" data-cat="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?> (<?php echo esc_html($c['count']); ?>)</span>
            <?php endforeach; ?>
        </div>

        <div id="si-pt-dash-project-list">
            <?php if (!empty($projects)) : foreach ($projects as $project) :
                $status_class = strtolower(str_replace(array(' ', '/'), '', $project['status']));
                $cat_colors = \SI_Project_Tracker\Projects::get_category_colors($project['category']);
            ?>
                <div class="si-pt-project-row" data-category="<?php echo esc_attr($project['category']); ?>">
                    <div class="si-pt-project-row-top">
                        <span class="si-pt-pill" style="background: <?php echo esc_attr($cat_colors['bg']); ?>; color: <?php echo esc_attr($cat_colors['text']); ?>;"><?php echo esc_html($project['category']); ?></span>
                        <span class="si-pt-project-row-title"><?php echo esc_html($project['title']); ?></span>
                        <span class="si-pt-pill si-pt-pill-<?php echo esc_attr($status_class); ?>"><?php echo esc_html($project['status']); ?></span>
                    </div>
                    <div class="si-pt-project-row-desc"><?php echo esc_html(wp_trim_words($project['description'], 20)); ?></div>
                    <div class="si-pt-project-row-meta">
                        <span><?php echo intval($project['note_count'] ?? 0); ?> <?php _e('notes', 'si-project-tracker'); ?></span>
                        <span>&bull;</span>
                        <span><?php echo intval($project['doc_count'] ?? 0); ?> <?php _e('docs', 'si-project-tracker'); ?></span>
                        <button type="button" class="si-pt-btn-icon si-pt-open-add-reminder-for" title="<?php esc_attr_e('Set Reminder', 'si-project-tracker'); ?>" data-project-id="<?php echo esc_attr($project['id']); ?>" style="width:22px; height:22px; padding:0;">&#128276;</button>
                        <span style="margin-left:auto; font-weight:600;"><?php echo esc_html($project['progress']); ?>%</span>
                    </div>
                    <div class="si-pt-hero-progress-bar" style="background:#eef0f4; margin-top:6px;">
                        <div class="si-pt-hero-progress-fill" style="width: <?php echo esc_attr($project['progress']); ?>%; background: linear-gradient(90deg, var(--si-accent), var(--si-purple));"></div>
                    </div>
                </div>
            <?php endforeach; else : ?>
                <div class="si-pt-empty-state"><?php _e('No projects yet. Click "New Project" to add your first one.', 'si-project-tracker'); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="si-pt-panel">
        <div class="si-pt-panel-head">
            <h2>&#128337; <?php _e('Recent Activity Feed', 'si-project-tracker'); ?></h2>
            <span class="si-pt-link"><?php _e('Timeline', 'si-project-tracker'); ?></span>
        </div>
        <?php if (!empty($stats['recent_updates'])) : foreach ($stats['recent_updates'] as $update) : ?>
            <div class="si-pt-activity-item">
                <div class="si-pt-activity-top">
                    <span><?php echo esc_html($update['project_title']); ?></span>
                    <span class="si-pt-activity-date"><?php echo esc_html(mysql2date('n/j/Y', $update['note_date'])); ?></span>
                </div>
                <div class="si-pt-activity-note"><?php echo esc_html(wp_trim_words($update['note'], 22)); ?></div>
                <div class="si-pt-activity-badges">
                    <?php if (!empty($update['status_after'])) : ?>
                        <span class="si-pt-pill si-pt-pill-<?php echo esc_attr(strtolower(str_replace(array(' ', '/'), '', $update['status_after']))); ?>"><?php echo esc_html__('Status:', 'si-project-tracker'); ?> <?php echo esc_html($update['status_after']); ?></span>
                    <?php endif; ?>
                    <?php if ($update['progress_after'] !== null && $update['progress_after'] !== '') : ?>
                        <span class="si-pt-pill si-pt-pill-cat"><?php echo esc_html($update['progress_after']); ?>% <?php _e('Progress', 'si-project-tracker'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; else : ?>
            <div class="si-pt-empty-state"><?php _e('No developer notes logged yet.', 'si-project-tracker'); ?></div>
        <?php endif; ?>
    </div>
</div>

<?php
include SI_PT_PATH . 'views/modern/partials/project-modal.php';
include SI_PT_PATH . 'views/modern/partials/reminder-modal.php';
include SI_PT_PATH . 'views/modern/partials/shell-close.php';
