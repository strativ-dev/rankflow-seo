<?php
/**
 * Features settings tab.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('mpseo_features'); ?>
	<div class="mpseo-section-card">
		<h2><?php esc_html_e('Content Analysis', 'metapilot-smart-seo'); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e('Content Analysis', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_enable_content_analysis" value="1" <?php checked(get_option('mpseo_enable_content_analysis', true), true); ?>>
						<?php esc_html_e('Enable content analysis', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Analyze content quality, keyword density, and readability', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('SEO Score', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_enable_seo_score" value="1" <?php checked(get_option('mpseo_enable_seo_score', true), true); ?>>
						<?php esc_html_e('Enable SEO score calculator', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Calculate and display SEO score for posts', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Schema Markup', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_enable_schema" value="1" <?php checked(get_option('mpseo_enable_schema', true), true); ?>>
						<?php esc_html_e('Enable automatic schema markup', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Automatically generate JSON-LD schema markup for posts', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Focus Keyword', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_focus_keyword" value="1" <?php checked(get_option('mpseo_focus_keyword', true), true); ?>>
						<?php esc_html_e('Enable focus keyword optimization', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Track and optimize content for a specific keyword', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Readability Analysis', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_readability_analysis" value="1" <?php checked(get_option('mpseo_readability_analysis', true), true); ?>>
						<?php esc_html_e('Enable readability analysis', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Analyze content readability using Flesch Reading Ease score', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<?php submit_button(); ?>
</form>