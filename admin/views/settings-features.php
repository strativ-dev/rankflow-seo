<?php
/**
 * Features settings tab.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('ai_seo_pro_features'); ?>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Content Analysis', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_enable_content_analysis" value="1" <?php checked(get_option('ai_seo_pro_enable_content_analysis', true), true); ?>>
					<?php esc_html_e('Enable content analysis', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Analyze content quality, keyword density, and readability', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('SEO Score', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_enable_seo_score" value="1" <?php checked(get_option('ai_seo_pro_enable_seo_score', true), true); ?>>
					<?php esc_html_e('Enable SEO score calculator', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Calculate and display SEO score for posts', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Schema Markup', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_enable_schema" value="1" <?php checked(get_option('ai_seo_pro_enable_schema', true), true); ?>>
					<?php esc_html_e('Enable automatic schema markup', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Automatically generate JSON-LD schema markup for posts', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Focus Keyword', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_focus_keyword" value="1" <?php checked(get_option('ai_seo_pro_focus_keyword', true), true); ?>>
					<?php esc_html_e('Enable focus keyword optimization', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Track and optimize content for a specific keyword', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Readability Analysis', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_readability_analysis" value="1" <?php checked(get_option('ai_seo_pro_readability_analysis', true), true); ?>>
					<?php esc_html_e('Enable readability analysis', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Analyze content readability using Flesch Reading Ease score', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>