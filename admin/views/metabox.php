<?php
/**
 * Meta box view with tabs - SEO & Readability
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 *
 * @var WP_Post $post                            Post object
 * @var array   $ai_seo_pro_meta_data            Meta data
 * @var int     $ai_seo_pro_seo_score            SEO score
 * @var array   $ai_seo_pro_seo_analysis         SEO analysis results
 * @var array   $ai_seo_pro_readability_analysis Readability analysis results
 */

if (!defined('ABSPATH')) {
	exit;
}

// Ensure variables are defined with defaults.
if (!isset($post)) {
	$post = get_post();
}

// Handle meta_data.
if (!isset($ai_seo_pro_meta_data)) {
	if (isset($meta_data)) {
		$ai_seo_pro_meta_data = $meta_data;
	} else {
		$ai_seo_pro_meta_data = array();
	}
}

// Ensure all keys exist with defaults.
$ai_seo_pro_meta_data = wp_parse_args($ai_seo_pro_meta_data, array(
	'title' => '',
	'description' => '',
	'keywords' => '',
	'focus_keyword' => '',
	'canonical' => '',
	'robots' => '',
	'og_title' => '',
	'og_description' => '',
	'og_image' => '',
	'twitter_title' => '',
	'twitter_description' => '',
	'auto_generate' => '',
));

// Handle seo_score.
if (!isset($ai_seo_pro_seo_score)) {
	$ai_seo_pro_seo_score = isset($seo_score) ? $seo_score : 0;
}

// Handle seo_analysis.
if (!isset($ai_seo_pro_seo_analysis)) {
	$ai_seo_pro_seo_analysis = isset($seo_analysis) ? $seo_analysis : array();
}

// Handle readability_analysis.
if (!isset($ai_seo_pro_readability_analysis)) {
	$ai_seo_pro_readability_analysis = isset($readability_analysis) ? $readability_analysis : array();
}

$ai_seo_pro_score_status = (new AI_SEO_Pro_SEO_Score())->get_score_status($ai_seo_pro_seo_score);

// Count problems and good results.
$ai_seo_pro_seo_problems = isset($ai_seo_pro_seo_analysis['problems']) ? count($ai_seo_pro_seo_analysis['problems']) : 0;
$ai_seo_pro_seo_good = isset($ai_seo_pro_seo_analysis['good']) ? count($ai_seo_pro_seo_analysis['good']) : 0;
$ai_seo_pro_readability_problems = isset($ai_seo_pro_readability_analysis['problems']) ? count($ai_seo_pro_readability_analysis['problems']) : 0;
$ai_seo_pro_readability_good = isset($ai_seo_pro_readability_analysis['good']) ? count($ai_seo_pro_readability_analysis['good']) : 0;

// Get exclude from sitemap value.
$ai_seo_pro_exclude_sitemap = get_post_meta($post->ID, '_ai_seo_exclude_sitemap', true);

// Get OG Image from post meta.
$ai_seo_pro_og_image = get_post_meta($post->ID, '_ai_seo_og_image', true);

/**
 * Get first image from post content (works with page builders).
 *
 * @param WP_Post $ai_seo_pro_post Post object.
 * @return string Image URL or empty string.
 */
