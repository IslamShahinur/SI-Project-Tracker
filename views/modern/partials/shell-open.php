<?php
/**
 * Modern view app shell (sidebar + header).
 * Expects: $view_urls (array), $active_nav (string: dashboard|projects|docs|settings)
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = $active_nav ?? 'dashboard';
?>
<div class="si-pt-app">
    <aside class="si-pt-app-sidebar">
        <div class="si-pt-brand">
            <div class="si-pt-brand-icon">W</div>
            <div>
                <div class="si-pt-brand-name"><?php _e('SI Project Tracker', 'si-project-tracker'); ?></div>
                <div class="si-pt-brand-sub"><?php _e('WP Plugin v1.0.0', 'si-project-tracker'); ?></div>
            </div>
        </div>

        <a href="#" class="si-pt-btn-new-project si-pt-open-add-project">
            <span>+</span> <?php _e('New Project', 'si-project-tracker'); ?>
        </a>

        <nav class="si-pt-sidebar-nav">
            <a href="<?php echo esc_url($view_urls['dashboard']); ?>" class="<?php echo $active_nav === 'dashboard' ? 'active' : ''; ?>"><?php _e('Dashboard', 'si-project-tracker'); ?></a>
            <a href="<?php echo esc_url($view_urls['projects']); ?>" class="<?php echo $active_nav === 'projects' ? 'active' : ''; ?>"><?php _e('Projects List', 'si-project-tracker'); ?></a>
            <a href="<?php echo esc_url($view_urls['docs']); ?>" class="<?php echo $active_nav === 'docs' ? 'active' : ''; ?>">
                <span><?php _e('Reference Documents', 'si-project-tracker'); ?></span>
            </a>
            <a href="<?php echo esc_url($view_urls['settings']); ?>" class="<?php echo $active_nav === 'settings' ? 'active' : ''; ?>"><?php _e('Plugin Settings', 'si-project-tracker'); ?></a>
            <a href="<?php echo esc_url($view_urls['reminders']); ?>" class="<?php echo $active_nav === 'reminders' ? 'active' : ''; ?>"><?php _e('Reminders', 'si-project-tracker'); ?></a>
        </nav>

        <div class="si-pt-sidebar-footer">
            <a href="<?php echo esc_url(admin_url()); ?>" class="si-pt-exit-link">&larr; <?php _e('Exit to WordPress Admin', 'si-project-tracker'); ?></a>
        </div>
    </aside>

    <div class="si-pt-app-main">
        <header class="si-pt-app-header">
            <div class="si-pt-app-header-brand">
                <div class="si-pt-brand-icon">W</div>
                <div>
                    <div class="si-pt-app-header-title">
                        <?php _e('SI Project Tracker', 'si-project-tracker'); ?>
                        <span class="si-pt-header-version-badge"><?php _e('WP Plugin v1.0.0', 'si-project-tracker'); ?></span>
                    </div>
                    <div class="si-pt-app-header-sub"><?php _e('Empower your workflow with intelligent project tracking, seamless milestone management, and automated deadline reminders—all built right into your dashboard.', 'si-project-tracker'); ?></div>
                </div>
            </div>
            <div class="si-pt-header-actions">
                <div class="si-pt-view-pill-group">
                    <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="si-pt-view-pill active"><?php _e('Modern View', 'si-project-tracker'); ?></a>
                    <a href="<?php echo esc_url($view_urls['to_classic']); ?>" class="si-pt-view-pill"><?php _e('WP Admin View', 'si-project-tracker'); ?></a>
                </div>
                <a href="#" class="si-pt-btn si-pt-btn-primary si-pt-open-add-project">+ <?php _e('New Project', 'si-project-tracker'); ?></a>
            </div>
        </header>

        <div class="si-pt-app-content">
