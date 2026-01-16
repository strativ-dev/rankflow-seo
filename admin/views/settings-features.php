<?php
/**
 * Features settings tab.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('rankflow_seo_features'); ?>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Content Analysis', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_enable_content_analysis" value="1" <?php checked(get_option('rankflow_seo_enable_content_analysis', true), true); ?>>
					<?php esc_html_e('Enable content analysis', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Analyze content quality, keyword density, and readability', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('SEO Score', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_enable_seo_score" value="1" <?php checked(get_option('rankflow_seo_enable_seo_score', true), true); ?>>
					<?php esc_html_e('Enable SEO score calculator', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Calculate and display SEO score for posts', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Schema Markup', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_enable_schema" value="1" <?php checked(get_option('rankflow_seo_enable_schema', true), true); ?>>
					<?php esc_html_e('Enable automatic schema markup', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Automatically generate JSON-LD schema markup for posts', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Focus Keyword', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_focus_keyword" value="1" <?php checked(get_option('rankflow_seo_focus_keyword', true), true); ?>>
					<?php esc_html_e('Enable focus keyword optimization', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Track and optimize content for a specific keyword', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Readability Analysis', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_readability_analysis" value="1" <?php checked(get_option('rankflow_seo_readability_analysis', true), true); ?>>
					<?php esc_html_e('Enable readability analysis', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Analyze content readability using Flesch Reading Ease score', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>