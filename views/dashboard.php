<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap si-pt-wrap">
    <div class="si-pt-view-toggle">
        <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="button"><?php _e('Switch to Modern View', 'si-project-tracker'); ?></a>
    </div>
    <h1 class="wp-heading-inline"><?php _e('SI Project Tracker - Dashboard', 'si-project-tracker'); ?></h1>
    <hr class="wp-header-end">

    <div class="si-pt-card-grid">
        <div class="si-pt-card">
            <h3><?php _e('Total Projects', 'si-project-tracker'); ?></h3>
            <div class="si-pt-card-value"><?php echo esc_html($stats['total_projects']); ?></div>
        </div>
        <div class="si-pt-card">
            <h3><?php _e('Active Projects', 'si-project-tracker'); ?></h3>
            <div class="si-pt-card-value" style="color:#0073aa;"><?php echo esc_html($stats['active_projects']); ?></div>
        </div>
        <div class="si-pt-card">
            <h3><?php _e('On Hold', 'si-project-tracker'); ?></h3>
            <div class="si-pt-card-value" style="color:#996800;"><?php echo esc_html($stats['on_hold']); ?></div>
        </div>
        <div class="si-pt-card">
            <h3><?php _e('Completed', 'si-project-tracker'); ?></h3>
            <div class="si-pt-card-value" style="color:#008a20;"><?php echo esc_html($stats['completed']); ?></div>
        </div>
        <div class="si-pt-card">
            <h3><?php _e('Reference Docs', 'si-project-tracker'); ?></h3>
            <div class="si-pt-card-value" style="color:#8224e3;"><?php echo esc_html($stats['total_docs']); ?></div>
        </div>
    </div>

    <div class="si-pt-card" style="margin-bottom: 24px;">
        <h3><?php _e('Overall Average Progress', 'si-project-tracker'); ?> (<?php echo esc_html($stats['avg_progress']); ?>%)</h3>
        <div class="si-pt-progress-bar" style="margin-top: 10px;">
            <div class="si-pt-progress-fill" style="width: <?php echo esc_attr($stats['avg_progress']); ?>%;"></div>
        </div>
    </div>

    <h2><?php _e('Recent Developer Activity', 'si-project-tracker'); ?></h2>
    <?php if (!empty($stats['recent_updates'])) : ?>
        <table class="si-pt-table">
            <thead>
                <tr>
                    <th><?php _e('Project', 'si-project-tracker'); ?></th>
                    <th><?php _e('Note', 'si-project-tracker'); ?></th>
                    <th><?php _e('Status After', 'si-project-tracker'); ?></th>
                    <th><?php _e('Progress', 'si-project-tracker'); ?></th>
                    <th><?php _e('Date', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_updates'] as $note) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($note['project_title']); ?></strong></td>
                        <td><?php echo esc_html($note['note']); ?></td>
                        <td><span class="si-pt-badge si-pt-badge-<?php echo strtolower(str_replace(' ', '', $note['status_after'])); ?>"><?php echo esc_html($note['status_after']); ?></span></td>
                        <td><?php echo esc_html($note['progress_after']); ?>%</td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($note['note_date']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No developer activity notes logged yet.', 'si-project-tracker'); ?></p>
    <?php endif; ?>
</div>
