<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="si-pt-project-modal" class="si-pt-modal">
    <div class="si-pt-modal-box">
        <div class="si-pt-modal-box-head">
            <div>
                <h2 id="si-pt-project-modal-title">&#128193; <?php _e('Create New Project', 'si-project-tracker'); ?></h2>
                <p><?php _e('Track a website, plugin, app, or backend service.', 'si-project-tracker'); ?></p>
            </div>
            <button type="button" class="si-pt-modal-close si-pt-modal-cancel">&times;</button>
        </div>

        <form id="si-pt-project-form">
            <input type="hidden" name="id" id="si-pt-project-id" value="">

            <div class="si-pt-form-group">
                <label for="si-pt-title"><?php _e('Project Title *', 'si-project-tracker'); ?></label>
                <input type="text" name="title" id="si-pt-title" placeholder="<?php esc_attr_e('e.g. SI Project Tracker WP Plugin', 'si-project-tracker'); ?>" required>
            </div>

            <div class="si-pt-form-row">
                <div class="si-pt-form-group">
                    <label for="si-pt-category"><?php _e('Category / Type', 'si-project-tracker'); ?></label>
                    <select name="category" id="si-pt-category">
                        <?php foreach (\SI_Project_Tracker\Projects::get_categories() as $cat) : ?>
                            <option value="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="si-pt-form-group">
                    <label for="si-pt-status"><?php _e('Current Status', 'si-project-tracker'); ?></label>
                    <select name="status" id="si-pt-status">
                        <?php foreach (\SI_Project_Tracker\Projects::get_statuses() as $status) : ?>
                            <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="si-pt-form-group">
                <label for="si-pt-progress"><?php _e('Progress Percentage', 'si-project-tracker'); ?> (<span id="si-pt-progress-out">0</span>%)</label>
                <input type="range" name="progress" id="si-pt-progress" min="0" max="100" value="0" oninput="document.getElementById('si-pt-progress-out').textContent = this.value;">
            </div>

            <div class="si-pt-form-group">
                <label for="si-pt-description"><?php _e('Description', 'si-project-tracker'); ?></label>
                <textarea name="description" id="si-pt-description" rows="3" placeholder="<?php esc_attr_e('Detailed project summary, goals, and target features...', 'si-project-tracker'); ?>"></textarea>
            </div>

            <div class="si-pt-form-row">
                <div class="si-pt-form-group">
                    <label for="si-pt-repo_url"><?php _e('Repository URL (Optional)', 'si-project-tracker'); ?></label>
                    <input type="url" name="repo_url" id="si-pt-repo_url" placeholder="https://github.com/...">
                </div>
                <div class="si-pt-form-group">
                    <label for="si-pt-target_date"><?php _e('Target Date', 'si-project-tracker'); ?></label>
                    <input type="date" name="target_date" id="si-pt-target_date">
                </div>
            </div>

            <div class="si-pt-form-group">
                <label for="si-pt-live_url"><?php _e('Live Preview URL (Optional)', 'si-project-tracker'); ?></label>
                <input type="url" name="live_url" id="si-pt-live_url" placeholder="https://example.com">
                <p class="si-pt-form-hint"><?php _e('Want to attach a Google Drive or OneDrive reference document? Save the project first, then attach it from the Reference Documents page.', 'si-project-tracker'); ?></p>
            </div>

            <div class="si-pt-modal-footer">
                <button type="button" class="si-pt-btn si-pt-btn-outline si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
                <button type="submit" class="si-pt-btn si-pt-btn-primary">&#128190; <?php _e('Save Project', 'si-project-tracker'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Add Note Modal (shared) -->
<div id="si-pt-note-modal" class="si-pt-modal">
    <div class="si-pt-modal-box">
        <div class="si-pt-modal-box-head">
            <div>
                <h2>&#128221; <?php _e('Add Developer Note', 'si-project-tracker'); ?></h2>
                <p id="si-pt-note-project-title"></p>
            </div>
            <button type="button" class="si-pt-modal-close si-pt-modal-cancel">&times;</button>
        </div>
        <form id="si-pt-note-form">
            <input type="hidden" name="project_id" id="si-pt-note-project-id" value="">
            <div class="si-pt-form-group">
                <label for="si-pt-note-text"><?php _e('Note', 'si-project-tracker'); ?></label>
                <textarea name="note" id="si-pt-note-text" rows="3" required></textarea>
            </div>
            <div class="si-pt-form-row">
                <div class="si-pt-form-group">
                    <label for="si-pt-note-status"><?php _e('New Status (optional)', 'si-project-tracker'); ?></label>
                    <select name="status_after" id="si-pt-note-status">
                        <option value=""><?php _e('-- Keep current --', 'si-project-tracker'); ?></option>
                        <?php foreach (\SI_Project_Tracker\Projects::get_statuses() as $status) : ?>
                            <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="si-pt-form-group">
                    <label for="si-pt-note-progress"><?php _e('New Progress % (optional)', 'si-project-tracker'); ?></label>
                    <input type="number" name="progress_after" id="si-pt-note-progress" min="0" max="100" placeholder="<?php esc_attr_e('Keep current', 'si-project-tracker'); ?>">
                </div>
            </div>
            <div class="si-pt-modal-footer">
                <button type="button" class="si-pt-btn si-pt-btn-outline si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
                <button type="submit" class="si-pt-btn si-pt-btn-primary"><?php _e('Add Note', 'si-project-tracker'); ?></button>
            </div>
        </form>
    </div>
</div>
