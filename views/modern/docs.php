<?php
/**
 * Modern Reference Documents
 * Expects: $projects, $documents, $view_urls
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = 'docs';
$settings = get_option('si_pt_settings', array());
include SI_PT_PATH . 'views/modern/partials/shell-open.php';
?>
<div class="si-pt-panel">
    <div class="si-pt-panel-head">
        <div>
            <h2>&#128220; <?php _e('Reference Documents', 'si-project-tracker'); ?></h2>
            <p style="font-size:12px; color:var(--si-text-muted); margin:2px 0 0;"><?php _e('Store specification PDFs, wireframes, drive links, and project resources.', 'si-project-tracker'); ?></p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="si-pt-btn si-pt-btn-green si-pt-open-doc-modal" data-service="google_drive">&#9729; <?php _e('Upload to Google Drive', 'si-project-tracker'); ?></button>
            <button type="button" class="si-pt-btn si-pt-btn-primary si-pt-open-doc-modal" data-service="onedrive">&#9729; <?php _e('Upload to OneDrive', 'si-project-tracker'); ?></button>
            <button type="button" class="si-pt-btn si-pt-btn-dark si-pt-open-doc-modal" data-service="custom">+ <?php _e('Add Custom File Link', 'si-project-tracker'); ?></button>
        </div>
    </div>

    <div class="si-pt-doc-table-wrap">
    <?php if (!empty($documents)) : ?>
        <table class="si-pt-modern-table">
            <thead>
                <tr>
                    <th><?php _e('Name of the File', 'si-project-tracker'); ?></th>
                    <th><?php _e('Project', 'si-project-tracker'); ?></th>
                    <th><?php _e('Description', 'si-project-tracker'); ?></th>
                    <th><?php _e('Storage Service', 'si-project-tracker'); ?></th>
                    <th><?php _e('Date Uploaded', 'si-project-tracker'); ?></th>
                    <th><?php _e('Actions', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($doc['file_name']); ?></strong></td>
                        <td><?php echo esc_html($doc['project_title']); ?></td>
                        <td><?php echo esc_html(wp_trim_words($doc['description'], 12)); ?></td>
                        <td>
                            <?php if ($doc['service_type'] === 'onedrive') : ?>
                                <span class="si-pt-service-badge"><span class="si-pt-service-dot onedrive"></span> <?php _e('OneDrive', 'si-project-tracker'); ?></span>
                            <?php elseif ($doc['service_type'] === 'google_drive') : ?>
                                <span class="si-pt-service-badge"><span class="si-pt-service-dot gdrive"></span> <?php _e('Google Drive', 'si-project-tracker'); ?></span>
                            <?php else : ?>
                                <span class="si-pt-service-badge"><?php _e('Custom Link', 'si-project-tracker'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(mysql2date('n/j/Y', $doc['created_at'])); ?></td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="<?php echo esc_url($doc['file_url']); ?>" target="_blank" rel="noopener noreferrer" class="si-pt-btn si-pt-btn-outline">&#128065; <?php _e('View File', 'si-project-tracker'); ?></a>
                                <button type="button" class="si-pt-btn-icon danger si-pt-delete-doc" title="<?php esc_attr_e('Delete', 'si-project-tracker'); ?>" data-id="<?php echo esc_attr($doc['id']); ?>">&#128465;</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="si-pt-empty-state"><?php _e('No reference documents attached yet. Use the buttons above to add one.', 'si-project-tracker'); ?></div>
    <?php endif; ?>
    </div>
</div>

<?php include SI_PT_PATH . 'views/modern/partials/doc-modal.php'; ?>
<script>
window.siPtOauthState = {
    googleConnected: <?php echo !empty($settings['google_drive_client_id']) ? 'true' : 'false'; ?>,
    onedriveConnected: <?php echo !empty($settings['onedrive_app_id']) ? 'true' : 'false'; ?>,
    settingsUrl: <?php echo wp_json_encode($view_urls['settings']); ?>
};
</script>
<?php
include SI_PT_PATH . 'views/modern/partials/project-modal.php';
include SI_PT_PATH . 'views/modern/partials/shell-close.php';
