<?php
/**
 * Redirect Import View
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="import-container" style="max-width: 800px;">
    <h2><?php esc_html_e('Import Redirects from CSV', 'ai-seo-pro'); ?></h2>

    <div class="import-instructions"
        style="background: #e7f5fe; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
        <h3 style="margin-top: 0;"><?php esc_html_e('CSV Format Requirements:', 'ai-seo-pro'); ?></h3>
        <p><?php esc_html_e('Your CSV file should have the following columns:', 'ai-seo-pro'); ?></p>
        <ol>
            <li><strong><?php esc_html_e('Source URL', 'ai-seo-pro'); ?></strong> -
                <?php esc_html_e('The URL to redirect from (required)', 'ai-seo-pro'); ?>
            </li>
            <li><strong><?php esc_html_e('Target URL', 'ai-seo-pro'); ?></strong> -
                <?php esc_html_e('The URL to redirect to (required)', 'ai-seo-pro'); ?>
            </li>
            <li><strong><?php esc_html_e('Type', 'ai-seo-pro'); ?></strong> -
                <?php esc_html_e('Redirect type: 301, 302, 307, 410, or 451 (optional, defaults to 301)', 'ai-seo-pro'); ?>
            </li>
        </ol>
    </div>

    <div class="sample-csv" style="background: #fff; border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
        <h4><?php esc_html_e('Sample CSV Format:', 'ai-seo-pro'); ?></h4>
        <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;">Source URL,Target URL,Type
/old-page,/new-page,301
/removed-page,/,410
/blog/old-post,/blog/new-post,301
/temporary,/temp-location,302</pre>

        <p>
            <a href="<?php echo esc_url(AI_SEO_PRO_PLUGIN_URL . 'admin/sample-redirects.csv'); ?>" class="button"
                download>
                <?php esc_html_e('Download Sample CSV', 'ai-seo-pro'); ?>
            </a>
        </p>
    </div>

    <form method="post" enctype="multipart/form-data" class="import-form"
        style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
        <?php wp_nonce_field('import_redirects', 'ai_seo_redirect_nonce'); ?>
        <input type="hidden" name="action" value="import_csv">

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="csv_file">
                        <?php esc_html_e('Choose CSV File', 'ai-seo-pro'); ?>
                        <span class="required" style="color: #dc3232;">*</span>
                    </label>
                </th>
                <td>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    <p class="description">
                        <?php esc_html_e('Select a CSV file to import. Maximum file size: 2MB', 'ai-seo-pro'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <div class="import-options"
            style="background: #fff4e5; border-left: 4px solid #ffb900; padding: 15px; margin: 20px 0;">
            <h4 style="margin-top: 0;">⚠️ <?php esc_html_e('Important Notes:', 'ai-seo-pro'); ?></h4>
            <ul style="margin: 0;">
                <li><?php esc_html_e('Duplicate source URLs will be skipped', 'ai-seo-pro'); ?></li>
                <li><?php esc_html_e('Invalid redirect types will default to 301', 'ai-seo-pro'); ?></li>
                <li><?php esc_html_e('The first row should contain column headers', 'ai-seo-pro'); ?></li>
                <li><?php esc_html_e('Existing redirects will NOT be overwritten', 'ai-seo-pro'); ?></li>
            </ul>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <?php esc_html_e('Import Redirects', 'ai-seo-pro'); ?>
            </button>
            <a href="?page=<?php echo esc_attr($this->plugin_name); ?>-redirects" class="button button-large">
                <?php esc_html_e('Cancel', 'ai-seo-pro'); ?>
            </a>
        </p>
    </form>

    <div class="existing-redirects-warning"
        style="background: #ffe5e5; border-left: 4px solid #dc3232; padding: 15px; margin: 20px 0;">
        <h4 style="margin-top: 0;">🔴 <?php esc_html_e('Before Importing:', 'ai-seo-pro'); ?></h4>
        <p><?php esc_html_e('It\'s recommended to export your existing redirects as a backup before importing new ones.', 'ai-seo-pro'); ?>
        </p>
        <p>
            <a href="<?php echo esc_url(wp_nonce_url('?page=' . $this->plugin_name . '-redirects&action=export', 'export_redirects')); ?>"
                class="button">
                <?php esc_html_e('Export Current Redirects', 'ai-seo-pro'); ?>
            </a>
        </p>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Validate file before submit
        $('.import-form').on('submit', function (e) {
            var file = $('#csv_file')[0].files[0];

            if (!file) {
                alert('<?php echo esc_js(__('Please select a CSV file to import.', 'ai-seo-pro')); ?>');
                e.preventDefault();
                return false;
            }

            // Check file size (2MB max)
            if (file.size > 2097152) {
                alert('<?php echo esc_js(__('File size exceeds 2MB limit. Please use a smaller file.', 'ai-seo-pro')); ?>');
                e.preventDefault();
                return false;
            }

            // Check file extension
            var fileName = file.name;
            var fileExt = fileName.split('.').pop().toLowerCase();

            if (fileExt !== 'csv') {
                alert('<?php echo esc_js(__('Please select a valid CSV file.', 'ai-seo-pro')); ?>');
                e.preventDefault();
                return false;
            }

            // Confirm import
            if (!confirm('<?php echo esc_js(__('Are you sure you want to import these redirects? This action cannot be undone.', 'ai-seo-pro')); ?>')) {
                e.preventDefault();
                return false;
            }
        });

        // File input styling
        $('#csv_file').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).next('.description').html('Selected: <strong>' + fileName + '</strong>');
            }
        });
    });
</script>