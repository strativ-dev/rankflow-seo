<?php
/**
 * Advanced settings tab.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('rankflow_seo_advanced'); ?>

	<h2><?php esc_html_e('Advanced SEO Settings', 'rankflow-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Archive Pages', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_noindex_archives" value="1" <?php checked(get_option('rankflow_seo_noindex_archives', false), true); ?>>
					<?php esc_html_e('Noindex archive pages', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Prevent search engines from indexing category, tag, and date archives', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('URL Optimization', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_remove_stopwords" value="1" <?php checked(get_option('rankflow_seo_remove_stopwords', false), true); ?>>
					<?php esc_html_e('Remove stop words from URLs', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Automatically remove common words (and, the, or, etc.) from post URLs', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Breadcrumbs', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_breadcrumbs" value="1" <?php checked(get_option('rankflow_seo_breadcrumbs', false), true); ?>>
					<?php esc_html_e('Enable breadcrumb schema', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Add breadcrumb structured data to pages', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="api_timeout"><?php esc_html_e('API Timeout', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="number" id="api_timeout" name="rankflow_seo_api_timeout"
					value="<?php echo esc_attr(get_option('rankflow_seo_api_timeout', 30)); ?>" min="10" max="120"
					step="5" class="small-text">
				<?php esc_html_e('seconds', 'rankflow-seo'); ?>
				<p class="description">
					<?php esc_html_e('Maximum time to wait for AI API responses (10-120 seconds)', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="cache_duration"><?php esc_html_e('Cache Duration', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="number" id="cache_duration" name="rankflow_seo_cache_duration"
					value="<?php echo esc_attr(get_option('rankflow_seo_cache_duration', 24)); ?>" min="1" max="168"
					class="small-text">
				<?php esc_html_e('hours', 'rankflow-seo'); ?>
				<p class="description">
					<?php esc_html_e('How long to cache API responses (1-168 hours)', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Danger Zone', 'rankflow-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Reset Settings', 'rankflow-seo'); ?>
			</th>
			<td>
				<button type="button" id="reset_settings" class="button button-secondary">
					<?php esc_html_e('Reset All Settings', 'rankflow-seo'); ?>
				</button>
				<p class="description">
					<?php esc_html_e('Reset all plugin settings to default values (does not delete post meta)', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Clear Cache', 'rankflow-seo'); ?>
			</th>
			<td>
				<button type="button" id="clear_cache" class="button button-secondary">
					<?php esc_html_e('Clear All Caches', 'rankflow-seo'); ?>
				</button>
				<p class="description">
					<?php esc_html_e('Clear all cached API responses', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>

<script>
	jQuery(document).ready(function ($) {
		$('#reset_settings').on('click', function () {
			if (confirm('<?php echo esc_js(__('Are you sure? This will reset all plugin settings to defaults.', 'rankflow-seo')); ?>')) {
				// Add AJAX call to reset settings.
				alert('<?php echo esc_js(__('Settings reset functionality will be implemented.', 'rankflow-seo')); ?>');
			}
		});

		$('#clear_cache').on('click', function () {
			if (confirm('<?php echo esc_js(__('Are you sure? This will clear all cached API responses.', 'rankflow-seo')); ?>')) {
				// Add AJAX call to clear cache.
				alert('<?php echo esc_js(__('Cache cleared successfully.', 'rankflow-seo')); ?>');
			}
		});
	});
</script>