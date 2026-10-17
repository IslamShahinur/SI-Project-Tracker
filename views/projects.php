<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap si-pt-wrap">
    <div class="si-pt-view-toggle">
        <a href="<?php echo esc_url($view_urls['to_modern']); ?>" class="button"><?php _e('Switch to Modern View', 'si-project-tracker'); ?></a>
    </div>
    <h1 class="wp-heading-inline"><?php _e('All Projects', 'si-project-tracker'); ?></h1>
    <button type="button" class="page-title-action" id="si-pt-open-add-project"><?php _e('Add New Project', 'si-project-tracker'); ?></button>
    <hr class="wp-header-end">

    <?php if (!empty($projects)) : ?>
        <table class="si-pt-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th><?php _e('Title', 'si-project-tracker'); ?></th>
                    <th><?php _e('Category', 'si-project-tracker'); ?></th>
                    <th><?php _e('Status', 'si-project-tracker'); ?></th>
                    <th><?php _e('Progress', 'si-project-tracker'); ?></th>
                    <th><?php _e('Links / Repos', 'si-project-tracker'); ?></th>
                    <th><?php _e('Target Date', 'si-project-tracker'); ?></th>
                    <th><?php _e('Actions', 'si-project-tracker'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($project['title']); ?></strong>
                            <div style="font-size: 11px; color: #666; margin-top: 4px;">
                                <?php echo esc_html($project['description']); ?>
                            </div>
                        </td>
                        <td><span class="si-pt-badge" style="background: <?php echo esc_attr(\SI_Project_Tracker\Projects::get_category_colors($project['category'])['bg']); ?>; color: <?php echo esc_attr(\SI_Project_Tracker\Projects::get_category_colors($project['category'])['text']); ?>;"><?php echo esc_html($project['category']); ?></span></td>
                        <td><span class="si-pt-badge si-pt-badge-<?php echo strtolower(str_replace(' ', '', $project['status'])); ?>"><?php echo esc_html($project['status']); ?></span></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="si-pt-progress-bar" style="width: 100px;">
                                    <div class="si-pt-progress-fill" style="width: <?php echo esc_attr($project['progress']); ?>%;"></div>
                                </div>
                                <span><?php echo esc_html($project['progress']); ?>%</span>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($project['repo_url'])) : ?>
                                <a href="<?php echo esc_url($project['repo_url']); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Repo', 'si-project-tracker'); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($project['live_url'])) : ?>
                                | <a href="<?php echo esc_url($project['live_url']); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Live', 'si-project-tracker'); ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo !empty($project['target_date']) ? esc_html($project['target_date']) : '—'; ?></td>
                        <td class="si-pt-actions">
                            <button type="button" class="button si-pt-edit-project"
                                data-id="<?php echo esc_attr($project['id']); ?>"
                                data-title="<?php echo esc_attr($project['title']); ?>"
                                data-category="<?php echo esc_attr($project['category']); ?>"
                                data-status="<?php echo esc_attr($project['status']); ?>"
                                data-progress="<?php echo esc_attr($project['progress']); ?>"
                                data-description="<?php echo esc_attr($project['description']); ?>"
                                data-repo-url="<?php echo esc_attr($project['repo_url']); ?>"
                                data-live-url="<?php echo esc_attr($project['live_url']); ?>"
                                data-target-date="<?php echo esc_attr($project['target_date']); ?>"
                            ><?php _e('Edit', 'si-project-tracker'); ?></button>
                            <button type="button" class="button si-pt-add-note"
                                data-id="<?php echo esc_attr($project['id']); ?>"
                                data-title="<?php echo esc_attr($project['title']); ?>"
                            ><?php _e('Add Note', 'si-project-tracker'); ?></button>
                            <a href="<?php echo esc_url(add_query_arg('project_id', $project['id'], $view_urls['reminders'])); ?>" class="button" title="<?php esc_attr_e('Set Reminder', 'si-project-tracker'); ?>"><?php _e('Remind', 'si-project-tracker'); ?></a>
                            <button type="button" class="button si-pt-delete-project"
                                data-id="<?php echo esc_attr($project['id']); ?>"
                            ><?php _e('Delete', 'si-project-tracker'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="si-pt-card" style="margin-top: 15px;">
            <p><?php _e('No projects found.', 'si-project-tracker'); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Add / Edit Project Modal -->
<div id="si-pt-project-modal" class="si-pt-modal" style="display:none;">
    <div class="si-pt-modal-content">
        <h2 id="si-pt-project-modal-title"><?php _e('Add New Project', 'si-project-tracker'); ?></h2>
        <form id="si-pt-project-form">
            <input type="hidden" name="id" id="si-pt-project-id" value="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="si-pt-title"><?php _e('Title', 'si-project-tracker'); ?></label></th>
                    <td><input type="text" name="title" id="si-pt-title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-category"><?php _e('Category', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="category" id="si-pt-category">
                            <option value="Website"><?php _e('Website', 'si-project-tracker'); ?></option>
                            <option value="Plugin"><?php _e('Plugin', 'si-project-tracker'); ?></option>
                            <option value="Android App"><?php _e('Android App', 'si-project-tracker'); ?></option>
                            <option value="iOS App"><?php _e('iOS App', 'si-project-tracker'); ?></option>
                            <option value="API/Backend"><?php _e('API/Backend', 'si-project-tracker'); ?></option>
                            <option value="Desktop"><?php _e('Desktop', 'si-project-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-status"><?php _e('Status', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="status" id="si-pt-status">
                            <option value="Planning"><?php _e('Planning', 'si-project-tracker'); ?></option>
                            <option value="Active"><?php _e('Active', 'si-project-tracker'); ?></option>
                            <option value="On Hold"><?php _e('On Hold', 'si-project-tracker'); ?></option>
                            <option value="Completed"><?php _e('Completed', 'si-project-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-progress"><?php _e('Progress %', 'si-project-tracker'); ?></label></th>
                    <td><input type="number" name="progress" id="si-pt-progress" min="0" max="100" value="0"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-description"><?php _e('Description', 'si-project-tracker'); ?></label></th>
                    <td><textarea name="description" id="si-pt-description" class="large-text" rows="3"></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-repo_url"><?php _e('Repo URL', 'si-project-tracker'); ?></label></th>
                    <td><input type="url" name="repo_url" id="si-pt-repo_url" class="large-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-live_url"><?php _e('Live URL', 'si-project-tracker'); ?></label></th>
                    <td><input type="url" name="live_url" id="si-pt-live_url" class="large-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-target_date"><?php _e('Target Date', 'si-project-tracker'); ?></label></th>
                    <td><input type="date" name="target_date" id="si-pt-target_date"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Project', 'si-project-tracker'); ?>">
                <button type="button" class="button si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
            </p>
        </form>
    </div>
</div>

<!-- Add Note Modal -->
<div id="si-pt-note-modal" class="si-pt-modal" style="display:none;">
    <div class="si-pt-modal-content">
        <h2><?php _e('Add Developer Note', 'si-project-tracker'); ?> — <span id="si-pt-note-project-title"></span></h2>
        <form id="si-pt-note-form">
            <input type="hidden" name="project_id" id="si-pt-note-project-id" value="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="si-pt-note-text"><?php _e('Note', 'si-project-tracker'); ?></label></th>
                    <td><textarea name="note" id="si-pt-note-text" class="large-text" rows="3" required></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-note-status"><?php _e('New Status (optional)', 'si-project-tracker'); ?></label></th>
                    <td>
                        <select name="status_after" id="si-pt-note-status">
                            <option value=""><?php _e('-- Keep current status --', 'si-project-tracker'); ?></option>
                            <option value="Planning"><?php _e('Planning', 'si-project-tracker'); ?></option>
                            <option value="Active"><?php _e('Active', 'si-project-tracker'); ?></option>
                            <option value="On Hold"><?php _e('On Hold', 'si-project-tracker'); ?></option>
                            <option value="Completed"><?php _e('Completed', 'si-project-tracker'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="si-pt-note-progress"><?php _e('New Progress % (optional)', 'si-project-tracker'); ?></label></th>
                    <td><input type="number" name="progress_after" id="si-pt-note-progress" min="0" max="100" placeholder="<?php esc_attr_e('Keep current', 'si-project-tracker'); ?>"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e('Add Note', 'si-project-tracker'); ?>">
                <button type="button" class="button si-pt-modal-cancel"><?php _e('Cancel', 'si-project-tracker'); ?></button>
            </p>
        </form>
    </div>
</div>
