<?php
/**
 * Advanced settings tab.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('mpseo_advanced'); ?>
	<div class="mpseo-section-card">
		<h2><?php esc_html_e('Advanced SEO Settings', 'metapilot-smart-seo'); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e('Archive Pages', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_noindex_archives" value="1" <?php checked(get_option('mpseo_noindex_archives', false), true); ?>>
						<?php esc_html_e('Noindex archive pages', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Prevent search engines from indexing category, tag, and date archives', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('URL Optimization', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_remove_stopwords" value="1" <?php checked(get_option('mpseo_remove_stopwords', false), true); ?>>
						<?php esc_html_e('Remove stop words from URLs', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Automatically remove common words (and, the, or, etc.) from post URLs', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Breadcrumbs', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_breadcrumbs" value="1" <?php checked(get_option('mpseo_breadcrumbs', false), true); ?>>
						<?php esc_html_e('Enable breadcrumb schema', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Add breadcrumb structured data to pages', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="api_timeout"><?php esc_html_e('API Timeout', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<input type="number" id="api_timeout" name="mpseo_api_timeout"
						value="<?php echo esc_attr(get_option('mpseo_api_timeout', 30)); ?>" min="10" max="120" step="5"
						class="small-text">
					<?php esc_html_e('seconds', 'metapilot-smart-seo'); ?>
					<p class="description">
						<?php esc_html_e('Maximum time to wait for AI API responses (10-120 seconds)', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="cache_duration"><?php esc_html_e('Cache Duration', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<input type="number" id="cache_duration" name="mpseo_cache_duration"
						value="<?php echo esc_attr(get_option('mpseo_cache_duration', 24)); ?>" min="1" max="168"
						class="small-text">
					<?php esc_html_e('hours', 'metapilot-smart-seo'); ?>
					<p class="description">
						<?php esc_html_e('How long to cache API responses (1-168 hours)', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<div class="mpseo-section-card">
		<h2><?php esc_html_e('Danger Zone', 'metapilot-smart-seo'); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e('Reset Settings', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<button type="button" id="reset_settings" class="button button-secondary">
						<?php esc_html_e('Reset All Settings', 'metapilot-smart-seo'); ?>
					</button>
					<p class="description">
						<?php esc_html_e('Reset all plugin settings to default values (does not delete post meta)', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Clear Cache', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<button type="button" id="clear_cache" class="button button-secondary">
						<?php esc_html_e('Clear All Caches', 'metapilot-smart-seo'); ?>
					</button>
					<p class="description">
						<?php esc_html_e('Clear all cached API responses', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<?php submit_button(); ?>
</form>