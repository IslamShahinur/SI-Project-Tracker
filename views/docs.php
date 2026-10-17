<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap si-pt-wrap">
    <div class="si-pt-view-toggle">
        <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="button"><?php _e('Switch to Modern View', 'si-project-tracker'); ?></a>
    </div>
    <h1 class="wp-heading-inline"><?php _e('Reference Documents (Google Drive & OneDrive)', 'si-project-tracker'); ?></h1>
    <hr class="wp-header-end">

    <div class="si-pt-card" style="margin-top: 15px; margin-bottom: 20px;">
        <h3><?php _e('Attach New Reference Document', 'si-project-tracker'); ?></h3>
        <form id="si-pt-add-doc-form" style="margin-top: 10px;">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="project_id"><?php _e('Select Project', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="project_id" id="project_id" required>
                            <option value=""><?php _e('-- Choose Project --', 'si-project-tracker'); ?></option>
                            <?php foreach ($projects as $p) : ?>
                                <option value="<?php echo esc_attr($p['id']); ?>"><?php echo esc_html($p['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="file_name"><?php _e('File Name', 'si-project-tracker'); ?></label></th>
                    <td><input type="text" name="file_name" id="file_name" class="regular-text" required placeholder="e.g. Architecture_Spec.pdf"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="service_type"><?php _e('Cloud Provider', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="service_type" id="service_type">
                            <option value="google_drive"><?php _e('Google Drive', 'si-project-tracker'); ?></option>
                            <option value="onedrive"><?php _e('Microsoft OneDrive', 'si-project-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="file_url"><?php _e('Cloud Document Link URL', 'si-project-tracker'); ?></label></th>
                    <td><input type="url" name="file_url" id="file_url" class="large-text" required placeholder="https://drive.google.com/... or https://1drv.ms/..."></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description"><?php _e('Description', 'si-project-tracker'); ?></label></th>
                    <td><textarea name="description" id="description" class="large-text" rows="2"></textarea></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Attach Reference Document', 'si-project-tracker'); ?>">
            </p>
        </form>
    </div>

    <h2><?php _e('Attached Documents', 'si-project-tracker'); ?></h2>
    <?php if (!empty($documents)) : ?>
        <table class="si-pt-table">
            <thead>
                <tr>
                    <th><?php _e('Project', 'si-project-tracker'); ?></th>
                    <th><?php _e('File Name', 'si-project-tracker'); ?></th>
                    <th><?php _e('Provider', 'si-project-tracker'); ?></th>
                    <th><?php _e('Description', 'si-project-tracker'); ?></th>
                    <th><?php _e('Link', 'si-project-tracker'); ?></th>
                    <th><?php _e('Actions', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc) : ?>
                    <tr>
                        <td><?php echo esc_html($doc['project_title']); ?></td>
                        <td><?php echo esc_html($doc['file_name']); ?></td>
                        <td><span class="si-pt-badge"><?php echo $doc['service_type'] === 'onedrive' ? esc_html__('OneDrive', 'si-project-tracker') : esc_html__('Google Drive', 'si-project-tracker'); ?></span></td>
                        <td><?php echo esc_html($doc['description']); ?></td>
                        <td><a href="<?php echo esc_url($doc['file_url']); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Open', 'si-project-tracker'); ?></a></td>
                        <td>
                            <button type="button" class="button si-pt-delete-doc" data-id="<?php echo esc_attr($doc['id']); ?>"><?php _e('Delete', 'si-project-tracker'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No reference documents attached yet.', 'si-project-tracker'); ?></p>
    <?php endif; ?>
</div>