function ai_seo_pro_get_first_content_image($ai_seo_pro_post)
{
	$ai_seo_pro_content = $ai_seo_pro_post->post_content;

	// Check for standard img tags in raw content.
	if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $ai_seo_pro_content, $ai_seo_pro_matches)) {
		return $ai_seo_pro_matches[1];
	}

	// Check for Gutenberg image blocks.
	if (preg_match('/<!-- wp:image[^>]*-->.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is', $ai_seo_pro_content, $ai_seo_pro_matches)) {
		return $ai_seo_pro_matches[1];
	}

	// Check for Gutenberg cover blocks with background image.
	if (preg_match('/<!-- wp:cover[^>]*"url":"([^"]+)"[^>]*-->/i', $ai_seo_pro_content, $ai_seo_pro_matches)) {
		return stripslashes($ai_seo_pro_matches[1]);
	}

	// Check for image attachment class (wp-image-ID).
	if (preg_match('/wp-image-(\d+)/i', $ai_seo_pro_content, $ai_seo_pro_matches)) {
		$ai_seo_pro_image_url = wp_get_attachment_image_url(intval($ai_seo_pro_matches[1]), 'large');
		if ($ai_seo_pro_image_url) {
			return $ai_seo_pro_image_url;
		}
	}

	// Check for Elementor data.
	$ai_seo_pro_elementor_data = get_post_meta($ai_seo_pro_post->ID, '_elementor_data', true);
	if (!empty($ai_seo_pro_elementor_data) && is_string($ai_seo_pro_elementor_data)) {
		// Look for image URL in Elementor JSON.
		if (preg_match('/"url":\s*"(https?:[^"]+\.(?:jpg|jpeg|png|gif|webp))"/i', $ai_seo_pro_elementor_data, $ai_seo_pro_matches)) {
			return stripslashes($ai_seo_pro_matches[1]);
		}
	}

	// Check for Divi Builder data.
	$ai_seo_pro_divi_data = get_post_meta($ai_seo_pro_post->ID, '_et_builder_version', true);
	if (!empty($ai_seo_pro_divi_data)) {
		// Divi stores images in shortcodes, check content for src attributes.
		if (preg_match('/src=["\']([^"\']+\.(?:jpg|jpeg|png|gif|webp))["\'][^>]*>/i', $ai_seo_pro_content, $ai_seo_pro_matches)) {
			return $ai_seo_pro_matches[1];
		}
	}

	// Check for WPBakery/Visual Composer shortcodes.
	if (preg_match('/\[vc_single_image[^\]]+image=["\']?(\d+)["\']?/i', $ai_seo_pro_content, $ai_seo_pro_matches)) {
		$ai_seo_pro_image_url = wp_get_attachment_image_url(intval($ai_seo_pro_matches[1]), 'large');
		if ($ai_seo_pro_image_url) {
			return $ai_seo_pro_image_url;
		}
	}

	// Check for Beaver Builder data.
	$ai_seo_pro_bb_data = get_post_meta($ai_seo_pro_post->ID, '_fl_builder_data', true);
	if (!empty($ai_seo_pro_bb_data) && is_array($ai_seo_pro_bb_data)) {
		$ai_seo_pro_bb_json = wp_json_encode($ai_seo_pro_bb_data);
		if (preg_match('/"photo_src":\s*"([^"]+)"/i', $ai_seo_pro_bb_json, $ai_seo_pro_matches)) {
			return stripslashes($ai_seo_pro_matches[1]);
		}
	}

	return '';
}

// Get preview image (priority: OG Image > Featured Image > Content Image > Default OG Image).
$ai_seo_pro_preview_image = '';

if (!empty($ai_seo_pro_og_image)) {
	// 1. OG Image (if set)
	$ai_seo_pro_preview_image = $ai_seo_pro_og_image;
} elseif (has_post_thumbnail($post->ID)) {
	// 2. Featured Image
	$ai_seo_pro_preview_image = get_the_post_thumbnail_url($post->ID, 'medium');
} else {
	// 3. First image from content/page builder
	$ai_seo_pro_content_image = ai_seo_pro_get_first_content_image($post);
	if (!empty($ai_seo_pro_content_image)) {
		$ai_seo_pro_preview_image = $ai_seo_pro_content_image;
	} else {
		// 4. Default OG Image from settings
		$ai_seo_pro_default_og = get_option('ai_seo_pro_default_og_image', '');
		if (!empty($ai_seo_pro_default_og)) {
			$ai_seo_pro_preview_image = $ai_seo_pro_default_og;
		}
	}
}

// Get site icon/favicon.
$ai_seo_pro_site_icon = get_site_icon_url(16);
?>

