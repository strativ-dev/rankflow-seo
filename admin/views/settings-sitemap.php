<?php
/**
 * Sitemap Settings View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Get current settings.
$rankflow_seo_sitemap_enabled = get_option('rankflow_seo_sitemap_enabled', true);
$rankflow_seo_include_images = get_option('rankflow_seo_sitemap_include_images', true);
$rankflow_seo_include_taxonomies = get_option('rankflow_seo_sitemap_include_taxonomies', true);
$rankflow_seo_include_authors = get_option('rankflow_seo_sitemap_include_authors', false);
$rankflow_seo_entries_per_page = get_option('rankflow_seo_sitemap_entries_per_page', 1000);
$rankflow_seo_ping_search_engines = get_option('rankflow_seo_sitemap_ping_search_engines', true);
$rankflow_seo_saved_post_types = get_option('rankflow_seo_sitemap_post_types', array());
$rankflow_seo_saved_taxonomies = get_option('rankflow_seo_sitemap_taxonomies', array());

// Get available post types (public ones only).
$rankflow_seo_post_types = get_post_types(array('public' => true), 'objects');

// Exclude page builder and internal post types (no public frontend pages).
$rankflow_seo_excluded_post_types = array(
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

foreach ($rankflow_seo_excluded_post_types as $rankflow_seo_excluded) {
	unset($rankflow_seo_post_types[$rankflow_seo_excluded]);
}

// Get available taxonomies (public ones only).
$rankflow_seo_taxonomies = get_taxonomies(array('public' => true), 'objects');

// Exclude page builder and internal taxonomies.
$rankflow_seo_excluded_taxonomies = array(
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

foreach ($rankflow_seo_excluded_taxonomies as $rankflow_seo_excluded) {
	unset($rankflow_seo_taxonomies[$rankflow_seo_excluded]);
}

// Default post types: only 'post' and 'page' enabled by default.
if (empty($rankflow_seo_saved_post_types)) {
	$rankflow_seo_saved_post_types = array('post', 'page');
}

// Default taxonomies: only 'category' and 'product_cat' enabled by default.
if (empty($rankflow_seo_saved_taxonomies)) {
	$rankflow_seo_saved_taxonomies = array('category', 'product_cat');
}

$rankflow_seo_sitemap_url = home_url('sitemap_index.xml');
?>

<div class="wrap rankflow-seo-settings">
	<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<?php settings_errors('rankflow_seo_sitemap'); ?>

	<!-- Sitemap Status Box -->
	<div class="rankflow-seo-sitemap-status">
		<h2><?php esc_html_e('Sitemap Status', 'rankflow-seo'); ?></h2>

		<?php if ($rankflow_seo_sitemap_enabled): ?>
			<div class="sitemap-enabled">
				<span class="dashicons dashicons-yes-alt"></span>
				<p>
					<?php esc_html_e('Your XML Sitemap is enabled and accessible at:', 'rankflow-seo'); ?>
					<br>
					<a href="<?php echo esc_url($rankflow_seo_sitemap_url); ?>" target="_blank" class="sitemap-url">
						<?php echo esc_html($rankflow_seo_sitemap_url); ?>
						<span class="dashicons dashicons-external"></span>
					</a>
				</p>
				<div class="sitemap-actions">
					<a href="<?php echo esc_url($rankflow_seo_sitemap_url); ?>" target="_blank" class="button">
						<?php esc_html_e('View Sitemap', 'rankflow-seo'); ?>
					</a>
				</div>
			</div>
		<?php else: ?>
			<div class="sitemap-disabled">
				<span class="dashicons dashicons-warning"></span>
				<p><?php esc_html_e('XML Sitemap is currently disabled.', 'rankflow-seo'); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<form method="post" action="options.php">
		<?php settings_fields('rankflow_seo_sitemap_settings'); ?>

		<!-- General Settings -->
		<div class="rankflow-seo-card">
			<h2><?php esc_html_e('General Settings', 'rankflow-seo'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_enabled">
							<?php esc_html_e('Enable XML Sitemap', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<label class="rankflow-seo-toggle">
							<input type="checkbox" id="rankflow_seo_sitemap_enabled" name="rankflow_seo_sitemap_enabled"
								value="1" <?php checked($rankflow_seo_sitemap_enabled); ?>>
							<span class="toggle-slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Generate an XML sitemap for search engines.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_entries_per_page">
							<?php esc_html_e('URLs per Sitemap', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<select id="rankflow_seo_sitemap_entries_per_page" name="rankflow_seo_sitemap_entries_per_page">
							<option value="100" <?php selected($rankflow_seo_entries_per_page, 100); ?>>100</option>
							<option value="250" <?php selected($rankflow_seo_entries_per_page, 250); ?>>250</option>
							<option value="500" <?php selected($rankflow_seo_entries_per_page, 500); ?>>500</option>
							<option value="1000" <?php selected($rankflow_seo_entries_per_page, 1000); ?>>1000</option>
							<option value="2500" <?php selected($rankflow_seo_entries_per_page, 2500); ?>>2500</option>
						</select>
						<p class="description">
							<?php esc_html_e('Maximum number of URLs per sitemap file. Recommended: 1000', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_include_images">
							<?php esc_html_e('Include Images', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<label class="rankflow-seo-toggle">
							<input type="checkbox" id="rankflow_seo_sitemap_include_images"
								name="rankflow_seo_sitemap_include_images" value="1" <?php checked($rankflow_seo_include_images); ?>>
							<span class="toggle-slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include image URLs in the sitemap for better image SEO.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_ping_search_engines">
							<?php esc_html_e('Ping Search Engines', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<label class="rankflow-seo-toggle">
							<input type="checkbox" id="rankflow_seo_sitemap_ping_search_engines"
								name="rankflow_seo_sitemap_ping_search_engines" value="1" <?php checked($rankflow_seo_ping_search_engines); ?>>
							<span class="toggle-slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Automatically notify Google and Bing when content is published.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<!-- Post Types -->
		<div class="rankflow-seo-card">
			<h2><?php esc_html_e('Post Types', 'rankflow-seo'); ?></h2>
			<p class="description">
				<?php esc_html_e('Select which post types to include in the sitemap.', 'rankflow-seo'); ?>
			</p>

			<table class="form-table">
				<?php foreach ($rankflow_seo_post_types as $rankflow_seo_post_type): ?>
					<?php
					$rankflow_seo_is_default = in_array($rankflow_seo_post_type->name, array('post', 'page'), true);
					?>
					<tr>
						<th scope="row">
							<label for="sitemap_pt_<?php echo esc_attr($rankflow_seo_post_type->name); ?>">
								<?php echo esc_html($rankflow_seo_post_type->labels->name); ?>
								<?php if ($rankflow_seo_is_default): ?>
									<span
										class="rankflow-seo-recommended"><?php esc_html_e('(recommended)', 'rankflow-seo'); ?></span>
								<?php endif; ?>
							</label>
						</th>
						<td>
							<label class="rankflow-seo-toggle">
								<input type="checkbox"
									id="sitemap_pt_<?php echo esc_attr($rankflow_seo_post_type->name); ?>"
									name="rankflow_seo_sitemap_post_types[]"
									value="<?php echo esc_attr($rankflow_seo_post_type->name); ?>" <?php checked(in_array($rankflow_seo_post_type->name, $rankflow_seo_saved_post_types, true)); ?>>
								<span class="toggle-slider"></span>
							</label>
							<?php
							$rankflow_seo_count = wp_count_posts($rankflow_seo_post_type->name);
							$rankflow_seo_published = isset($rankflow_seo_count->publish) ? $rankflow_seo_count->publish : 0;
							?>
							<span class="post-count">
								<?php
								printf(
									/* translators: %d: number of published posts */
									esc_html__('%d published', 'rankflow-seo'),
									esc_html($rankflow_seo_published)
								);
								?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>

		<!-- Taxonomies -->
		<div class="rankflow-seo-card">
			<h2><?php esc_html_e('Taxonomies', 'rankflow-seo'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_include_taxonomies">
							<?php esc_html_e('Include Taxonomies', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<label class="rankflow-seo-toggle">
							<input type="checkbox" id="rankflow_seo_sitemap_include_taxonomies"
								name="rankflow_seo_sitemap_include_taxonomies" value="1" <?php checked($rankflow_seo_include_taxonomies); ?>>
							<span class="toggle-slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include taxonomy archives (categories, tags, etc.) in the sitemap.', 'rankflow-seo'); ?>
						</p>
					</td>
				</tr>
			</table>

			<div class="taxonomy-list <?php echo !$rankflow_seo_include_taxonomies ? 'rankflow-seo-hidden' : ''; ?>">
				<table class="form-table">
					<?php foreach ($rankflow_seo_taxonomies as $rankflow_seo_taxonomy): ?>
						<?php
						$rankflow_seo_is_default = in_array($rankflow_seo_taxonomy->name, array('category', 'product_cat'), true);
						?>
						<tr>
							<th scope="row">
								<label for="sitemap_tax_<?php echo esc_attr($rankflow_seo_taxonomy->name); ?>">
									<?php echo esc_html($rankflow_seo_taxonomy->labels->name); ?>
									<?php if ($rankflow_seo_is_default): ?>
										<span
											class="rankflow-seo-recommended"><?php esc_html_e('(recommended)', 'rankflow-seo'); ?></span>
									<?php endif; ?>
								</label>
							</th>
							<td>
								<label class="rankflow-seo-toggle">
									<input type="checkbox"
										id="sitemap_tax_<?php echo esc_attr($rankflow_seo_taxonomy->name); ?>"
										name="rankflow_seo_sitemap_taxonomies[]"
										value="<?php echo esc_attr($rankflow_seo_taxonomy->name); ?>" <?php checked(in_array($rankflow_seo_taxonomy->name, $rankflow_seo_saved_taxonomies, true)); ?>>
									<span class="toggle-slider"></span>
								</label>
								<?php
								$rankflow_seo_term_count = wp_count_terms(
									array(
										'taxonomy' => $rankflow_seo_taxonomy->name,
										'hide_empty' => true,
									)
								);
								?>
								<span class="post-count">
									<?php
									printf(
										/* translators: %d: number of terms */
										esc_html__('%d terms', 'rankflow-seo'),
										esc_html(is_wp_error($rankflow_seo_term_count) ? 0 : $rankflow_seo_term_count)
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
		<div class="rankflow-seo-card">
			<h2><?php esc_html_e('Author Archives', 'rankflow-seo'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="rankflow_seo_sitemap_include_authors">
							<?php esc_html_e('Include Author Archives', 'rankflow-seo'); ?>
						</label>
					</th>
					<td>
						<label class="rankflow-seo-toggle">
							<input type="checkbox" id="rankflow_seo_sitemap_include_authors"
								name="rankflow_seo_sitemap_include_authors" value="1" <?php checked($rankflow_seo_include_authors); ?>>
							<span class="toggle-slider"></span>
						</label>
						<p class="description">
							<?php esc_html_e('Include author archive pages in the sitemap.', 'rankflow-seo'); ?>
						</p>
						<?php
						$rankflow_seo_author_count = count(
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
								esc_html__('%d authors with published posts', 'rankflow-seo'),
								esc_html($rankflow_seo_author_count)
							);
							?>
						</span>
					</td>
				</tr>
			</table>
		</div>

		<!-- Submit -->
		<p class="submit">
			<?php submit_button(esc_html__('Save Settings', 'rankflow-seo'), 'primary', 'submit', false); ?>
			<button type="button" class="button" id="flush-sitemap-cache">
				<?php esc_html_e('Flush Sitemap Cache', 'rankflow-seo'); ?>
			</button>
		</p>
	</form>
</div>

