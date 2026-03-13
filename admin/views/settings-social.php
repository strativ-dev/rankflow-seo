<?php
/**
 * Social media settings tab
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$mpseo_default_og_image = get_option('mpseo_default_og_image', '');
?>

<form method="post" action="options.php">
	<?php settings_fields('mpseo_social'); ?>

	<div class="mpseo-section-card">
		<h2><?php esc_html_e('Social Media Meta Tags', 'metapilot-smart-seo'); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row">
					<?php esc_html_e('Open Graph Tags', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_og_tags" value="1" <?php checked(get_option('mpseo_og_tags', true), true); ?>>
						<?php esc_html_e('Enable Open Graph meta tags', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Add Open Graph tags for Facebook, LinkedIn, and other platforms', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php esc_html_e('Twitter Cards', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_twitter_cards" value="1" <?php checked(get_option('mpseo_twitter_cards', true), true); ?>>
						<?php esc_html_e('Enable Twitter Card meta tags', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
						<?php esc_html_e('Add Twitter Card tags for better Twitter sharing', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<div class="mpseo-section-card">
		<h2><?php esc_html_e('Default Social Images', 'metapilot-smart-seo'); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label
						for="mpseo_default_og_image"><?php esc_html_e('Default OG Image', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<div class="default-og-image-wrapper">
						<div class="default-og-image-preview" id="default_og_image_preview">
							<?php if (!empty($mpseo_default_og_image)): ?>
								<img src="<?php echo esc_url($mpseo_default_og_image); ?>" alt="">
								<button type="button" class="default-og-image-remove" id="default_og_image_remove"
									title="<?php esc_attr_e('Remove image', 'metapilot-smart-seo'); ?>">
									<span class="dashicons dashicons-no-alt"></span>
								</button>
							<?php endif; ?>
						</div>
						<input type="hidden" id="mpseo_default_og_image" name="mpseo_default_og_image"
							value="<?php echo esc_url($mpseo_default_og_image); ?>">
						<button type="button" class="button" id="default_og_image_upload">
							<span class="dashicons dashicons-upload"></span>
							<?php esc_html_e('Upload Image', 'metapilot-smart-seo'); ?>
						</button>
						<p class="description">
							<?php esc_html_e('Default image for posts without featured images. Recommended size: 1200x630 pixels.', 'metapilot-smart-seo'); ?>
						</p>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label
						for="mpseo_twitter_username"><?php esc_html_e('Twitter Username', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<input type="text" id="mpseo_twitter_username" name="mpseo_twitter_username"
						value="<?php echo esc_attr(get_option('mpseo_twitter_username', '')); ?>" class="regular-text"
						placeholder="@username">
					<p class="description">
						<?php esc_html_e('Your Twitter username (include @)', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<?php submit_button(); ?>
</form>