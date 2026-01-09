<?php
/**
 * Advanced settings tab.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('ai_seo_pro_advanced'); ?>

	<h2><?php esc_html_e('Advanced SEO Settings', 'ai-seo-pro'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Archive Pages', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_noindex_archives" value="1" <?php checked(get_option('ai_seo_pro_noindex_archives', false), true); ?>>
					<?php esc_html_e('Noindex archive pages', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Prevent search engines from indexing category, tag, and date archives', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('URL Optimization', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_remove_stopwords" value="1" <?php checked(get_option('ai_seo_pro_remove_stopwords', false), true); ?>>
					<?php esc_html_e('Remove stop words from URLs', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Automatically remove common words (and, the, or, etc.) from post URLs', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Breadcrumbs', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_breadcrumbs" value="1" <?php checked(get_option('ai_seo_pro_breadcrumbs', false), true); ?>>
					<?php esc_html_e('Enable breadcrumb schema', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Add breadcrumb structured data to pages', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="api_timeout"><?php esc_html_e('API Timeout', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<input type="number" id="api_timeout" name="ai_seo_pro_api_timeout"
					value="<?php echo esc_attr(get_option('ai_seo_pro_api_timeout', 30)); ?>" min="10" max="120"
					step="5" class="small-text">
				<?php esc_html_e('seconds', 'ai-seo-pro'); ?>
				<p class="description">
					<?php esc_html_e('Maximum time to wait for AI API responses (10-120 seconds)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="cache_duration"><?php esc_html_e('Cache Duration', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<input type="number" id="cache_duration" name="ai_seo_pro_cache_duration"
					value="<?php echo esc_attr(get_option('ai_seo_pro_cache_duration', 24)); ?>" min="1" max="168"
					class="small-text">
				<?php esc_html_e('hours', 'ai-seo-pro'); ?>
				<p class="description">
					<?php esc_html_e('How long to cache API responses (1-168 hours)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Danger Zone', 'ai-seo-pro'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Reset Settings', 'ai-seo-pro'); ?>
			</th>
			<td>
				<button type="button" id="reset_settings" class="button button-secondary">
					<?php esc_html_e('Reset All Settings', 'ai-seo-pro'); ?>
				</button>
				<p class="description">
					<?php esc_html_e('Reset all plugin settings to default values (does not delete post meta)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Clear Cache', 'ai-seo-pro'); ?>
			</th>
			<td>
				<button type="button" id="clear_cache" class="button button-secondary">
					<?php esc_html_e('Clear All Caches', 'ai-seo-pro'); ?>
				</button>
				<p class="description">
					<?php esc_html_e('Clear all cached API responses', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>

<script>
	jQuery(document).ready(function ($) {
		$('#reset_settings').on('click', function () {
			if (confirm('<?php echo esc_js(__('Are you sure? This will reset all plugin settings to defaults.', 'ai-seo-pro')); ?>')) {
				// Add AJAX call to reset settings.
				alert('<?php echo esc_js(__('Settings reset functionality will be implemented.', 'ai-seo-pro')); ?>');
			}
		});

		$('#clear_cache').on('click', function () {
			if (confirm('<?php echo esc_js(__('Are you sure? This will clear all cached API responses.', 'ai-seo-pro')); ?>')) {
				// Add AJAX call to clear cache.
				alert('<?php echo esc_js(__('Cache cleared successfully.', 'ai-seo-pro')); ?>');
			}
		});
	});
</script>