<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap si-pt-wrap">
    <div class="si-pt-view-toggle">
        <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="button"><?php _e('Switch to Modern View', 'si-project-tracker'); ?></a>
    </div>
    <h1><?php _e('SI Project Tracker - Settings', 'si-project-tracker'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('si_pt_settings_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Uninstallation Cleanup', 'si-project-tracker'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="cleanup_on_uninstall" value="1" <?php checked(1, $settings['cleanup_on_uninstall'] ?? 0); ?>>
                        <?php _e('Drop custom database tables (wp_si_projects, wp_si_project_notes, wp_si_project_docs) when plugin is uninstalled.', 'si-project-tracker'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="google_drive_client_id"><?php _e('Google Drive Client ID', 'si-project-tracker'); ?></label></th>
                <td>
                    <input type="text" name="google_drive_client_id" id="google_drive_client_id" class="large-text" value="<?php echo esc_attr($settings['google_drive_client_id'] ?? ''); ?>">
                    <p class="description"><?php _e('Google Drive API Client ID for document picker integration.', 'si-project-tracker'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="onedrive_app_id"><?php _e('OneDrive App ID', 'si-project-tracker'); ?></label></th>
                <td>
                    <input type="text" name="onedrive_app_id" id="onedrive_app_id" class="large-text" value="<?php echo esc_attr($settings['onedrive_app_id'] ?? ''); ?>">
                    <p class="description"><?php _e('Microsoft OneDrive App ID for file integration.', 'si-project-tracker'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="default_status"><?php _e('Default New Project Status', 'si-project-tracker'); ?></label></th>
                <td>
                    <select name="default_status" id="default_status">
                        <?php foreach (\SI_Project_Tracker\Projects::get_statuses() as $status_option) : ?>
                            <option value="<?php echo esc_attr($status_option); ?>" <?php selected($status_option, $settings['default_status'] ?? ''); ?>><?php echo esc_html($status_option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Notifications', 'si-project-tracker'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_notifications" value="1" <?php checked(1, $settings['enable_notifications'] ?? 0); ?>>
                        <?php _e('Enable admin dashboard notice notifications.', 'si-project-tracker'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Email Reminders', 'si-project-tracker'); ?></th>
                <td>
                    <label><input type="checkbox" name="reminder_email_enabled" value="1" <?php checked(1, $settings['reminder_email_enabled'] ?? 0); ?>> <?php _e('Enable', 'si-project-tracker'); ?></label>
                    <p><input type="text" name="reminder_email_address" class="regular-text" value="<?php echo esc_attr($settings['reminder_email_address'] ?? get_option('admin_email')); ?>" placeholder="you@example.com"></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Slack Reminders', 'si-project-tracker'); ?></th>
                <td>
                    <label><input type="checkbox" name="reminder_slack_enabled" value="1" <?php checked(1, $settings['reminder_slack_enabled'] ?? 0); ?>> <?php _e('Enable', 'si-project-tracker'); ?></label>
                    <p><input type="url" name="reminder_slack_webhook" class="large-text" value="<?php echo esc_attr($settings['reminder_slack_webhook'] ?? ''); ?>" placeholder="https://hooks.slack.com/services/..."></p>
                    <p class="description"><?php _e('Create an Incoming Webhook in your Slack workspace and paste its URL here.', 'si-project-tracker'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('WhatsApp Reminders', 'si-project-tracker'); ?></th>
                <td>
                    <label><input type="checkbox" name="reminder_whatsapp_enabled" value="1" <?php checked(1, $settings['reminder_whatsapp_enabled'] ?? 0); ?>> <?php _e('Enable', 'si-project-tracker'); ?></label>
                    <p><input type="url" name="reminder_whatsapp_webhook" class="large-text" value="<?php echo esc_attr($settings['reminder_whatsapp_webhook'] ?? ''); ?>" placeholder="https://graph.facebook.com/v20.0/.../messages"></p>
                    <p><input type="text" name="reminder_whatsapp_token" class="large-text" value="<?php echo esc_attr($settings['reminder_whatsapp_token'] ?? ''); ?>" placeholder="<?php esc_attr_e('Access token', 'si-project-tracker'); ?>"></p>
                    <p class="description"><?php _e('Requires your own WhatsApp Business Cloud API (or similar) account and credentials.', 'si-project-tracker'); ?></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="si_pt_save_settings" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'si-project-tracker'); ?>">
        </p>
    </form>

    <hr>
    <h2><?php _e('Custom Project Categories', 'si-project-tracker'); ?></h2>
    <p class="description"><?php _e('Built-in categories (Website, Plugin, Android App, iOS App, API/Backend, Desktop) are always available. Add your own below.', 'si-project-tracker'); ?></p>
    <div id="si-pt-category-list" style="margin: 10px 0;">
        <?php foreach (\SI_Project_Tracker\Projects::get_custom_categories() as $custom_cat) : ?>
            <span class="si-pt-badge" style="margin-right:6px;">
                <?php echo esc_html($custom_cat); ?>
                <button type="button" class="si-pt-delete-category" data-name="<?php echo esc_attr($custom_cat); ?>" style="border:none; background:none; cursor:pointer; font-weight:700;">&times;</button>
            </span>
        <?php endforeach; ?>
    </div>
    <p>
        <input type="text" id="si-pt-new-category-name" placeholder="<?php esc_attr_e('e.g. Chrome Extension', 'si-project-tracker'); ?>">
        <button type="button" class="button" id="si-pt-add-category-btn"><?php _e('Add Category', 'si-project-tracker'); ?></button>
    </p>
</div>