<div class="ai-seo-pro-metabox">
	<!-- Tab Navigation -->
	<div class="ai-seo-tabs-nav">
		<button type="button" class="ai-seo-tab-btn active" data-tab="seo">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e('SEO', 'ai-seo-pro'); ?>
			<?php if ($ai_seo_pro_seo_problems > 0): ?>
				<span class="tab-badge badge-problems"><?php echo esc_html($ai_seo_pro_seo_problems); ?></span>
			<?php endif; ?>
		</button>
		<button type="button" class="ai-seo-tab-btn" data-tab="readability">
			<span class="dashicons dashicons-editor-paragraph"></span>
			<?php esc_html_e('Readability', 'ai-seo-pro'); ?>
			<?php if ($ai_seo_pro_readability_problems > 0): ?>
				<span class="tab-badge badge-problems"><?php echo esc_html($ai_seo_pro_readability_problems); ?></span>
			<?php endif; ?>
		</button>
	</div>

	<!-- SEO Tab Content -->
	<div class="ai-seo-tab-content active" data-tab="seo">
		<!-- SEO Score -->
		<div class="seo-score-section">
			<div class="score-display">
				<div class="score-circle"
					style="border-color: <?php echo esc_attr($ai_seo_pro_score_status['color']); ?>">
					<span
						class="score-number"><?php echo esc_html($ai_seo_pro_seo_score ? $ai_seo_pro_seo_score : 0); ?></span>
					<span class="score-label">/100</span>
				</div>
				<div class="score-info">
					<h4><?php esc_html_e('SEO Score', 'ai-seo-pro'); ?></h4>
					<p class="score-status <?php echo esc_attr($ai_seo_pro_score_status['status']); ?>"
						style="color: <?php echo esc_attr($ai_seo_pro_score_status['color']); ?>">
						<?php echo esc_html($ai_seo_pro_score_status['label']); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- AI Generation Options -->
		<div class="ai-options-section">
			<label class="toggle-option">
				<input type="checkbox" name="ai_seo_auto_generate" id="ai_seo_auto_generate" value="1" <?php checked($ai_seo_pro_meta_data['auto_generate'], '1'); ?>>
				<span><?php esc_html_e('Auto-generate with AI on save', 'ai-seo-pro'); ?></span>
			</label>

			<div class="ai-generation-filters">
				<p class="filters-title"><?php esc_html_e('Select fields to generate:', 'ai-seo-pro'); ?></p>
				<div class="filter-options">
					<label>
						<input type="checkbox" id="ai_generate_title" name="ai_generate_title" value="1" checked>
						<span><?php esc_html_e('Meta Title', 'ai-seo-pro'); ?></span>
					</label>
					<label>
						<input type="checkbox" id="ai_generate_description" name="ai_generate_description" value="1"
							checked>
						<span><?php esc_html_e('Meta Description', 'ai-seo-pro'); ?></span>
					</label>
					<label>
						<input type="checkbox" id="ai_generate_keywords" name="ai_generate_keywords" value="1" checked>
						<span><?php esc_html_e('Meta Keywords', 'ai-seo-pro'); ?></span>
					</label>
				</div>
			</div>

			<button type="button" id="ai_seo_generate_now" class="button button-primary">
				<span class="dashicons dashicons-superhero"></span>
				<?php esc_html_e('Generate Now with AI', 'ai-seo-pro'); ?>
			</button>
			<span class="spinner"></span>
		</div>

		<!-- Focus Keyword -->
		<div class="meta-field">
			<label for="ai_seo_focus_keyword">
				<strong><?php esc_html_e('Focus Keyphrase', 'ai-seo-pro'); ?></strong>
			</label>
			<input type="text" id="ai_seo_focus_keyword" name="ai_seo_focus_keyword"
				value="<?php echo esc_attr($ai_seo_pro_meta_data['focus_keyword']); ?>" class="widefat"
				placeholder="<?php esc_attr_e('Enter your focus keyphrase', 'ai-seo-pro'); ?>">
			<p class="description">
				<?php esc_html_e('The main keyphrase you want this content to rank for', 'ai-seo-pro'); ?></p>
		</div>

		<!-- Meta Title -->
		<div class="meta-field">
			<label for="ai_seo_title">
				<strong><?php esc_html_e('SEO Title', 'ai-seo-pro'); ?></strong>
				<span class="character-counter" data-field="ai_seo_title" data-max="60">0 / 60</span>
			</label>
			<input type="text" id="ai_seo_title" name="ai_seo_title"
				value="<?php echo esc_attr($ai_seo_pro_meta_data['title']); ?>" class="widefat" maxlength="70"
				placeholder="<?php esc_attr_e('Enter SEO title or generate with AI', 'ai-seo-pro'); ?>">
			<p class="description"><?php esc_html_e('Recommended: 50-60 characters', 'ai-seo-pro'); ?></p>
		</div>

		<!-- Slug Preview -->
		<div class="meta-field">
			<label><strong><?php esc_html_e('Slug', 'ai-seo-pro'); ?></strong></label>
			<div class="slug-preview"><code><?php echo esc_html($post->post_name); ?></code></div>
		</div>

		<!-- Meta Description -->
		<div class="meta-field">
			<label for="ai_seo_description">
				<strong><?php esc_html_e('Meta Description', 'ai-seo-pro'); ?></strong>
				<span class="character-counter" data-field="ai_seo_description" data-max="160">0 / 160</span>
			</label>
			<textarea id="ai_seo_description" name="ai_seo_description" rows="3" class="widefat" maxlength="170"
				placeholder="<?php esc_attr_e('Enter meta description or generate with AI', 'ai-seo-pro'); ?>"><?php echo esc_textarea($ai_seo_pro_meta_data['description']); ?></textarea>
			<p class="description"><?php esc_html_e('Recommended: 150-160 characters', 'ai-seo-pro'); ?></p>
		</div>

		<!-- Keywords -->
		<div class="meta-field">
			<label for="ai_seo_keywords">
				<strong><?php esc_html_e('Meta Keywords', 'ai-seo-pro'); ?></strong>
			</label>
			<input type="text" id="ai_seo_keywords" name="ai_seo_keywords"
				value="<?php echo esc_attr($ai_seo_pro_meta_data['keywords']); ?>" class="widefat"
				placeholder="<?php esc_attr_e('keyword1, keyword2, keyword3', 'ai-seo-pro'); ?>">
			<p class="description"><?php esc_html_e('Comma-separated keywords (optional)', 'ai-seo-pro'); ?></p>
		</div>

		<!-- Google-Style Search Preview with Image -->
		<div class="search-preview-section">
			<h4><?php esc_html_e('Search Engine Preview', 'ai-seo-pro'); ?></h4>
			<div class="search-preview google-style">
				<div class="preview-content">
					<!-- Site Info Row -->
					<div class="preview-site-info">
						<?php if (!empty($ai_seo_pro_site_icon)): ?>
							<img src="<?php echo esc_url($ai_seo_pro_site_icon); ?>" alt="" class="preview-favicon">
						<?php else: ?>
							<span class="preview-favicon-default">
								<span class="dashicons dashicons-admin-site-alt3"></span>
							</span>
						<?php endif; ?>
						<div class="preview-site-details">
							<span class="preview-site-name"><?php echo esc_html(get_bloginfo('name')); ?></span>
							<span class="preview-url-text"><?php echo esc_url(get_permalink($post->ID)); ?></span>
						</div>
					</div>
					<!-- Title -->
					<div class="preview-title" id="ai_seo_preview_title">
						<?php echo esc_html($ai_seo_pro_meta_data['title'] ? $ai_seo_pro_meta_data['title'] : get_the_title($post->ID)); ?>
					</div>
					<!-- Description -->
					<div class="preview-description" id="ai_seo_preview_description">
						<?php
						$ai_seo_pro_preview_desc = $ai_seo_pro_meta_data['description'];
						if (empty($ai_seo_pro_preview_desc)) {
							$ai_seo_pro_preview_desc = $post->post_excerpt ? $post->post_excerpt : wp_trim_words(wp_strip_all_tags($post->post_content), 25, '...');
						}
						echo esc_html($ai_seo_pro_preview_desc);
						?>
					</div>
				</div>
				<!-- Preview Image -->
				<div class="preview-image-wrapper">
					<?php if (!empty($ai_seo_pro_preview_image)): ?>
						<div class="preview-image">
							<img src="<?php echo esc_url($ai_seo_pro_preview_image); ?>" alt="" id="ai_seo_preview_img">
						</div>
					<?php else: ?>
						<div class="preview-image preview-no-image">
							<span class="dashicons dashicons-format-image"></span>
							<span><?php esc_html_e('No image', 'ai-seo-pro'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<p class="preview-note">
				<span class="dashicons dashicons-info-outline"></span>
				<?php esc_html_e('This is how your page may appear in Google search results. Set a featured image to display the thumbnail.', 'ai-seo-pro'); ?>
			</p>
		</div>

		<!-- SEO Analysis Accordion -->
		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle" data-accordion="seo-analysis">
				<span class="toggle-icon dashicons dashicons-arrow-down-alt2"></span>
				<?php esc_html_e('SEO Analysis', 'ai-seo-pro'); ?>
				<span class="analysis-summary">
					<?php if ($ai_seo_pro_seo_problems > 0): ?>
						<span class="summary-problems">
							<?php
							printf(
								/* translators: %d: number of SEO problems found */
								esc_html(_n('%d problem', '%d problems', $ai_seo_pro_seo_problems, 'ai-seo-pro')),
								esc_html($ai_seo_pro_seo_problems)
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ($ai_seo_pro_seo_good > 0): ?>
						<span class="summary-good">
							<?php
							printf(
								/* translators: %d: number of good SEO results */
								esc_html(_n('%d good result', '%d good results', $ai_seo_pro_seo_good, 'ai-seo-pro')),
								esc_html($ai_seo_pro_seo_good)
							);
							?>
						</span>
					<?php endif; ?>
				</span>
			</button>
			<div class="accordion-content" id="seo-analysis" style="display: none;">
				<?php if (!empty($ai_seo_pro_seo_analysis['problems'])): ?>
					<div class="analysis-group problems-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-warning"></span>
							<?php
							/* translators: %d: number of SEO problems */
							printf(esc_html__('Problems (%d)', 'ai-seo-pro'), count($ai_seo_pro_seo_analysis['problems']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($ai_seo_pro_seo_analysis['problems'] as $ai_seo_pro_item): ?>
								<li class="analysis-item problem">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($ai_seo_pro_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($ai_seo_pro_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (!empty($ai_seo_pro_seo_analysis['good'])): ?>
					<div class="analysis-group good-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php
							/* translators: %d: number of good SEO results */
							printf(esc_html__('Good results (%d)', 'ai-seo-pro'), count($ai_seo_pro_seo_analysis['good']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($ai_seo_pro_seo_analysis['good'] as $ai_seo_pro_item): ?>
								<li class="analysis-item good">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($ai_seo_pro_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($ai_seo_pro_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (empty($ai_seo_pro_seo_analysis['problems']) && empty($ai_seo_pro_seo_analysis['good'])): ?>
					<p class="no-analysis">
						<?php esc_html_e('Enter a focus keyphrase and save the post to see SEO analysis.', 'ai-seo-pro'); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Advanced Options Accordion -->
		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle" data-accordion="advanced-options">
				<span class="toggle-icon dashicons dashicons-arrow-down-alt2"></span>
				<?php esc_html_e('Advanced Options', 'ai-seo-pro'); ?>
				<span class="analysis-summary">
					<span
						class="summary-info"><?php esc_html_e('Canonical, Robots, Social Tags', 'ai-seo-pro'); ?></span>
				</span>
			</button>
			<div class="accordion-content" id="advanced-options" style="display: none;">
				<div class="advanced-options-inner">
					<!-- Sitemap Settings -->
					<div class="meta-field sitemap-field">
						<label class="toggle-option exclude-sitemap-toggle">
							<input type="checkbox" name="ai_seo_exclude_sitemap" id="ai_seo_exclude_sitemap" value="1"
								<?php checked($ai_seo_pro_exclude_sitemap, '1'); ?>>
							<span><strong><?php esc_html_e('Exclude from XML Sitemap', 'ai-seo-pro'); ?></strong></span>
						</label>
						<p class="description" style="margin-left: 24px;">
							<?php esc_html_e('When enabled, this page will not appear in the XML sitemap.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">

					<!-- Canonical URL -->
					<div class="meta-field">
						<label
							for="ai_seo_canonical"><strong><?php esc_html_e('Canonical URL', 'ai-seo-pro'); ?></strong></label>
						<input type="url" id="ai_seo_canonical" name="ai_seo_canonical"
							value="<?php echo esc_url($ai_seo_pro_meta_data['canonical']); ?>" class="widefat"
							placeholder="<?php echo esc_url(get_permalink($post->ID)); ?>">
						<p class="description">
							<?php esc_html_e('Override the default canonical URL for this page', 'ai-seo-pro'); ?></p>
					</div>

					<!-- Robots Meta -->
					<div class="meta-field">
						<label
							for="ai_seo_robots"><strong><?php esc_html_e('Robots Meta', 'ai-seo-pro'); ?></strong></label>
						<select id="ai_seo_robots" name="ai_seo_robots" class="widefat">
							<option value=""><?php esc_html_e('Default (index, follow)', 'ai-seo-pro'); ?></option>
							<option value="noindex" <?php selected($ai_seo_pro_meta_data['robots'], 'noindex'); ?>>
								<?php esc_html_e('No Index', 'ai-seo-pro'); ?></option>
							<option value="nofollow" <?php selected($ai_seo_pro_meta_data['robots'], 'nofollow'); ?>>
								<?php esc_html_e('No Follow', 'ai-seo-pro'); ?></option>
							<option value="noindex,nofollow" <?php selected($ai_seo_pro_meta_data['robots'], 'noindex,nofollow'); ?>><?php esc_html_e('No Index, No Follow', 'ai-seo-pro'); ?>
							</option>
						</select>
					</div>

					<!-- Open Graph Title -->
					<div class="meta-field">
						<label
							for="ai_seo_og_title"><strong><?php esc_html_e('Open Graph Title', 'ai-seo-pro'); ?></strong></label>
						<input type="text" id="ai_seo_og_title" name="ai_seo_og_title"
							value="<?php echo esc_attr($ai_seo_pro_meta_data['og_title']); ?>" class="widefat"
							maxlength="60"
							placeholder="<?php esc_attr_e('Leave blank to use SEO title', 'ai-seo-pro'); ?>">
					</div>

					<!-- Open Graph Description -->
					<div class="meta-field">
						<label
							for="ai_seo_og_description"><strong><?php esc_html_e('Open Graph Description', 'ai-seo-pro'); ?></strong></label>
						<textarea id="ai_seo_og_description" name="ai_seo_og_description" rows="2" class="widefat"
							maxlength="160"
							placeholder="<?php esc_attr_e('Leave blank to use meta description', 'ai-seo-pro'); ?>"><?php echo esc_textarea($ai_seo_pro_meta_data['og_description']); ?></textarea>
					</div>

					<!-- Open Graph Image -->
					<div class="meta-field og-image-field">
						<label
							for="ai_seo_og_image"><strong><?php esc_html_e('Open Graph Image', 'ai-seo-pro'); ?></strong></label>
						<div class="og-image-upload-wrapper">
							<div class="og-image-preview" id="ai_seo_og_image_preview">
								<?php if (!empty($ai_seo_pro_og_image)): ?>
									<img src="<?php echo esc_url($ai_seo_pro_og_image); ?>" alt="">
									<button type="button" class="og-image-remove" id="ai_seo_og_image_remove"
										title="<?php esc_attr_e('Remove image', 'ai-seo-pro'); ?>">
										<span class="dashicons dashicons-no-alt"></span>
									</button>
								<?php endif; ?>
							</div>
							<input type="hidden" id="ai_seo_og_image" name="ai_seo_og_image"
								value="<?php echo esc_url($ai_seo_pro_og_image); ?>">
							<button type="button" class="button" id="ai_seo_og_image_upload">
								<span class="dashicons dashicons-upload"></span>
								<?php esc_html_e('Upload Image', 'ai-seo-pro'); ?>
							</button>
						</div>
						<p class="description">
							<?php esc_html_e('Recommended: 1200x630 pixels. Used when sharing on Facebook, LinkedIn, etc.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">

					<!-- Twitter Title -->
					<div class="meta-field">
						<label
							for="ai_seo_twitter_title"><strong><?php esc_html_e('Twitter Title', 'ai-seo-pro'); ?></strong></label>
						<input type="text" id="ai_seo_twitter_title" name="ai_seo_twitter_title"
							value="<?php echo esc_attr($ai_seo_pro_meta_data['twitter_title']); ?>" class="widefat"
							maxlength="60"
							placeholder="<?php esc_attr_e('Leave blank to use SEO title', 'ai-seo-pro'); ?>">
					</div>

					<!-- Twitter Description -->
					<div class="meta-field">
						<label
							for="ai_seo_twitter_description"><strong><?php esc_html_e('Twitter Description', 'ai-seo-pro'); ?></strong></label>
						<textarea id="ai_seo_twitter_description" name="ai_seo_twitter_description" rows="2"
							class="widefat" maxlength="160"
							placeholder="<?php esc_attr_e('Leave blank to use meta description', 'ai-seo-pro'); ?>"><?php echo esc_textarea($ai_seo_pro_meta_data['twitter_description']); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Readability Tab Content -->
	<div class="ai-seo-tab-content" data-tab="readability">
		<?php
		$ai_seo_pro_flesch_score = isset($ai_seo_pro_readability_analysis['flesch_score']) ? $ai_seo_pro_readability_analysis['flesch_score'] : 0;
		$ai_seo_pro_grade_level = isset($ai_seo_pro_readability_analysis['grade_level']) ? $ai_seo_pro_readability_analysis['grade_level'] : '';

		if ($ai_seo_pro_flesch_score >= 80) {
			$ai_seo_pro_readability_status = array('status' => 'excellent', 'label' => __('Very Easy to Read', 'ai-seo-pro'), 'color' => '#46b450');
		} elseif ($ai_seo_pro_flesch_score >= 60) {
			$ai_seo_pro_readability_status = array('status' => 'good', 'label' => __('Easy to Read', 'ai-seo-pro'), 'color' => '#46b450');
		} elseif ($ai_seo_pro_flesch_score >= 40) {
			$ai_seo_pro_readability_status = array('status' => 'moderate', 'label' => __('Fairly Readable', 'ai-seo-pro'), 'color' => '#ffb900');
		} elseif ($ai_seo_pro_flesch_score >= 20) {
			$ai_seo_pro_readability_status = array('status' => 'difficult', 'label' => __('Difficult to Read', 'ai-seo-pro'), 'color' => '#dc3232');
		} else {
			$ai_seo_pro_readability_status = array('status' => 'very-difficult', 'label' => __('Very Difficult to Read', 'ai-seo-pro'), 'color' => '#dc3232');
		}
		?>
		<div class="readability-score-section">
			<div class="score-display">
				<div class="score-circle"
					style="border-color: <?php echo esc_attr($ai_seo_pro_readability_status['color']); ?>">
					<span class="score-number"><?php echo esc_html(round($ai_seo_pro_flesch_score)); ?></span>
					<span class="score-label">/100</span>
				</div>
				<div class="score-info">
					<h4><?php esc_html_e('Readability Score', 'ai-seo-pro'); ?></h4>
					<p class="score-status"
						style="color: <?php echo esc_attr($ai_seo_pro_readability_status['color']); ?>">
						<?php echo esc_html($ai_seo_pro_readability_status['label']); ?></p>
					<?php if ($ai_seo_pro_grade_level): ?>
						<p class="grade-level"><?php echo esc_html($ai_seo_pro_grade_level); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle active" data-accordion="readability-analysis">
				<span class="toggle-icon dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e('Readability Analysis', 'ai-seo-pro'); ?>
				<span class="analysis-summary">
					<?php if ($ai_seo_pro_readability_problems > 0): ?>
						<span class="summary-problems">
							<?php
							printf(
								/* translators: %d: number of readability problems found */
								esc_html(_n('%d problem', '%d problems', $ai_seo_pro_readability_problems, 'ai-seo-pro')),
								esc_html($ai_seo_pro_readability_problems)
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ($ai_seo_pro_readability_good > 0): ?>
						<span class="summary-good">
							<?php
							printf(
								/* translators: %d: number of good readability results */
								esc_html(_n('%d good result', '%d good results', $ai_seo_pro_readability_good, 'ai-seo-pro')),
								esc_html($ai_seo_pro_readability_good)
							);
							?>
						</span>
					<?php endif; ?>
				</span>
			</button>
			<div class="accordion-content" id="readability-analysis">
				<?php if (!empty($ai_seo_pro_readability_analysis['problems'])): ?>
					<div class="analysis-group problems-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-warning"></span>
							<?php
							/* translators: %d: number of readability problems */
							printf(esc_html__('Problems (%d)', 'ai-seo-pro'), count($ai_seo_pro_readability_analysis['problems']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($ai_seo_pro_readability_analysis['problems'] as $ai_seo_pro_item): ?>
								<li class="analysis-item problem">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($ai_seo_pro_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($ai_seo_pro_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (!empty($ai_seo_pro_readability_analysis['good'])): ?>
					<div class="analysis-group good-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php
							/* translators: %d: number of good readability results */
							printf(esc_html__('Good results (%d)', 'ai-seo-pro'), count($ai_seo_pro_readability_analysis['good']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($ai_seo_pro_readability_analysis['good'] as $ai_seo_pro_item): ?>
								<li class="analysis-item good">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($ai_seo_pro_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($ai_seo_pro_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (empty($ai_seo_pro_readability_analysis['problems']) && empty($ai_seo_pro_readability_analysis['good'])): ?>
					<p class="no-analysis"><?php esc_html_e('Add content to see readability analysis.', 'ai-seo-pro'); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="readability-tips">
			<h4><?php esc_html_e('Readability Tips', 'ai-seo-pro'); ?></h4>
			<ul>
				<li><?php esc_html_e('Use short sentences (under 20 words)', 'ai-seo-pro'); ?></li>
				<li><?php esc_html_e('Break up long paragraphs', 'ai-seo-pro'); ?></li>
				<li><?php esc_html_e('Use subheadings to structure content', 'ai-seo-pro'); ?></li>
				<li><?php esc_html_e('Use transition words for better flow', 'ai-seo-pro'); ?></li>
				<li><?php esc_html_e('Avoid passive voice when possible', 'ai-seo-pro'); ?></li>
			</ul>
		</div>
	</div>
</div>

<!-- Google-Style Search Preview CSS -->
<style>
	.search-preview-section h4 {
		margin: 0 0 12px 0;
		font-size: 14px;
		font-weight: 600;
	}

	.search-preview.google-style {
		background: #202124;
		border-radius: 12px;
		padding: 16px;
		display: flex;
		gap: 16px;
		align-items: flex-start;
	}

	.search-preview .preview-content {
		flex: 1;
		min-width: 0;
	}

	.search-preview .preview-site-info {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 10px;
	}

	.search-preview .preview-favicon {
		width: 26px;
		height: 26px;
		border-radius: 50%;
		background: #303134;
		object-fit: contain;
	}

	.search-preview .preview-favicon-default {
		width: 26px;
		height: 26px;
		border-radius: 50%;
		background: #303134;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.search-preview .preview-favicon-default .dashicons {
		font-size: 14px;
		width: 14px;
		height: 14px;
		color: #9aa0a6;
	}

	.search-preview .preview-site-details {
		display: flex;
		flex-direction: column;
		gap: 2px;
		min-width: 0;
	}

	.search-preview .preview-site-name {
		color: #dadce0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.3;
	}

	.search-preview .preview-url-text {
		color: #9aa0a6;
		font-size: 12px;
		line-height: 1.3;
		word-break: break-all;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		max-width: 100%;
	}

	.search-preview .preview-title {
		color: #8ab4f8;
		font-size: 20px;
		font-weight: 400;
		line-height: 1.3;
		margin-bottom: 8px;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.search-preview .preview-title:hover {
		text-decoration: underline;
		cursor: pointer;
	}

	.search-preview .preview-description {
		color: #bdc1c6;
		font-size: 14px;
		line-height: 1.58;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.search-preview .preview-image-wrapper {
		flex-shrink: 0;
	}

	.search-preview .preview-image {
		width: 92px;
		height: 92px;
		border-radius: 8px;
		overflow: hidden;
		background: #303134;
	}

	.search-preview .preview-image img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.search-preview .preview-no-image {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		color: #5f6368;
		gap: 4px;
		font-size: 10px;
	}

	.search-preview .preview-no-image .dashicons {
		font-size: 24px;
		width: 24px;
		height: 24px;
	}

	.search-preview-section .preview-note {
		display: flex;
		align-items: center;
		gap: 6px;
		margin-top: 10px;
		font-size: 12px;
		color: #666;
		font-style: italic;
	}

	.search-preview-section .preview-note .dashicons {
		font-size: 14px;
		width: 14px;
		height: 14px;
		color: #999;
	}

	@media (max-width: 500px) {
		.search-preview.google-style {
			flex-direction: column-reverse;
		}

		.search-preview .preview-image {
			width: 100%;
			height: 120px;
		}
	}

	/* OG Image Upload Field */
	.og-image-field .og-image-upload-wrapper {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.og-image-field .og-image-preview {
		position: relative;
		max-width: 300px;
		border-radius: 6px;
		overflow: hidden;
		background: #f0f0f0;
	}

	.og-image-field .og-image-preview img {
		width: 100%;
		height: auto;
		display: block;
		max-height: 160px;
		object-fit: cover;
	}

	.og-image-field .og-image-preview:empty {
		display: none;
	}

	.og-image-field .og-image-remove {
		position: absolute;
		top: 5px;
		right: 5px;
		background: rgba(0, 0, 0, 0.7);
		border: none;
		border-radius: 50%;
		width: 24px;
		height: 24px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0;
	}

	.og-image-field .og-image-remove .dashicons {
		color: #fff;
		font-size: 16px;
		width: 16px;
		height: 16px;
	}

	.og-image-field .og-image-remove:hover {
		background: rgba(220, 50, 50, 0.9);
	}

	.og-image-field #ai_seo_og_image_upload {
		display: inline-flex;
		align-items: center;
		gap: 4px;
	}

	.og-image-field #ai_seo_og_image_upload .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
	}
</style>

<!-- OG Image Upload JavaScript -->
<script>
	jQuery(document).ready(function ($) {
		var ogImageFrame;

		// OG Image Upload Button
		$('#ai_seo_og_image_upload').on('click', function (e) {
			e.preventDefault();

			if (ogImageFrame) {
				ogImageFrame.open();
				return;
			}

			ogImageFrame = wp.media({
				title: '<?php echo esc_js(__("Select Open Graph Image", "ai-seo-pro")); ?>',
				button: {
					text: '<?php echo esc_js(__("Use this image", "ai-seo-pro")); ?>'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			ogImageFrame.on('select', function () {
				var attachment = ogImageFrame.state().get('selection').first().toJSON();
				var imageUrl = attachment.url;

				// Update hidden field
				$('#ai_seo_og_image').val(imageUrl);

				// Update preview
				$('#ai_seo_og_image_preview').html(
					'<img src="' + imageUrl + '" alt="">' +
					'<button type="button" class="og-image-remove" id="ai_seo_og_image_remove" title="<?php echo esc_js(__("Remove image", "ai-seo-pro")); ?>">' +
					'<span class="dashicons dashicons-no-alt"></span></button>'
				);

				// Update search preview
				updateSearchPreviewImage(imageUrl);
			});

			ogImageFrame.open();
		});

		// Remove OG Image
		$(document).on('click', '#ai_seo_og_image_remove', function (e) {
			e.preventDefault();
			$('#ai_seo_og_image').val('');
			$('#ai_seo_og_image_preview').empty();
			updateSearchPreviewImage('');
		});

		// Update search preview image
		function updateSearchPreviewImage(url) {
			var $wrapper = $('.search-preview .preview-image-wrapper');

			if (url) {
				$wrapper.html('<div class="preview-image"><img src="' + url + '" alt=""></div>');
			} else {
				// Check for featured image
				var $featuredImg = $('#set-post-thumbnail img, #postimagediv img');
				if ($featuredImg.length && $featuredImg.attr('src')) {
					$wrapper.html('<div class="preview-image"><img src="' + $featuredImg.attr('src') + '" alt=""></div>');
				} else {
					$wrapper.html(
						'<div class="preview-image preview-no-image">' +
						'<span class="dashicons dashicons-format-image"></span>' +
						'<span><?php echo esc_js(__("No image", "ai-seo-pro")); ?></span></div>'
					);
				}
			}
		}

		// Watch for featured image changes
		$(document).on('click', '#remove-post-thumbnail', function () {
			setTimeout(function () {
				if (!$('#ai_seo_og_image').val()) {
					updateSearchPreviewImage('');
				}
			}, 500);
		});
	});
</script>