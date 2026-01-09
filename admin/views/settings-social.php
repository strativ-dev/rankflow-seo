<?php
/**
 * Social media settings tab - FIXED
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<form method="post" action="options.php">
	<?php settings_fields('ai_seo_pro_social'); ?>

	<h2><?php esc_html_e('Social Media Meta Tags', 'ai-seo-pro'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<?php esc_html_e('Open Graph Tags', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_og_tags" value="1" <?php checked(get_option('ai_seo_pro_og_tags', true), true); ?>>
					<?php esc_html_e('Enable Open Graph meta tags', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Add Open Graph tags for Facebook, LinkedIn, and other platforms', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Twitter Cards', 'ai-seo-pro'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="ai_seo_pro_twitter_cards" value="1" <?php checked(get_option('ai_seo_pro_twitter_cards', true), true); ?>>
					<?php esc_html_e('Enable Twitter Card meta tags', 'ai-seo-pro'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('Add Twitter Card tags for better Twitter sharing', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Default Social Images', 'ai-seo-pro'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="default_og_image"><?php esc_html_e('Default OG Image', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<input type="url" id="default_og_image" name="ai_seo_pro_default_og_image"
					value="<?php echo esc_url(get_option('ai_seo_pro_default_og_image', '')); ?>" class="regular-text"
					placeholder="https://example.com/image.jpg">
				<p class="description">
					<?php esc_html_e('Default image URL for posts without featured images (1200x630px recommended)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="twitter_username"><?php esc_html_e('Twitter Username', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<input type="text" id="twitter_username" name="ai_seo_pro_twitter_username"
					value="<?php echo esc_attr(get_option('ai_seo_pro_twitter_username', '')); ?>" class="regular-text"
					placeholder="@username">
				<p class="description">
					<?php esc_html_e('Your Twitter username (include @)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>