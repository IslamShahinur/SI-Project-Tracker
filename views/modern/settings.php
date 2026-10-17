<?php
/**
 * Modern Settings
 * Expects: $settings, $view_urls, $saved_notice (optional)
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = 'settings';
include SI_PT_PATH . 'views/modern/partials/shell-open.php';
?>

<?php if (!empty($saved_notice)) : ?>
    <div class="si-pt-panel" style="border-color:#b7ebc6; background:#f1fbf3;">
        &#9989; <?php echo esc_html($saved_notice); ?>
    </div>
<?php endif; ?>

<div class="si-pt-backup-panel">
    <div style="display:flex; align-items:center; gap:8px;">&#128190; <strong style="font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:#9ea3d6;"><?php _e('Backup & Migration Tools', 'si-project-tracker'); ?></strong></div>
    <h2><?php _e('Export Total Project Data & Import Project Data', 'si-project-tracker'); ?></h2>
    <p><?php _e('Safely back up all WordPress projects, development progress notes, and document links as a JSON file, or migrate data to another site.', 'si-project-tracker'); ?></p>

    <div class="si-pt-backup-two-col">
        <div class="si-pt-backup-card">
            <h3>&#11015; <?php _e('Export Total Project Data', 'si-project-tracker'); ?></h3>
            <p><?php _e('Download a complete backup JSON containing all projects, notes, and documents.', 'si-project-tracker'); ?></p>
            <button type="button" id="si-pt-export-btn" class="si-pt-btn si-pt-btn-green" style="width:100%; justify-content:center;">&#11015; <?php _e('Export Total Data Backup (JSON)', 'si-project-tracker'); ?></button>
        </div>
        <div class="si-pt-backup-card">
            <h3>&#11014; <?php _e('Import Project Data', 'si-project-tracker'); ?></h3>
            <p><?php _e('Restore or merge project data from an existing .json backup file.', 'si-project-tracker'); ?></p>
            <div class="si-pt-backup-dropzone" id="si-pt-import-dropzone">
                <strong><?php _e('Click to select .json backup', 'si-project-tracker'); ?></strong>
                <?php _e('or drag & drop backup file here', 'si-project-tracker'); ?>
            </div>
            <input type="file" id="si-pt-import-file-input" accept="application/json" style="display:none;">
        </div>
    </div>
</div>

<div class="si-pt-warning-panel">
    <h2>&#9888; <?php _e('Uninstallation Database Cleanup', 'si-project-tracker'); ?></h2>
    <p><?php _e('Clean all plugin tables upon deletion? When checked, deleting the SI Project Tracker plugin via WordPress Admin will permanently drop tables', 'si-project-tracker'); ?>
        <code>wp_si_projects</code>, <code>wp_si_project_notes</code>, <?php _e('and', 'si-project-tracker'); ?> <code>wp_si_project_docs</code>.</p>
    <form method="post" action="" id="si-pt-settings-form">
        <?php wp_nonce_field('si_pt_settings_nonce'); ?>
        <label>
            <input type="checkbox" name="cleanup_on_uninstall" value="1" <?php checked(1, $settings['cleanup_on_uninstall'] ?? 0); ?>>
            <?php _e('Delete all projects, notes, and document links when plugin is uninstalled', 'si-project-tracker'); ?>
        </label>

        <div class="si-pt-panel" style="margin-top:16px;">
            <div class="si-pt-panel-head"><h2>&#9729; <?php _e('Google Drive & OneDrive Integration API Settings', 'si-project-tracker'); ?></h2></div>
            <div class="si-pt-form-row">
                <div class="si-pt-form-group">
                    <label for="google_drive_client_id"><?php _e('Google Drive OAuth Client ID', 'si-project-tracker'); ?></label>
                    <input type="text" name="google_drive_client_id" id="google_drive_client_id" value="<?php echo esc_attr($settings['google_drive_client_id'] ?? ''); ?>" placeholder="xxxxxxx.apps.googleusercontent.com">
                    <p class="si-pt-form-hint"><?php _e('Used by the Google Picker API for selecting reference files.', 'si-project-tracker'); ?></p>
                </div>
                <div class="si-pt-form-group">
                    <label for="onedrive_app_id"><?php _e('OneDrive Microsoft App ID', 'si-project-tracker'); ?></label>
                    <input type="text" name="onedrive_app_id" id="onedrive_app_id" value="<?php echo esc_attr($settings['onedrive_app_id'] ?? ''); ?>" placeholder="a1b2c3d4-e5f6-7890-abcd-ef1234567890">
                    <p class="si-pt-form-hint"><?php _e('Used for Microsoft OneDrive file picker integration.', 'si-project-tracker'); ?></p>
                </div>
            </div>
            <p class="si-pt-form-hint">
                <?php _e('Note: entering an ID here does not create a live OAuth connection by itself — you also need to register these apps in the Google Cloud Console / Azure Portal and complete their consent flow. Until then, Reference Documents are saved as share links rather than direct uploads.', 'si-project-tracker'); ?>
            </p>
        </div>

        <div class="si-pt-panel" style="margin-top:16px;">
            <div class="si-pt-panel-head"><h2>&#9881; <?php _e('Plugin General Defaults', 'si-project-tracker'); ?></h2></div>
            <div class="si-pt-form-group">
                <label for="default_status"><?php _e('Default New Project Status', 'si-project-tracker'); ?></label>
                <select name="default_status" id="default_status">
                    <?php foreach (\SI_Project_Tracker\Projects::get_statuses() as $status_option) : ?>
                        <option value="<?php echo esc_attr($status_option); ?>" <?php selected($status_option, $settings['default_status'] ?? ''); ?>><?php echo esc_html($status_option); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="si-pt-toggle-row">
                <label style="font-weight:600; font-size:13px;"><?php _e('Enable Admin Dashboard Notice Notifications', 'si-project-tracker'); ?></label>
                <input type="checkbox" name="enable_notifications" value="1" <?php checked(1, $settings['enable_notifications'] ?? 0); ?>>
            </div>
        </div>

        <div class="si-pt-panel" style="margin-top:16px;">
            <div class="si-pt-panel-head"><h2>&#127991; <?php _e('Custom Project Categories', 'si-project-tracker'); ?></h2></div>
            <p class="si-pt-form-hint" style="margin-top:-6px; margin-bottom:12px;"><?php _e('Built-in categories (Website, Plugin, Android App, iOS App, API/Backend, Desktop) are always available. Add your own below — each gets its own distinct badge color automatically.', 'si-project-tracker'); ?></p>

            <div id="si-pt-category-list" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px;">
                <?php foreach (\SI_Project_Tracker\Projects::get_custom_categories() as $custom_cat) :
                    $cc = \SI_Project_Tracker\Projects::get_category_colors($custom_cat);
                ?>
                    <span class="si-pt-pill" style="background: <?php echo esc_attr($cc['bg']); ?>; color: <?php echo esc_attr($cc['text']); ?>; display:inline-flex; align-items:center; gap:6px; padding: 5px 6px 5px 10px;">
                        <?php echo esc_html($custom_cat); ?>
                        <button type="button" class="si-pt-delete-category" data-name="<?php echo esc_attr($custom_cat); ?>" style="border:none; background:none; cursor:pointer; color:inherit; font-weight:700; padding:0 2px;">&times;</button>
                    </span>
                <?php endforeach; ?>
                <?php if (empty(\SI_Project_Tracker\Projects::get_custom_categories())) : ?>
                    <span style="font-size:12px; color:var(--si-text-muted);"><?php _e('No custom categories yet.', 'si-project-tracker'); ?></span>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:8px;">
                <input type="text" id="si-pt-new-category-name" placeholder="<?php esc_attr_e('e.g. Chrome Extension', 'si-project-tracker'); ?>" style="flex:1; border:1px solid var(--si-border); border-radius:8px; padding:8px 10px; font-size:13px;">
                <button type="button" class="si-pt-btn si-pt-btn-primary" id="si-pt-add-category-btn"><?php _e('Add Category', 'si-project-tracker'); ?></button>
            </div>
        </div>

        <div class="si-pt-panel" style="margin-top:16px;">
            <div class="si-pt-panel-head"><h2>&#128276; <?php _e('Reminder Integrations', 'si-project-tracker'); ?></h2></div>
            <p class="si-pt-form-hint" style="margin-top:-6px; margin-bottom:12px;"><?php _e('Choose how project reminders (set from the Reminders page or any project) get delivered. A reminder is sent through every channel enabled here that it was scheduled on.', 'si-project-tracker'); ?></p>

            <div class="si-pt-toggle-row">
                <label style="font-weight:600; font-size:13px;">&#128231; <?php _e('Email', 'si-project-tracker'); ?></label>
                <input type="checkbox" name="reminder_email_enabled" value="1" <?php checked(1, $settings['reminder_email_enabled'] ?? 0); ?>>
            </div>
            <div class="si-pt-form-group">
                <label for="reminder_email_address"><?php _e('Notification Email Address', 'si-project-tracker'); ?></label>
                <input type="text" name="reminder_email_address" id="reminder_email_address" value="<?php echo esc_attr($settings['reminder_email_address'] ?? get_option('admin_email')); ?>" placeholder="you@example.com">
            </div>

            <div class="si-pt-toggle-row" style="margin-top:10px;">
                <label style="font-weight:600; font-size:13px;">&#128172; <?php _e('Slack', 'si-project-tracker'); ?></label>
                <input type="checkbox" name="reminder_slack_enabled" value="1" <?php checked(1, $settings['reminder_slack_enabled'] ?? 0); ?>>
            </div>
            <div class="si-pt-form-group">
                <label for="reminder_slack_webhook"><?php _e('Slack Incoming Webhook URL', 'si-project-tracker'); ?></label>
                <input type="url" name="reminder_slack_webhook" id="reminder_slack_webhook" value="<?php echo esc_attr($settings['reminder_slack_webhook'] ?? ''); ?>" placeholder="https://hooks.slack.com/services/...">
                <p class="si-pt-form-hint"><?php _e('Create one under your Slack workspace\'s "Incoming Webhooks" app.', 'si-project-tracker'); ?></p>
            </div>

            <div class="si-pt-toggle-row" style="margin-top:10px;">
                <label style="font-weight:600; font-size:13px;">&#128241; <?php _e('WhatsApp', 'si-project-tracker'); ?></label>
                <input type="checkbox" name="reminder_whatsapp_enabled" value="1" <?php checked(1, $settings['reminder_whatsapp_enabled'] ?? 0); ?>>
            </div>
            <div class="si-pt-form-row">
                <div class="si-pt-form-group">
                    <label for="reminder_whatsapp_webhook"><?php _e('WhatsApp API Endpoint URL', 'si-project-tracker'); ?></label>
                    <input type="url" name="reminder_whatsapp_webhook" id="reminder_whatsapp_webhook" value="<?php echo esc_attr($settings['reminder_whatsapp_webhook'] ?? ''); ?>" placeholder="https://graph.facebook.com/v20.0/.../messages">
                </div>
                <div class="si-pt-form-group">
                    <label for="reminder_whatsapp_token"><?php _e('Access Token', 'si-project-tracker'); ?></label>
                    <input type="text" name="reminder_whatsapp_token" id="reminder_whatsapp_token" value="<?php echo esc_attr($settings['reminder_whatsapp_token'] ?? ''); ?>" placeholder="<?php esc_attr_e('Bearer token', 'si-project-tracker'); ?>">
                </div>
            </div>
            <p class="si-pt-form-hint"><?php _e('WhatsApp delivery requires your own WhatsApp Business Cloud API (or similar) account — this plugin cannot send WhatsApp messages without those credentials.', 'si-project-tracker'); ?></p>
        </div>

        <p class="submit" style="margin-top:16px;">
            <input type="submit" name="si_pt_save_settings" class="si-pt-btn si-pt-btn-primary" value="<?php esc_attr_e('Save Settings', 'si-project-tracker'); ?>">
        </p>
    </form>
</div>

<?php
include SI_PT_PATH . 'views/modern/partials/shell-close.php';
