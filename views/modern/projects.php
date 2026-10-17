<?php
/**
 * Modern Projects List
 * Expects: $projects, $view_urls
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = 'projects';
include SI_PT_PATH . 'views/modern/partials/shell-open.php';
?>
<div class="si-pt-panel">
    <div class="si-pt-panel-head">
        <div>
            <h2>&#128203; <?php _e('Developer Projects', 'si-project-tracker'); ?></h2>
            <p style="font-size:12px; color:var(--si-text-muted); margin:2px 0 0;"><?php _e('Manage your development projects, update progress %, log notes, and attach reference docs.', 'si-project-tracker'); ?></p>
        </div>
        <a href="#" class="si-pt-btn si-pt-btn-primary si-pt-open-add-project">+ <?php _e('New Project', 'si-project-tracker'); ?></a>
    </div>

    <div class="si-pt-toolbar">
        <div class="si-pt-search">
            <input type="text" id="si-pt-project-search" placeholder="<?php esc_attr_e('Search projects by name or description...', 'si-project-tracker'); ?>">
        </div>
        <select id="si-pt-filter-category">
            <option value=""><?php _e('All Categories', 'si-project-tracker'); ?></option>
            <?php foreach (\SI_Project_Tracker\Projects::get_categories() as $cat) : ?>
                <option value="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <select id="si-pt-filter-status">
            <option value=""><?php _e('All Statuses', 'si-project-tracker'); ?></option>
            <?php foreach (\SI_Project_Tracker\Projects::get_statuses() as $status) : ?>
                <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="si-pt-card-grid-2" id="si-pt-project-card-grid">
        <?php if (!empty($projects)) : foreach ($projects as $project) :
            $status_class = strtolower(str_replace(array(' ', '/'), '', $project['status']));
            $cat_colors = \SI_Project_Tracker\Projects::get_category_colors($project['category']);
        ?>
            <div class="si-pt-proj-card"
                data-title="<?php echo esc_attr(strtolower($project['title'] . ' ' . $project['description'])); ?>"
                data-category="<?php echo esc_attr($project['category']); ?>"
                data-status="<?php echo esc_attr($project['status']); ?>"
            >
                <div class="si-pt-proj-card-top">
                    <span class="si-pt-pill" style="background: <?php echo esc_attr($cat_colors['bg']); ?>; color: <?php echo esc_attr($cat_colors['text']); ?>;"><?php echo esc_html($project['category']); ?></span>
                    <span class="si-pt-pill si-pt-pill-<?php echo esc_attr($status_class); ?>"><?php echo esc_html($project['status']); ?></span>
                </div>
                <h3><?php echo esc_html($project['title']); ?></h3>
                <div class="si-pt-proj-card-desc"><?php echo esc_html(wp_trim_words($project['description'], 22)); ?></div>
                <div class="si-pt-proj-card-links">
                    <?php if (!empty($project['repo_url'])) : ?>
                        <a href="<?php echo esc_url($project['repo_url']); ?>" target="_blank" rel="noopener noreferrer">&#128279; <?php _e('Repository', 'si-project-tracker'); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($project['live_url'])) : ?>
                        <a href="<?php echo esc_url($project['live_url']); ?>" target="_blank" rel="noopener noreferrer">&#128279; <?php _e('Live Preview', 'si-project-tracker'); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($project['target_date'])) : ?>
                        <span>&#128197; <?php _e('Target:', 'si-project-tracker'); ?> <?php echo esc_html($project['target_date']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="si-pt-proj-card-progress-row">
                    <span><?php _e('Completion Progress', 'si-project-tracker'); ?></span>
                    <span><strong><?php echo esc_html($project['progress']); ?>%</strong></span>
                </div>
                <div class="si-pt-hero-progress-bar" style="background:#eef0f4;">
                    <div class="si-pt-hero-progress-fill" style="width: <?php echo esc_attr($project['progress']); ?>%; background: linear-gradient(90deg, var(--si-accent), var(--si-purple));"></div>
                </div>
                <div class="si-pt-proj-card-bottom">
                    <div class="si-pt-proj-card-meta">
                        <span>&#128221; <?php echo intval($project['note_count'] ?? 0); ?> <?php _e('notes', 'si-project-tracker'); ?></span>
                        <span>&#128196; <?php echo intval($project['doc_count'] ?? 0); ?> <?php _e('docs', 'si-project-tracker'); ?></span>
                    </div>
                    <div class="si-pt-proj-card-actions">
                        <button type="button" class="si-pt-btn si-pt-btn-outline si-pt-add-note"
                            title="<?php esc_attr_e('View & add developer notes', 'si-project-tracker'); ?>"
                            data-id="<?php echo esc_attr($project['id']); ?>"
                            data-title="<?php echo esc_attr($project['title']); ?>"
                        ><?php _e('Open', 'si-project-tracker'); ?></button>
                        <button type="button" class="si-pt-btn-icon si-pt-edit-project"
                            title="<?php esc_attr_e('Edit', 'si-project-tracker'); ?>"
                            data-id="<?php echo esc_attr($project['id']); ?>"
                            data-title="<?php echo esc_attr($project['title']); ?>"
                            data-category="<?php echo esc_attr($project['category']); ?>"
                            data-status="<?php echo esc_attr($project['status']); ?>"
                            data-progress="<?php echo esc_attr($project['progress']); ?>"
                            data-description="<?php echo esc_attr($project['description']); ?>"
                            data-repo-url="<?php echo esc_attr($project['repo_url']); ?>"
                            data-live-url="<?php echo esc_attr($project['live_url']); ?>"
                            data-target-date="<?php echo esc_attr($project['target_date']); ?>"
                        >&#9998;</button>
                        <button type="button" class="si-pt-btn-icon si-pt-open-add-reminder-for"
                            title="<?php esc_attr_e('Set Reminder', 'si-project-tracker'); ?>"
                            data-project-id="<?php echo esc_attr($project['id']); ?>"
                        >&#128276;</button>
                        <button type="button" class="si-pt-btn-icon danger si-pt-delete-project"
                            title="<?php esc_attr_e('Delete', 'si-project-tracker'); ?>"
                            data-id="<?php echo esc_attr($project['id']); ?>"
                        >&#128465;</button>
                    </div>
                </div>
            </div>
        <?php endforeach; else : ?>
            <div class="si-pt-empty-state"><?php _e('No projects yet. Click "New Project" to add your first one.', 'si-project-tracker'); ?></div>
        <?php endif; ?>
    </div>
    <div class="si-pt-empty-state" id="si-pt-project-no-results" style="display:none;"><?php _e('No projects match your search or filters.', 'si-project-tracker'); ?></div>
</div>

<?php
include SI_PT_PATH . 'views/modern/partials/project-modal.php';
include SI_PT_PATH . 'views/modern/partials/reminder-modal.php';
include SI_PT_PATH . 'views/modern/partials/shell-close.php';
