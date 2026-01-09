<?php
/**
 * Redirect Form View
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 *
 * @var object $ai_seo_pro_redirect Redirect object.
 */

if (!defined('ABSPATH')) {
    exit;
}

$ai_seo_pro_is_edit = isset($ai_seo_pro_redirect->id);
$ai_seo_pro_page_title = $ai_seo_pro_is_edit ? __('Edit Redirect', 'ai-seo-pro') : __('Add New Redirect', 'ai-seo-pro');
?>

<div class="redirect-form-container" style="max-width: 800px;">
    <h2><?php echo esc_html($ai_seo_pro_page_title); ?></h2>

    <form method="post" action="" class="redirect-form">
        <?php wp_nonce_field('ai_seo_redirect_save', 'ai_seo_redirect_nonce'); ?>
        <input type="hidden" name="action" value="save_redirect">
        <?php if ($ai_seo_pro_is_edit): ?>
            <input type="hidden" name="redirect_id" value="<?php echo esc_attr($ai_seo_pro_redirect->id); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="source_url">
                        <?php esc_html_e('Source URL', 'ai-seo-pro'); ?>
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" id="source_url" name="source_url"
                        value="<?php echo esc_attr($ai_seo_pro_redirect->source_url); ?>" class="large-text" required
                        placeholder="/old-page">
                    <p class="description">
                        <?php esc_html_e('The URL to redirect FROM. Use relative URLs starting with / (e.g., /old-page)', 'ai-seo-pro'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="target_url">
                        <?php esc_html_e('Target URL', 'ai-seo-pro'); ?>
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <input type="text" id="target_url" name="target_url"
                        value="<?php echo esc_attr($ai_seo_pro_redirect->target_url); ?>" class="large-text" required
                        placeholder="/new-page">
                    <p class="description">
                        <?php esc_html_e('The URL to redirect TO. Can be relative (/new-page) or absolute (https://example.com)', 'ai-seo-pro'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="redirect_type"><?php esc_html_e('Redirect Type', 'ai-seo-pro'); ?></label>
                </th>
                <td>
                    <select id="redirect_type" name="redirect_type" class="regular-text">
                        <option value="301" <?php selected($ai_seo_pro_redirect->redirect_type, '301'); ?>>
                            301 - <?php esc_html_e('Permanent', 'ai-seo-pro'); ?>
                        </option>
                        <option value="302" <?php selected($ai_seo_pro_redirect->redirect_type, '302'); ?>>
                            302 - <?php esc_html_e('Temporary', 'ai-seo-pro'); ?>
                        </option>
                        <option value="307" <?php selected($ai_seo_pro_redirect->redirect_type, '307'); ?>>
                            307 - <?php esc_html_e('Temporary (Preserve Method)', 'ai-seo-pro'); ?>
                        </option>
                        <option value="410" <?php selected($ai_seo_pro_redirect->redirect_type, '410'); ?>>
                            410 - <?php esc_html_e('Gone', 'ai-seo-pro'); ?>
                        </option>
                        <option value="451" <?php selected($ai_seo_pro_redirect->redirect_type, '451'); ?>>
                            451 - <?php esc_html_e('Unavailable For Legal Reasons', 'ai-seo-pro'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('301 is recommended for SEO. Use 302/307 for temporary redirects.', 'ai-seo-pro'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Options', 'ai-seo-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="is_regex" value="1" <?php checked($ai_seo_pro_redirect->is_regex, 1); ?>>
                            <?php esc_html_e('Enable Regular Expression (Regex)', 'ai-seo-pro'); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e('Use regex patterns for advanced URL matching. Example: /category/(.*) → /new-category/$1', 'ai-seo-pro'); ?>
                        </p>
                    </fieldset>

                    <fieldset style="margin-top: 10px;">
                        <label>
                            <input type="checkbox" name="is_active" value="1" <?php checked($ai_seo_pro_redirect->is_active, 1); ?>>
                            <?php esc_html_e('Active', 'ai-seo-pro'); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e('Uncheck to temporarily disable this redirect without deleting it.', 'ai-seo-pro'); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
        </table>

        <div class="redirect-examples"
            style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
            <h4 style="margin-top: 0;"><?php esc_html_e('Examples:', 'ai-seo-pro'); ?></h4>
            <ul style="margin: 0;">
                <li><strong><?php esc_html_e('Simple:', 'ai-seo-pro'); ?></strong> /old-page → /new-page</li>
                <li><strong><?php esc_html_e('To Homepage:', 'ai-seo-pro'); ?></strong> /removed-page → /</li>
                <li><strong><?php esc_html_e('External:', 'ai-seo-pro'); ?></strong> /blog → https://blog.example.com
                </li>
                <li><strong><?php esc_html_e('Regex:', 'ai-seo-pro'); ?></strong> /category/(.*) → /new-category/$1
                </li>
            </ul>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <?php echo $ai_seo_pro_is_edit ? esc_html__('Update Redirect', 'ai-seo-pro') : esc_html__('Add Redirect', 'ai-seo-pro'); ?>
            </button>
            <a href="?page=<?php echo esc_attr($this->plugin_name); ?>-redirects" class="button button-large">
                <?php esc_html_e('Cancel', 'ai-seo-pro'); ?>
            </a>
        </p>
    </form>
</div>

<style>
    .redirect-form-container {
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 20px;
    }

    .required {
        color: #dc3232;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Show/hide target URL based on redirect type.
        $('#redirect_type').on('change', function () {
            var type = $(this).val();
            if (type === '410' || type === '451') {
                $('#target_url').closest('tr').hide();
            } else {
                $('#target_url').closest('tr').show();
            }
        }).trigger('change');

        // Validate form.
        $('.redirect-form').on('submit', function (e) {
            var sourceUrl = $('#source_url').val();
            var targetUrl = $('#target_url').val();
            var redirectType = $('#redirect_type').val();

            // Validate source URL.
            if (!sourceUrl || sourceUrl.trim() === '') {
                alert('<?php echo esc_js(__('Please enter a source URL.', 'ai-seo-pro')); ?>');
                e.preventDefault();
                return false;
            }

            // Validate target URL (except for 410/451).
            if (redirectType !== '410' && redirectType !== '451') {
                if (!targetUrl || targetUrl.trim() === '') {
                    alert('<?php echo esc_js(__('Please enter a target URL.', 'ai-seo-pro')); ?>');
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>