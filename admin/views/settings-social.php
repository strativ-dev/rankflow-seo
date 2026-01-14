<?php
/**
 * Social media settings tab
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$ai_seo_pro_default_og_image = get_option('ai_seo_pro_default_og_image', '');
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
				<label
					for="ai_seo_pro_default_og_image"><?php esc_html_e('Default OG Image', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<div class="default-og-image-wrapper">
					<div class="default-og-image-preview" id="default_og_image_preview">
						<?php if (!empty($ai_seo_pro_default_og_image)): ?>
							<img src="<?php echo esc_url($ai_seo_pro_default_og_image); ?>" alt="">
							<button type="button" class="default-og-image-remove" id="default_og_image_remove"
								title="<?php esc_attr_e('Remove image', 'ai-seo-pro'); ?>">
								<span class="dashicons dashicons-no-alt"></span>
							</button>
						<?php endif; ?>
					</div>
					<input type="hidden" id="ai_seo_pro_default_og_image" name="ai_seo_pro_default_og_image"
						value="<?php echo esc_url($ai_seo_pro_default_og_image); ?>">
					<button type="button" class="button" id="default_og_image_upload">
						<span class="dashicons dashicons-upload"></span>
						<?php esc_html_e('Upload Image', 'ai-seo-pro'); ?>
					</button>
					<p class="description">
						<?php esc_html_e('Default image for posts without featured images. Recommended size: 1200x630 pixels.', 'ai-seo-pro'); ?>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label
					for="ai_seo_pro_twitter_username"><?php esc_html_e('Twitter Username', 'ai-seo-pro'); ?></label>
			</th>
			<td>
				<input type="text" id="ai_seo_pro_twitter_username" name="ai_seo_pro_twitter_username"
					value="<?php echo esc_attr(get_option('ai_seo_pro_twitter_username', '')); ?>"
					class="regular-text" placeholder="@username">
				<p class="description">
					<?php esc_html_e('Your Twitter username (include @)', 'ai-seo-pro'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>

<style>
	.default-og-image-wrapper {
		max-width: 500px;
	}

	.default-og-image-preview {
		position: relative;
		max-width: 300px;
		margin-bottom: 10px;
		border-radius: 8px;
		overflow: hidden;
		background: #f0f0f0;
	}

	.default-og-image-preview:empty {
		display: none;
	}

	.default-og-image-preview img {
		width: 100%;
		height: auto;
		display: block;
		max-height: 180px;
		object-fit: cover;
	}

	.default-og-image-remove {
		position: absolute;
		top: 8px;
		right: 8px;
		background: rgba(0, 0, 0, 0.7);
		border: none;
		border-radius: 50%;
		width: 28px;
		height: 28px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0;
		transition: background 0.2s;
	}

	.default-og-image-remove:hover {
		background: rgba(220, 50, 50, 0.9);
	}

	.default-og-image-remove .dashicons {
		color: #fff;
		font-size: 18px;
		width: 18px;
		height: 18px;
	}

	#default_og_image_upload {
		display: inline-flex;
		align-items: center;
		gap: 5px;
	}

	#default_og_image_upload .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
	}
</style>

<script>
	jQuery(document).ready(function ($) {
		var defaultOgFrame;

		// Upload button click
		$('#default_og_image_upload').on('click', function (e) {
			e.preventDefault();

			if (defaultOgFrame) {
				defaultOgFrame.open();
				return;
			}

			defaultOgFrame = wp.media({
				title: '<?php echo esc_js(__('Select Default OG Image', 'ai-seo-pro')); ?>',
				button: {
					text: '<?php echo esc_js(__('Use this image', 'ai-seo-pro')); ?>'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			defaultOgFrame.on('select', function () {
				var attachment = defaultOgFrame.state().get('selection').first().toJSON();
				var imageUrl = attachment.url;

				$('#ai_seo_pro_default_og_image').val(imageUrl);
				$('#default_og_image_preview').html(
					'<img src="' + imageUrl + '" alt="">' +
					'<button type="button" class="default-og-image-remove" id="default_og_image_remove" title="<?php echo esc_js(__('Remove image', 'ai-seo-pro')); ?>">' +
					'<span class="dashicons dashicons-no-alt"></span></button>'
				);
			});

			defaultOgFrame.open();
		});

		// Remove button click
		$(document).on('click', '#default_og_image_remove', function (e) {
			e.preventDefault();
			$('#ai_seo_pro_default_og_image').val('');
			$('#default_og_image_preview').empty();
		});
	});
</script>