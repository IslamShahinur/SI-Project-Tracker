<?php
/**
 * Modern Reminders
 * Expects: $reminders, $projects, $view_urls
 */
if (!defined('ABSPATH')) {
    exit;
}
$active_nav = 'reminders';
include SI_PT_PATH . 'views/modern/partials/shell-open.php';

$channel_icons = array('email' => '&#128231;', 'slack' => '&#128172;', 'whatsapp' => '&#128241;');
$channel_labels = array('email' => 'Email', 'slack' => 'Slack', 'whatsapp' => 'WhatsApp');

$pending = array_filter($reminders, function ($r) { return $r['status'] === 'pending'; });
$sent = array_filter($reminders, function ($r) { return $r['status'] !== 'pending'; });
?>
<div class="si-pt-panel">
    <div class="si-pt-panel-head">
        <div>
            <h2>&#128276; <?php _e('Project Reminders', 'si-project-tracker'); ?></h2>
            <p style="font-size:12px; color:var(--si-text-muted); margin:2px 0 0;"><?php _e('Schedule a reminder for any project. It will be delivered automatically through whichever channels are enabled in Settings.', 'si-project-tracker'); ?></p>
        </div>
        <button type="button" class="si-pt-btn si-pt-btn-primary" id="si-pt-open-add-reminder">+ <?php _e('Schedule Reminder', 'si-project-tracker'); ?></button>
    </div>

    <h3 style="font-size:13px; margin-bottom:10px;"><?php _e('Upcoming', 'si-project-tracker'); ?> (<?php echo count($pending); ?>)</h3>
    <?php if (!empty($pending)) : ?>
        <div class="si-pt-doc-table-wrap">
            <table class="si-pt-modern-table">
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
                            <td><strong><?php echo esc_html($reminder['project_title'] ?? '—'); ?></strong></td>
                            <td><?php echo esc_html(mysql2date('M j, Y g:i A', $reminder['remind_at'])); ?></td>
                            <td>
                                <?php foreach (array_filter(explode(',', $reminder['channels'])) as $ch) : ?>
                                    <span class="si-pt-service-badge"><?php echo $channel_icons[$ch] ?? ''; ?> <?php echo esc_html($channel_labels[$ch] ?? $ch); ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td><?php echo esc_html(wp_trim_words($reminder['message'], 12)); ?></td>
                            <td>
                                <button type="button" class="si-pt-btn-icon danger si-pt-delete-reminder" title="<?php esc_attr_e('Delete', 'si-project-tracker'); ?>" data-id="<?php echo esc_attr($reminder['id']); ?>">&#128465;</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="si-pt-empty-state"><?php _e('No upcoming reminders. Click "Schedule Reminder" to add one.', 'si-project-tracker'); ?></div>
    <?php endif; ?>

    <h3 style="font-size:13px; margin:20px 0 10px;"><?php _e('Sent', 'si-project-tracker'); ?> (<?php echo count($sent); ?>)</h3>
    <?php if (!empty($sent)) : ?>
        <div class="si-pt-doc-table-wrap">
            <table class="si-pt-modern-table">
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
                            <td>
                                <?php foreach (array_filter(explode(',', $reminder['channels'])) as $ch) : ?>
                                    <span class="si-pt-service-badge"><?php echo $channel_icons[$ch] ?? ''; ?> <?php echo esc_html($channel_labels[$ch] ?? $ch); ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <button type="button" class="si-pt-btn-icon danger si-pt-delete-reminder" title="<?php esc_attr_e('Delete', 'si-project-tracker'); ?>" data-id="<?php echo esc_attr($reminder['id']); ?>">&#128465;</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="si-pt-empty-state"><?php _e('No reminders have been sent yet.', 'si-project-tracker'); ?></div>
    <?php endif; ?>
</div>

<?php include SI_PT_PATH . 'views/modern/partials/reminder-modal.php'; ?>
<?php include SI_PT_PATH . 'views/modern/partials/project-modal.php'; ?>
<?php include SI_PT_PATH . 'views/modern/partials/shell-close.php'; ?>
