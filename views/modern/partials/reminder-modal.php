<?php
if (!defined('ABSPATH')) {
    exit;
}
$reminder_projects = $projects ?? array();
?>
<div id="si-pt-reminder-modal" class="si-pt-modal">
    <div class="si-pt-modal-box">
        <div class="si-pt-modal-box-head">
            <div>
                <h2>&#128276; <?php _e('Schedule Reminder', 'si-project-tracker'); ?></h2>
                <p><?php _e('Delivered automatically via the channels you pick below, once enabled in Settings.', 'si-project-tracker'); ?></p>
            </div>
            <button type="button" class="si-pt-modal-close si-pt-modal-cancel">&times;</button>
        </div>

        <form id="si-pt-reminder-form">
            <div class="si-pt-form-group">
                <label for="si-pt-reminder-project"><?php _e('Project', 'si-project-tracker'); ?></label>
                <select name="project_id" id="si-pt-reminder-project" required>
                    <option value=""><?php _e('-- Choose Project --', 'si-project-tracker'); ?></option>
                    <?php foreach ($reminder_projects as $p) : ?>
                        <option value="<?php echo esc_attr($p['id']); ?>"><?php echo esc_html($p['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="si-pt-form-group">
                <label for="si-pt-reminder-datetime"><?php _e('Remind At', 'si-project-tracker'); ?></label>
                <input type="datetime-local" name="remind_at" id="si-pt-reminder-datetime" required>
            </div>

            <div class="si-pt-form-group">
                <label><?php _e('Send Via', 'si-project-tracker'); ?></label>
                <div style="display:flex; gap:16px; margin-top:4px;">
                    <label style="font-weight:400; display:flex; align-items:center; gap:6px;"><input type="checkbox" name="channels[]" value="email" checked> &#128231; <?php _e('Email', 'si-project-tracker'); ?></label>
                    <label style="font-weight:400; display:flex; align-items:center; gap:6px;"><input type="checkbox" name="channels[]" value="slack"> &#128172; <?php _e('Slack', 'si-project-tracker'); ?></label>
                    <label style="font-weight:400; display:flex; align-items:center; gap:6px;"><input type="checkbox" name="channels[]" value="whatsapp"> &#128241; <?php _e('WhatsApp', 'si-project-tracker'); ?></label>
                </div>
                <p class="si-pt-form-hint"><?php _e('Only channels enabled in Plugin Settings will actually be delivered.', 'si-project-tracker'); ?></p>
            </div>

            <div class="si-pt-form-group">
                <label for="si-pt-reminder-message"><?php _e('Message (optional)', 'si-project-tracker'); ?></label>
                <textarea name="message" id="si-pt-reminder-message" rows="3" placeholder="<?php esc_attr_e('e.g. Client demo tomorrow — confirm staging build is deployed.', 'si-project-tracker'); ?>"></textarea>
            </div>

            <div class="si-pt-modal-footer">
                <button type="button" class="si-pt-btn si-pt-btn-outline si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
                <button type="submit" class="si-pt-btn si-pt-btn-primary"><?php _e('Schedule Reminder', 'si-project-tracker'); ?></button>
            </div>
        </form>
    </div>
</div>
