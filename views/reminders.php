<?php
/**
 * Classic Reminders page
 * Expects: $reminders, $projects, $view_urls
 */
if (!defined('ABSPATH')) {
    exit;
}
$channel_labels = array('email' => 'Email', 'slack' => 'Slack', 'whatsapp' => 'WhatsApp');
$pending = array_filter($reminders, function ($r) { return $r['status'] === 'pending'; });
$sent = array_filter($reminders, function ($r) { return $r['status'] !== 'pending'; });
?>
<div class="wrap si-pt-wrap">
    <div class="si-pt-view-toggle">
        <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="button"><?php _e('Switch to Modern View', 'si-project-tracker'); ?></a>
    </div>
    <h1 class="wp-heading-inline"><?php _e('Project Reminders', 'si-project-tracker'); ?></h1>
    <p><?php _e('Schedule a reminder for any project. It will be delivered via whichever channels are enabled in Settings.', 'si-project-tracker'); ?></p>

    <div class="si-pt-card" style="max-width:600px; margin-bottom:20px;">
        <h3><?php _e('Schedule a New Reminder', 'si-project-tracker'); ?></h3>
        <form id="si-pt-reminder-form">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="si-pt-reminder-project"><?php _e('Project', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="project_id" id="si-pt-reminder-project" required>
                            <option value=""><?php _e('-- Choose Project --', 'si-project-tracker'); ?></option>
                            <?php $preselect_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0; ?>
                            <?php foreach ($projects as $p) : ?>
                                <option value="<?php echo esc_attr($p['id']); ?>" <?php selected($preselect_id, $p['id']); ?>><?php echo esc_html($p['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-reminder-datetime"><?php _e('Remind At', 'si-project-tracker'); ?></label></th>
                    <td><input type="datetime-local" name="remind_at" id="si-pt-reminder-datetime" required></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Send Via', 'si-project-tracker'); ?></th>
                    <td>
                        <label><input type="checkbox" name="channels[]" value="email" checked> <?php _e('Email', 'si-project-tracker'); ?></label>
                        &nbsp;&nbsp;
                        <label><input type="checkbox" name="channels[]" value="slack"> <?php _e('Slack', 'si-project-tracker'); ?></label>
                        &nbsp;&nbsp;
                        <label><input type="checkbox" name="channels[]" value="whatsapp"> <?php _e('WhatsApp', 'si-project-tracker'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-reminder-message"><?php _e('Message (optional)', 'si-project-tracker'); ?></label></th>
                    <td><textarea name="message" id="si-pt-reminder-message" class="large-text" rows="3"></textarea></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Schedule Reminder', 'si-project-tracker'); ?>">
            </p>
        </form>
    </div>

    <h2><?php _e('Upcoming', 'si-project-tracker'); ?> (<?php echo count($pending); ?>)</h2>
    <?php if (!empty($pending)) : ?>
        <table class="si-pt-table">
            <thead>
                <tr>
                    <th><?php _e('Project', 'si-project-tracker'); ?></th>
                    <th><?php _e('Remind At', 'si-project-tracker'); ?></th>
                    <th><?php _e('Channels', 'si-project-tracker'); ?></th>
                    <th><?php _e('Message', 'si-project-tracker'); ?></th>
                    <th><?php _e('Actions', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $reminder) : ?>
                    <tr>
                        <td><?php echo esc_html($reminder['project_title'] ?? '—'); ?></td>
                        <td><?php echo esc_html(mysql2date('M j, Y g:i A', $reminder['remind_at'])); ?></td>
                        <td><?php echo esc_html(implode(', ', array_map(function ($c) use ($channel_labels) { return $channel_labels[$c] ?? $c; }, array_filter(explode(',', $reminder['channels']))))); ?></td>
                        <td><?php echo esc_html(wp_trim_words($reminder['message'], 12)); ?></td>
                        <td><button type="button" class="button si-pt-delete-reminder" data-id="<?php echo esc_attr($reminder['id']); ?>"><?php _e('Delete', 'si-project-tracker'); ?></button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No upcoming reminders.', 'si-project-tracker'); ?></p>
    <?php endif; ?>

    <h2 style="margin-top:24px;"><?php _e('Sent', 'si-project-tracker'); ?> (<?php echo count($sent); ?>)</h2>
    <?php if (!empty($sent)) : ?>
        <table class="si-pt-table">
            <thead>
                <tr>
                    <th><?php _e('Project', 'si-project-tracker'); ?></th>
                    <th><?php _e('Was Due', 'si-project-tracker'); ?></th>
                    <th><?php _e('Sent At', 'si-project-tracker'); ?></th>
                    <th><?php _e('Channels', 'si-project-tracker'); ?></th>
                    <th><?php _e('Actions', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sent as $reminder) : ?>
                    <tr>
                        <td><?php echo esc_html($reminder['project_title'] ?? '—'); ?></td>
                        <td><?php echo esc_html(mysql2date('M j, Y g:i A', $reminder['remind_at'])); ?></td>
                        <td><?php echo !empty($reminder['sent_at']) ? esc_html(mysql2date('M j, Y g:i A', $reminder['sent_at'])) : '—'; ?></td>
                        <td><?php echo esc_html(implode(', ', array_map(function ($c) use ($channel_labels) { return $channel_labels[$c] ?? $c; }, array_filter(explode(',', $reminder['channels']))))); ?></td>
                        <td><button type="button" class="button si-pt-delete-reminder" data-id="<?php echo esc_attr($reminder['id']); ?>"><?php _e('Delete', 'si-project-tracker'); ?></button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No reminders have been sent yet.', 'si-project-tracker'); ?></p>
    <?php endif; ?>
</div>
