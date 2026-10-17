<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="si-pt-doc-modal" class="si-pt-modal">
    <div class="si-pt-modal-box">
        <div class="si-pt-modal-box-head">
            <div>
                <h2 id="si-pt-doc-modal-title">&#9729; <?php _e('Google Drive File Picker', 'si-project-tracker'); ?></h2>
                <p id="si-pt-doc-modal-sub"><?php _e('Select from Google Drive or attach a shareable link', 'si-project-tracker'); ?></p>
            </div>
            <button type="button" class="si-pt-modal-close si-pt-modal-cancel">&times;</button>
        </div>

        <div class="si-pt-tabs">
            <span class="si-pt-tab active" id="si-pt-doc-tab-local"><?php _e('Select Local File from Desktop', 'si-project-tracker'); ?></span>
            <span class="si-pt-tab" id="si-pt-doc-tab-presets"><?php _e('Paste Existing Share Link', 'si-project-tracker'); ?></span>
        </div>

        <form id="si-pt-add-doc-form">
            <input type="hidden" name="service_type" id="si-pt-doc-service_type" value="google_drive">

            <div class="si-pt-form-group">
                <label for="project_id"><?php _e('Select Project', 'si-project-tracker'); ?></label>
                <select name="project_id" id="project_id" required>
                    <option value=""><?php _e('-- Choose Project --', 'si-project-tracker'); ?></option>
                    <?php foreach ($projects as $p) : ?>
                        <option value="<?php echo esc_attr($p['id']); ?>"><?php echo esc_html($p['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="si-pt-doc-local-panel">
                <div class="si-pt-dropzone" id="si-pt-doc-dropzone">
                    <strong><?php _e('Desktop File Upload', 'si-project-tracker'); ?></strong>
                    <?php _e('Click to choose a local file or drag & drop here', 'si-project-tracker'); ?><br>
                    <?php _e('PDF, DOCX, ZIP, JSON, Images, or Code files (used to auto-fill the file name below)', 'si-project-tracker'); ?>
                    <input type="file" id="si-pt-doc-file-input" style="display:none;">
                </div>
            </div>

            <div class="si-pt-form-group">
                <label for="file_name"><?php _e('File Name *', 'si-project-tracker'); ?></label>
                <input type="text" name="file_name" id="file_name" placeholder="<?php esc_attr_e('e.g. My_Desktop_Document.pdf', 'si-project-tracker'); ?>" required>
            </div>

            <div class="si-pt-form-group">
                <label for="description"><?php _e('Description', 'si-project-tracker'); ?></label>
                <textarea name="description" id="description" rows="2" placeholder="<?php esc_attr_e('Document summary or reference details...', 'si-project-tracker'); ?>"></textarea>
            </div>

            <div class="si-pt-form-group">
                <label for="file_url" id="si-pt-doc-url-label"><?php _e('Google Drive Link URL', 'si-project-tracker'); ?></label>
                <input type="url" name="file_url" id="file_url" placeholder="https://drive.google.com/file/d/..." required>
                <p class="si-pt-form-hint" id="si-pt-doc-oauth-hint"></p>
            </div>

            <div class="si-pt-modal-footer" style="justify-content: space-between; align-items: center;">
                <div class="si-pt-oauth-status" id="si-pt-doc-oauth-status">
                    <span class="si-pt-oauth-dot"></span> <span id="si-pt-doc-oauth-text"></span>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="si-pt-btn si-pt-btn-outline si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
                    <button type="submit" class="si-pt-btn si-pt-btn-green" id="si-pt-doc-submit-btn"><?php _e('Confirm & Save Reference', 'si-project-tracker'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
