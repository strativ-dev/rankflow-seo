<?php
/**
 * Sitemap Settings View
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Get current settings.
$ai_seo_pro_sitemap_enabled = get_option('ai_seo_pro_sitemap_enabled', true);
$ai_seo_pro_include_images = get_option('ai_seo_pro_sitemap_include_images', true);
$ai_seo_pro_include_taxonomies = get_option('ai_seo_pro_sitemap_include_taxonomies', true);
$ai_seo_pro_include_authors = get_option('ai_seo_pro_sitemap_include_authors', false);
$ai_seo_pro_entries_per_page = get_option('ai_seo_pro_sitemap_entries_per_page', 1000);
$ai_seo_pro_ping_search_engines = get_option('ai_seo_pro_sitemap_ping_search_engines', true);
$ai_seo_pro_saved_post_types = get_option('ai_seo_pro_sitemap_post_types', array());
$ai_seo_pro_saved_taxonomies = get_option('ai_seo_pro_sitemap_taxonomies', array());

// Get available post types (public ones only).
$ai_seo_pro_post_types = get_post_types(array('public' => true), 'objects');

// Exclude page builder and internal post types (no public frontend pages).
$ai_seo_pro_excluded_post_types = array(
	'attachment',
	// Elementor.
	'elementor_library',
	'elementor_template',
	'elementor-thhf',
	'elementor_icons',
	'e-landing-page',
	'e-floating-buttons',
	// Divi.
	'et_pb_layout',
	'et_header_layout',
	'et_body_layout',
	'et_footer_layout',
	'et_template',
	'et_code_snippet',
	// Beaver Builder.
	'fl-builder-template',
	'fl-theme-layout',
	// WPBakery.
	'vc_grid_item',
	// Brizy.
	'brizy_template',
	'brizy-global-block',
	'brizy-saved-block',
	// Oxygen.
	'ct_template',
	'oxy_user_library',
	// Thrive.
	'tcb_lightbox',
	'tve_form_type',
	'tve_lead_shortcode',
	'tve_lead_2s_lightbox',
	// Fusion Builder (Avada).
	'fusion_template',
	'fusion_tb_layout',
	'fusion_tb_section',
	'fusion_element',
	'fusion_icons',
	'slide',
	'awb_off_canvas',
	// SeedProd.
	'seedprod',
	// OptimizePress.
	'op_page',
	// GenerateBlocks.
	'gblocks_templates',
	'gblocks_global_style',
	// Spectra.
	'spectra-popup',
	'uag-library',
	// Kadence.
	'kadence_element',
	'kadence_form',
	// Stackable.
	'stackable_template',
	// JetEngine.
	'jet-engine',
	'jet-menu',
	'jet-popup',
	'jet-smart-filters',
	'jet-theme-core',
	// Jepack.
	'jp_pay_order',
	'jp_pay_product',
	'feedback',
	// WooCommerce internal.
	'shop_order',
	'shop_coupon',
	'shop_order_refund',
	'product_variation',
	// ACF.
	'acf-field-group',
	'acf-field',
	'acf-post-type',
	'acf-taxonomy',
	'acf-ui-options-page',
	// WPForms.
	'wpforms',
	'wpforms_log',
	// Gravity Forms.
	'gravityview',
	// Formidable Forms.
	'frm_form_actions',
	// Contact Form 7.
	'wpcf7_contact_form',
	// Custom CSS & JS.
	'custom-css-js',
	// Popup Maker.
	'popup',
	'popup_theme',
	// MailPoet.
	'mailpoet_page',
	// Redirection.
	'redirection_item',
	// Polylang.
	'polylang_mo',
	// WPML.
	'wpml_string',
	// Yoast SEO.
	'yoast_seo_link',
	// Rank Math.
	'rm_content_ai',
	// MonsterInsights.
	'mi_log',
	// Wordfence.
	'wfsn',
	// UpdraftPlus.
	'updraft_jobdata',
	// TablePress.
	'tablepress_table',
	// Revision & internal WordPress types.
	'revision',
	'nav_menu_item',
	'custom_css',
	'customize_changeset',
	'oembed_cache',
	'user_request',
	'wp_block',
	'wp_template',
	'wp_template_part',
	'wp_global_styles',
	'wp_navigation',
	'wp_font_family',
	'wp_font_face',
);

foreach ($ai_seo_pro_excluded_post_types as $ai_seo_pro_excluded) {
	unset($ai_seo_pro_post_types[$ai_seo_pro_excluded]);
}

// Get available taxonomies (public ones only).
$ai_seo_pro_taxonomies = get_taxonomies(array('public' => true), 'objects');

// Exclude page builder and internal taxonomies.
$ai_seo_pro_excluded_taxonomies = array(
	'post_format',
	// Elementor.
	'elementor_library_type',
	'elementor_library_category',
	'elementor_library_doc_type',
	'elementor_font_type',
	// Divi.
	'et_pb_scope',
	'et_pb_layout_type',
	'et_pb_layout_category',
	// Beaver Builder.
	'fl-builder-template-type',
	'fl-builder-template-category',
	// WPBakery.
	'vc_grid_item_cat',
	// Oxygen.
	'ct_template_category',
	// Brizy.
	'brizy_template_type',
	// Fusion Builder (Avada).
	'fusion_tb_category',
	'element_category',
	'template_category',
	// JetEngine.
	'jet-engine-type',
	'jet-popup-type',
	// WooCommerce internal.
	'product_visibility',
	'product_shipping_class',
	'product_type',
	// ACF.
	'acf-field-group-category',
	// WPML.
	'translation_priority',
	// Action Scheduler.
	'action-group',
	// Navigation & internal WordPress taxonomies.
	'nav_menu',
	'link_category',
	'wp_theme',
	'wp_template_part_area',
	'wp_pattern_category',
);

foreach ($ai_seo_pro_excluded_taxonomies as $ai_seo_pro_excluded) {
	unset($ai_seo_pro_taxonomies[$ai_seo_pro_excluded]);
}

// Default post types: only 'post' and 'page' enabled by default.
if (empty($ai_seo_pro_saved_post_types)) {
	$ai_seo_pro_saved_post_types = array('post', 'page');
}

// Default taxonomies: only 'category' and 'product_cat' enabled by default.
if (empty($ai_seo_pro_saved_taxonomies)) {
	$ai_seo_pro_saved_taxonomies = array('category', 'product_cat');
}

$ai_seo_pro_sitemap_url = home_url('sitemap_index.xml');
?>

<div class="wrap ai-seo-pro-settings">
	<h1><?php esc_html_e('XML Sitemap Settings', 'ai-seo-pro'); ?></h1>

	<?php settings_errors('ai_seo_pro_sitemap'); ?>

	<!-- Sitemap Status Box -->
	<div class="ai-seo-pro-sitemap-status">
		<h2><?php esc_html_e('Sitemap Status', 'ai-seo-pro'); ?></h2>

		<?php if ($ai_seo_pro_sitemap_enabled): ?>
			<div class="sitemap-enabled">
				<span class="dashicons dashicons-yes-alt"></span>
				<p>
					<?php esc_html_e('Your XML Sitemap is enabled and accessible at:', 'ai-seo-pro'); ?>
					<br>
					<a href="<?php echo esc_url($ai_seo_pro_sitemap_url); ?>" target="_blank" class="sitemap-url">
						<?php echo esc_html($ai_seo_pro_sitemap_url); ?>
						<span class="dashicons dashicons-external"></span>
					</a>
				</p>
				<div class="sitemap-actions">
					<a href="<?php echo esc_url($ai_seo_pro_sitemap_url); ?>" target="_blank" class="button">
						<?php esc_html_e('View Sitemap', 'ai-seo-pro'); ?>
					</a>
				</div>
			</div>
		<?php else: ?>
			<div class="sitemap-disabled">
				<span class="dashicons dashicons-warning"></span>
				<p><?php esc_html_e('XML Sitemap is currently disabled.', 'ai-seo-pro'); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<form method="post" action="options.php">
		<?php settings_fields('ai_seo_pro_sitemap_settings'); ?>

		<!-- General Settings -->
		<div class="ai-seo-pro-card">
			<h2><?php esc_html_e('General Settings', 'ai-seo-pro'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_enabled">
							<?php esc_html_e('Enable XML Sitemap', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<label class="ai-seo-toggle">
							<input type="checkbox" id="ai_seo_pro_sitemap_enabled" name="ai_seo_pro_sitemap_enabled"
								value="1" <?php checked($ai_seo_pro_sitemap_enabled); ?>>
							<span class="slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Generate an XML sitemap for search engines.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_entries_per_page">
							<?php esc_html_e('URLs per Sitemap', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<select id="ai_seo_pro_sitemap_entries_per_page" name="ai_seo_pro_sitemap_entries_per_page">
							<option value="100" <?php selected($ai_seo_pro_entries_per_page, 100); ?>>100</option>
							<option value="250" <?php selected($ai_seo_pro_entries_per_page, 250); ?>>250</option>
							<option value="500" <?php selected($ai_seo_pro_entries_per_page, 500); ?>>500</option>
							<option value="1000" <?php selected($ai_seo_pro_entries_per_page, 1000); ?>>1000</option>
							<option value="2500" <?php selected($ai_seo_pro_entries_per_page, 2500); ?>>2500</option>
						</select>
						<p class="description">
							<?php esc_html_e('Maximum number of URLs per sitemap file. Recommended: 1000', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_include_images">
							<?php esc_html_e('Include Images', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<label class="ai-seo-toggle">
							<input type="checkbox" id="ai_seo_pro_sitemap_include_images"
								name="ai_seo_pro_sitemap_include_images" value="1" <?php checked($ai_seo_pro_include_images); ?>>
							<span class="slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include image URLs in the sitemap for better image SEO.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_ping_search_engines">
							<?php esc_html_e('Ping Search Engines', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<label class="ai-seo-toggle">
							<input type="checkbox" id="ai_seo_pro_sitemap_ping_search_engines"
								name="ai_seo_pro_sitemap_ping_search_engines" value="1" <?php checked($ai_seo_pro_ping_search_engines); ?>>
							<span class="slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Automatically notify Google and Bing when content is published.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<!-- Post Types -->
		<div class="ai-seo-pro-card">
			<h2><?php esc_html_e('Post Types', 'ai-seo-pro'); ?></h2>
			<p class="description">
				<?php esc_html_e('Select which post types to include in the sitemap.', 'ai-seo-pro'); ?>
			</p>

			<table class="form-table">
				<?php foreach ($ai_seo_pro_post_types as $ai_seo_pro_post_type): ?>
					<?php
					$ai_seo_pro_is_default = in_array($ai_seo_pro_post_type->name, array('post', 'page'), true);
					?>
					<tr>
						<th scope="row">
							<label for="sitemap_pt_<?php echo esc_attr($ai_seo_pro_post_type->name); ?>">
								<?php echo esc_html($ai_seo_pro_post_type->labels->name); ?>
								<?php if ($ai_seo_pro_is_default): ?>
									<span
										class="ai-seo-pro-recommended"><?php esc_html_e('(recommended)', 'ai-seo-pro'); ?></span>
								<?php endif; ?>
							</label>
						</th>
						<td>
							<label class="ai-seo-toggle">
								<input type="checkbox" id="sitemap_pt_<?php echo esc_attr($ai_seo_pro_post_type->name); ?>"
									name="ai_seo_pro_sitemap_post_types[]"
									value="<?php echo esc_attr($ai_seo_pro_post_type->name); ?>" <?php checked(in_array($ai_seo_pro_post_type->name, $ai_seo_pro_saved_post_types, true)); ?>>
								<span class="slider"></span>
							</label>
							<?php
							$ai_seo_pro_count = wp_count_posts($ai_seo_pro_post_type->name);
							$ai_seo_pro_published = isset($ai_seo_pro_count->publish) ? $ai_seo_pro_count->publish : 0;
							?>
							<span class="post-count">
								<?php
								printf(
									/* translators: %d: number of published posts */
									esc_html__('%d published', 'ai-seo-pro'),
									esc_html($ai_seo_pro_published)
								);
								?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>

		<!-- Taxonomies -->
		<div class="ai-seo-pro-card">
			<h2><?php esc_html_e('Taxonomies', 'ai-seo-pro'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_include_taxonomies">
							<?php esc_html_e('Include Taxonomies', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<label class="ai-seo-toggle">
							<input type="checkbox" id="ai_seo_pro_sitemap_include_taxonomies"
								name="ai_seo_pro_sitemap_include_taxonomies" value="1" <?php checked($ai_seo_pro_include_taxonomies); ?>>
							<span class="slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include taxonomy archives (categories, tags, etc.) in the sitemap.', 'ai-seo-pro'); ?>
						</p>
					</td>
				</tr>
			</table>

			<div class="taxonomy-list" <?php echo !$ai_seo_pro_include_taxonomies ? 'style="display:none;"' : ''; ?>>
				<table class="form-table">
					<?php foreach ($ai_seo_pro_taxonomies as $ai_seo_pro_taxonomy): ?>
						<?php
						$ai_seo_pro_is_default = in_array($ai_seo_pro_taxonomy->name, array('category', 'product_cat'), true);
						?>
						<tr>
							<th scope="row">
								<label for="sitemap_tax_<?php echo esc_attr($ai_seo_pro_taxonomy->name); ?>">
									<?php echo esc_html($ai_seo_pro_taxonomy->labels->name); ?>
									<?php if ($ai_seo_pro_is_default): ?>
										<span
											class="ai-seo-pro-recommended"><?php esc_html_e('(recommended)', 'ai-seo-pro'); ?></span>
									<?php endif; ?>
								</label>
							</th>
							<td>
								<label class="ai-seo-toggle">
									<input type="checkbox"
										id="sitemap_tax_<?php echo esc_attr($ai_seo_pro_taxonomy->name); ?>"
										name="ai_seo_pro_sitemap_taxonomies[]"
										value="<?php echo esc_attr($ai_seo_pro_taxonomy->name); ?>" <?php checked(in_array($ai_seo_pro_taxonomy->name, $ai_seo_pro_saved_taxonomies, true)); ?>>
									<span class="slider"></span>
								</label>
								<?php
								$ai_seo_pro_term_count = wp_count_terms(
									array(
										'taxonomy' => $ai_seo_pro_taxonomy->name,
										'hide_empty' => true,
									)
								);
								?>
								<span class="post-count">
									<?php
									printf(
										/* translators: %d: number of terms */
										esc_html__('%d terms', 'ai-seo-pro'),
										esc_html(is_wp_error($ai_seo_pro_term_count) ? 0 : $ai_seo_pro_term_count)
									);
									?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>

		<!-- Authors -->
		<div class="ai-seo-pro-card">
			<h2><?php esc_html_e('Author Archives', 'ai-seo-pro'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="ai_seo_pro_sitemap_include_authors">
							<?php esc_html_e('Include Author Archives', 'ai-seo-pro'); ?>
						</label>
					</th>
					<td>
						<label class="ai-seo-toggle">
							<input type="checkbox" id="ai_seo_pro_sitemap_include_authors"
								name="ai_seo_pro_sitemap_include_authors" value="1" <?php checked($ai_seo_pro_include_authors); ?>>
							<span class="slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include author archive pages in the sitemap.', 'ai-seo-pro'); ?>
						</p>
						<?php
						$ai_seo_pro_author_count = count(
							get_users(
								array(
									'has_published_posts' => true,
									'fields' => 'ID',
								)
							)
						);
						?>
						<span class="post-count">
							<?php
							printf(
								/* translators: %d: number of authors */
								esc_html__('%d authors with published posts', 'ai-seo-pro'),
								esc_html($ai_seo_pro_author_count)
							);
							?>
						</span>
					</td>
				</tr>
			</table>
		</div>

		<!-- Submit -->
		<p class="submit">
			<?php submit_button(esc_html__('Save Settings', 'ai-seo-pro'), 'primary', 'submit', false); ?>
			<button type="button" class="button" id="flush-sitemap-cache">
				<?php esc_html_e('Flush Sitemap Cache', 'ai-seo-pro'); ?>
			</button>
		</p>
	</form>
</div>

<style>
	.ai-seo-pro-sitemap-status {
		background: #fff;
		border: 1px solid #ccd0d4;
		border-radius: 4px;
		padding: 20px;
		margin: 20px 0;
	}

	.ai-seo-pro-sitemap-status h2 {
		margin-top: 0;
		padding-bottom: 10px;
		border-bottom: 1px solid #eee;
	}

	.sitemap-enabled {
		display: flex;
		align-items: flex-start;
		gap: 15px;
		flex-wrap: wrap;
	}

	.sitemap-enabled .dashicons-yes-alt {
		color: #46b450;
		font-size: 40px;
		width: 40px;
		height: 40px;
	}

	.sitemap-disabled .dashicons-warning {
		color: #dc3232;
		font-size: 40px;
		width: 40px;
		height: 40px;
	}

	.sitemap-url {
		display: inline-flex;
		align-items: center;
		gap: 5px;
		font-size: 14px;
		font-weight: 500;
		word-break: break-all;
	}

	.sitemap-actions {
		width: 100%;
		margin-top: 10px;
		display: flex;
		gap: 10px;
	}

	.ai-seo-pro-card {
		background: #fff;
		border: 1px solid #ccd0d4;
		border-radius: 4px;
		padding: 20px;
		margin: 20px 0;
	}

	.ai-seo-pro-card h2 {
		margin-top: 0;
		padding-bottom: 10px;
		border-bottom: 1px solid #eee;
	}

	.ai-seo-toggle {
		position: relative;
		display: inline-block;
		width: 40px;
		height: 22px;
		vertical-align: middle;
	}

	.ai-seo-toggle input {
		opacity: 0;
		width: 0;
		height: 0;
	}

	.ai-seo-toggle .slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #ccc;
		transition: .3s;
		border-radius: 22px;
	}

	.ai-seo-toggle .slider:before {
		position: absolute;
		content: "";
		height: 16px;
		width: 16px;
		left: 3px;
		bottom: 3px;
		background-color: white;
		transition: .3s;
		border-radius: 50%;
	}

	.ai-seo-toggle input:checked+.slider {
		background-color: #0073aa;
	}

	.ai-seo-toggle input:checked+.slider:before {
		transform: translateX(18px);
	}

	.post-count {
		color: #666;
		font-size: 12px;
		margin-left: 10px;
	}

	.taxonomy-list {
		margin-top: 15px;
		padding-top: 15px;
		border-top: 1px solid #eee;
	}

	#flush-sitemap-cache {
		margin-left: 10px;
	}

	.ai-seo-pro-recommended {
		color: #2271b1;
		font-size: 12px;
		font-weight: normal;
		margin-left: 5px;
	}
</style>

<script>
	jQuery(document).ready(function ($) {
		// Toggle taxonomy list visibility.
		$('#ai_seo_pro_sitemap_include_taxonomies').on('change', function () {
			$('.taxonomy-list').toggle(this.checked);
		});

		// Flush sitemap cache.
		$('#flush-sitemap-cache').on('click', function () {
			var $btn = $(this);
			$btn.prop('disabled', true).text('<?php echo esc_js(__('Flushing...', 'ai-seo-pro')); ?>');

			$.post(ajaxurl, {
				action: 'ai_seo_pro_flush_sitemap',
				nonce: '<?php echo esc_js(wp_create_nonce('ai_seo_pro_sitemap')); ?>'
			}, function (response) {
				$btn.prop('disabled', false).text('<?php echo esc_js(__('Flush Sitemap Cache', 'ai-seo-pro')); ?>');
				if (response.success) {
					alert('<?php echo esc_js(__('Sitemap cache flushed successfully!', 'ai-seo-pro')); ?>');
				}
			});
		});
	});
</script>