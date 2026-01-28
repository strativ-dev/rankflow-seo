<?php
/**
 * Meta box view with tabs - SEO & Readability
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 *
 * @var WP_Post $post                            Post object
 * @var array   $rankflow_seo_meta_data            Meta data
 * @var int     $rankflow_seo_seo_score            SEO score
 * @var array   $rankflow_seo_seo_analysis         SEO analysis results
 * @var array   $rankflow_seo_readability_analysis Readability analysis results
 */

if (!defined('ABSPATH')) {
	exit;
}

// Ensure variables are defined with defaults.
if (!isset($post)) {
	$post = get_post();
}

// Handle meta_data.
if (!isset($rankflow_seo_meta_data)) {
	if (isset($meta_data)) {
		$rankflow_seo_meta_data = $meta_data;
	} else {
		$rankflow_seo_meta_data = array();
	}
}

// Ensure all keys exist with defaults.
$rankflow_seo_meta_data = wp_parse_args($rankflow_seo_meta_data, array(
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
if (!isset($rankflow_seo_seo_score)) {
	$rankflow_seo_seo_score = isset($seo_score) ? $seo_score : 0;
}

// Handle seo_analysis.
if (!isset($rankflow_seo_seo_analysis)) {
	$rankflow_seo_seo_analysis = isset($seo_analysis) ? $seo_analysis : array();
}

// Handle readability_analysis.
if (!isset($rankflow_seo_readability_analysis)) {
	$rankflow_seo_readability_analysis = isset($readability_analysis) ? $readability_analysis : array();
}

$rankflow_seo_score_status = (new RankFlow_SEO_SEO_Score())->get_score_status($rankflow_seo_seo_score);

// Count problems and good results.
$rankflow_seo_seo_problems = isset($rankflow_seo_seo_analysis['problems']) ? count($rankflow_seo_seo_analysis['problems']) : 0;
$rankflow_seo_seo_good = isset($rankflow_seo_seo_analysis['good']) ? count($rankflow_seo_seo_analysis['good']) : 0;
$rankflow_seo_readability_problems = isset($rankflow_seo_readability_analysis['problems']) ? count($rankflow_seo_readability_analysis['problems']) : 0;
$rankflow_seo_readability_good = isset($rankflow_seo_readability_analysis['good']) ? count($rankflow_seo_readability_analysis['good']) : 0;

// Get exclude from sitemap value.
$rankflow_seo_exclude_sitemap = get_post_meta($post->ID, '_rankflow_seo_exclude_sitemap', true);

// Get OG Image from post meta.
$rankflow_seo_og_image = get_post_meta($post->ID, '_rankflow_seo_og_image', true);

/**
 * Get first image from post content (works with page builders).
 *
 * @param WP_Post $rankflow_seo_post Post object.
 * @return string Image URL or empty string.
 */
function rankflow_seo_get_first_content_image($rankflow_seo_post)
{
	$rankflow_seo_content = $rankflow_seo_post->post_content;

	// Check for standard img tags in raw content.
	if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $rankflow_seo_content, $rankflow_seo_matches)) {
		return $rankflow_seo_matches[1];
	}

	// Check for Gutenberg image blocks.
	if (preg_match('/<!-- wp:image[^>]*-->.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is', $rankflow_seo_content, $rankflow_seo_matches)) {
		return $rankflow_seo_matches[1];
	}

	// Check for Gutenberg cover blocks with background image.
	if (preg_match('/<!-- wp:cover[^>]*"url":"([^"]+)"[^>]*-->/i', $rankflow_seo_content, $rankflow_seo_matches)) {
		return stripslashes($rankflow_seo_matches[1]);
	}

	// Check for image attachment class (wp-image-ID).
	if (preg_match('/wp-image-(\d+)/i', $rankflow_seo_content, $rankflow_seo_matches)) {
		$rankflow_seo_image_url = wp_get_attachment_image_url(intval($rankflow_seo_matches[1]), 'large');
		if ($rankflow_seo_image_url) {
			return $rankflow_seo_image_url;
		}
	}

	// Check for Elementor data.
	$rankflow_seo_elementor_data = get_post_meta($rankflow_seo_post->ID, '_elementor_data', true);
	if (!empty($rankflow_seo_elementor_data) && is_string($rankflow_seo_elementor_data)) {
		// Look for image URL in Elementor JSON.
		if (preg_match('/"url":\s*"(https?:[^"]+\.(?:jpg|jpeg|png|gif|webp))"/i', $rankflow_seo_elementor_data, $rankflow_seo_matches)) {
			return stripslashes($rankflow_seo_matches[1]);
		}
	}

	// Check for Divi Builder data.
	$rankflow_seo_divi_data = get_post_meta($rankflow_seo_post->ID, '_et_builder_version', true);
	if (!empty($rankflow_seo_divi_data)) {
		// Divi stores images in shortcodes, check content for src attributes.
		if (preg_match('/src=["\']([^"\']+\.(?:jpg|jpeg|png|gif|webp))["\'][^>]*>/i', $rankflow_seo_content, $rankflow_seo_matches)) {
			return $rankflow_seo_matches[1];
		}
	}

	// Check for WPBakery/Visual Composer shortcodes.
	if (preg_match('/\[vc_single_image[^\]]+image=["\']?(\d+)["\']?/i', $rankflow_seo_content, $rankflow_seo_matches)) {
		$rankflow_seo_image_url = wp_get_attachment_image_url(intval($rankflow_seo_matches[1]), 'large');
		if ($rankflow_seo_image_url) {
			return $rankflow_seo_image_url;
		}
	}

	// Check for Beaver Builder data.
	$rankflow_seo_bb_data = get_post_meta($rankflow_seo_post->ID, '_fl_builder_data', true);
	if (!empty($rankflow_seo_bb_data) && is_array($rankflow_seo_bb_data)) {
		$rankflow_seo_bb_json = wp_json_encode($rankflow_seo_bb_data);
		if (preg_match('/"photo_src":\s*"([^"]+)"/i', $rankflow_seo_bb_json, $rankflow_seo_matches)) {
			return stripslashes($rankflow_seo_matches[1]);
		}
	}

	return '';
}

// Get preview image (priority: OG Image > Featured Image > Content Image > Default OG Image).
$rankflow_seo_preview_image = '';

if (!empty($rankflow_seo_og_image)) {
	// 1. OG Image (if set)
	$rankflow_seo_preview_image = $rankflow_seo_og_image;
} elseif (has_post_thumbnail($post->ID)) {
	// 2. Featured Image
	$rankflow_seo_preview_image = get_the_post_thumbnail_url($post->ID, 'medium');
} else {
	// 3. First image from content/page builder
	$rankflow_seo_content_image = rankflow_seo_get_first_content_image($post);
	if (!empty($rankflow_seo_content_image)) {
		$rankflow_seo_preview_image = $rankflow_seo_content_image;
	} else {
		// 4. Default OG Image from settings
		$rankflow_seo_default_og = get_option('rankflow_seo_default_og_image', '');
		if (!empty($rankflow_seo_default_og)) {
			$rankflow_seo_preview_image = $rankflow_seo_default_og;
		}
	}
}

// Get site icon/favicon.
$rankflow_seo_site_icon = get_site_icon_url(16);
?>

<div class="rankflow-seo-metabox">
	<!-- Tab Navigation -->
	<div class="ai-seo-tabs-nav">
		<button type="button" class="ai-seo-tab-btn active" data-tab="seo">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e('SEO', 'rankflow-seo'); ?>
			<?php if ($rankflow_seo_seo_problems > 0): ?>
				<span class="tab-badge badge-problems"><?php echo esc_html($rankflow_seo_seo_problems); ?></span>
			<?php endif; ?>
		</button>
		<button type="button" class="ai-seo-tab-btn" data-tab="readability">
			<span class="dashicons dashicons-editor-paragraph"></span>
			<?php esc_html_e('Readability', 'rankflow-seo'); ?>
			<?php if ($rankflow_seo_readability_problems > 0): ?>
				<span class="tab-badge badge-problems"><?php echo esc_html($rankflow_seo_readability_problems); ?></span>
			<?php endif; ?>
		</button>
	</div>

	<!-- SEO Tab Content -->
	<div class="ai-seo-tab-content active" data-tab="seo">
		<!-- SEO Score -->
		<div class="seo-score-section">
			<div class="score-display">
				<div class="score-circle rankflow-seo-score-<?php echo esc_attr($rankflow_seo_score_status['status']); ?>">
					<span
						class="score-number"><?php echo esc_html($rankflow_seo_seo_score ? $rankflow_seo_seo_score : 0); ?></span>
					<span class="score-label">/100</span>
				</div>
				<div class="score-info">
					<h4><?php esc_html_e('SEO Score', 'rankflow-seo'); ?></h4>
					<p class="score-status <?php echo esc_attr($rankflow_seo_score_status['status']); ?>">
						<?php echo esc_html($rankflow_seo_score_status['label']); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- AI Generation Options -->
		<div class="ai-options-section">
			<label class="toggle-option">
				<input type="checkbox" name="rankflow_seo_auto_generate" id="rankflow_seo_auto_generate" value="1" <?php checked($rankflow_seo_meta_data['auto_generate'], '1'); ?>>
				<span><?php esc_html_e('Auto-generate with AI on save', 'rankflow-seo'); ?></span>
			</label>

			<div class="ai-generation-filters">
				<p class="filters-title"><?php esc_html_e('Select fields to generate:', 'rankflow-seo'); ?></p>
				<div class="filter-options">
					<label>
						<input type="checkbox" id="ai_generate_title" name="ai_generate_title" value="1" checked>
						<span><?php esc_html_e('Meta Title', 'rankflow-seo'); ?></span>
					</label>
					<label>
						<input type="checkbox" id="ai_generate_description" name="ai_generate_description" value="1"
							checked>
						<span><?php esc_html_e('Meta Description', 'rankflow-seo'); ?></span>
					</label>
					<label>
						<input type="checkbox" id="ai_generate_keywords" name="ai_generate_keywords" value="1" checked>
						<span><?php esc_html_e('Meta Keywords', 'rankflow-seo'); ?></span>
					</label>
				</div>
			</div>

			<button type="button" id="rankflow_seo_generate_now" class="button button-primary">
				<span class="dashicons dashicons-superhero"></span>
				<?php esc_html_e('Generate Now with AI', 'rankflow-seo'); ?>
			</button>
			<span class="spinner"></span>
		</div>

		<!-- Focus Keyword -->
		<div class="meta-field">
			<label for="rankflow_seo_focus_keyword">
				<strong><?php esc_html_e('Focus Keyphrase', 'rankflow-seo'); ?></strong>
			</label>
			<input type="text" id="rankflow_seo_focus_keyword" name="rankflow_seo_focus_keyword"
				value="<?php echo esc_attr($rankflow_seo_meta_data['focus_keyword']); ?>" class="widefat"
				placeholder="<?php esc_attr_e('Enter your focus keyphrase', 'rankflow-seo'); ?>">
			<p class="description">
				<?php esc_html_e('The main keyphrase you want this content to rank for', 'rankflow-seo'); ?>
			</p>
		</div>

		<!-- Meta Title -->
		<div class="meta-field">
			<label for="rankflow_seo_title">
				<strong><?php esc_html_e('SEO Title', 'rankflow-seo'); ?></strong>
				<span class="character-counter" data-field="rankflow_seo_title" data-max="60">0 / 60</span>
			</label>
			<input type="text" id="rankflow_seo_title" name="rankflow_seo_title"
				value="<?php echo esc_attr($rankflow_seo_meta_data['title']); ?>" class="widefat" maxlength="70"
				placeholder="<?php esc_attr_e('Enter SEO title or generate with AI', 'rankflow-seo'); ?>">
			<p class="description"><?php esc_html_e('Recommended: 50-60 characters', 'rankflow-seo'); ?></p>
		</div>

		<!-- Slug Preview -->
		<div class="meta-field">
			<label><strong><?php esc_html_e('Slug', 'rankflow-seo'); ?></strong></label>
			<div class="slug-preview"><code><?php echo esc_html($post->post_name); ?></code></div>
		</div>

		<!-- Meta Description -->
		<div class="meta-field">
			<label for="rankflow_seo_description">
				<strong><?php esc_html_e('Meta Description', 'rankflow-seo'); ?></strong>
				<span class="character-counter" data-field="rankflow_seo_description" data-max="160">0 / 160</span>
			</label>
			<textarea id="rankflow_seo_description" name="rankflow_seo_description" rows="3" class="widefat"
				maxlength="170"
				placeholder="<?php esc_attr_e('Enter meta description or generate with AI', 'rankflow-seo'); ?>"><?php echo esc_textarea($rankflow_seo_meta_data['description']); ?></textarea>
			<p class="description"><?php esc_html_e('Recommended: 150-160 characters', 'rankflow-seo'); ?></p>
		</div>

		<!-- Keywords -->
		<div class="meta-field">
			<label for="rankflow_seo_keywords">
				<strong><?php esc_html_e('Meta Keywords', 'rankflow-seo'); ?></strong>
			</label>
			<input type="text" id="rankflow_seo_keywords" name="rankflow_seo_keywords"
				value="<?php echo esc_attr($rankflow_seo_meta_data['keywords']); ?>" class="widefat"
				placeholder="<?php esc_attr_e('keyword1, keyword2, keyword3', 'rankflow-seo'); ?>">
			<p class="description"><?php esc_html_e('Comma-separated keywords (optional)', 'rankflow-seo'); ?></p>
		</div>

		<!-- Google-Style Search Preview with Image -->
		<div class="search-preview-section">
			<h4><?php esc_html_e('Search Engine Preview', 'rankflow-seo'); ?></h4>
			<div class="search-preview google-style">
				<div class="preview-content">
					<!-- Site Info Row -->
					<div class="preview-site-info">
						<?php if (!empty($rankflow_seo_site_icon)): ?>
							<img src="<?php echo esc_url($rankflow_seo_site_icon); ?>" alt="" class="preview-favicon">
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
					<div class="preview-title" id="rankflow_seo_preview_title">
						<?php echo esc_html($rankflow_seo_meta_data['title'] ? $rankflow_seo_meta_data['title'] : get_the_title($post->ID)); ?>
					</div>
					<!-- Description -->
					<div class="preview-description" id="rankflow_seo_preview_description">
						<?php
						$rankflow_seo_preview_desc = $rankflow_seo_meta_data['description'];
						if (empty($rankflow_seo_preview_desc)) {
							$rankflow_seo_preview_desc = $post->post_excerpt ? $post->post_excerpt : wp_trim_words(wp_strip_all_tags($post->post_content), 25, '...');
						}
						echo esc_html($rankflow_seo_preview_desc);
						?>
					</div>
				</div>
				<!-- Preview Image -->
				<div class="preview-image-wrapper">
					<?php if (!empty($rankflow_seo_preview_image)): ?>
						<div class="preview-image">
							<img src="<?php echo esc_url($rankflow_seo_preview_image); ?>" alt=""
								id="rankflow_seo_preview_img">
						</div>
					<?php else: ?>
						<div class="preview-image preview-no-image">
							<span class="dashicons dashicons-format-image"></span>
							<span><?php esc_html_e('No image', 'rankflow-seo'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<p class="preview-note">
				<span class="dashicons dashicons-info-outline"></span>
				<?php esc_html_e('This is how your page may appear in Google search results. Set a featured image to display the thumbnail.', 'rankflow-seo'); ?>
			</p>
		</div>

		<!-- SEO Analysis Accordion -->
		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle" data-accordion="seo-analysis">
				<span class="toggle-icon dashicons dashicons-arrow-down-alt2"></span>
				<?php esc_html_e('SEO Analysis', 'rankflow-seo'); ?>
				<span class="analysis-summary">
					<?php if ($rankflow_seo_seo_problems > 0): ?>
						<span class="summary-problems">
							<?php
							printf(
								/* translators: %d: number of SEO problems found */
								esc_html(_n('%d problem', '%d problems', $rankflow_seo_seo_problems, 'rankflow-seo')),
								esc_html($rankflow_seo_seo_problems)
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ($rankflow_seo_seo_good > 0): ?>
						<span class="summary-good">
							<?php
							printf(
								/* translators: %d: number of good SEO results */
								esc_html(_n('%d good result', '%d good results', $rankflow_seo_seo_good, 'rankflow-seo')),
								esc_html($rankflow_seo_seo_good)
							);
							?>
						</span>
					<?php endif; ?>
				</span>
			</button>
			<div class="accordion-content rankflow-seo-accordion-content" id="seo-analysis">
				<?php if (!empty($rankflow_seo_seo_analysis['problems'])): ?>
					<div class="analysis-group problems-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-warning"></span>
							<?php
							/* translators: %d: number of SEO problems */
							printf(esc_html__('Problems (%d)', 'rankflow-seo'), count($rankflow_seo_seo_analysis['problems']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($rankflow_seo_seo_analysis['problems'] as $rankflow_seo_item): ?>
								<li class="analysis-item problem">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($rankflow_seo_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($rankflow_seo_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (!empty($rankflow_seo_seo_analysis['good'])): ?>
					<div class="analysis-group good-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php
							/* translators: %d: number of good SEO results */
							printf(esc_html__('Good results (%d)', 'rankflow-seo'), count($rankflow_seo_seo_analysis['good']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($rankflow_seo_seo_analysis['good'] as $rankflow_seo_item): ?>
								<li class="analysis-item good">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($rankflow_seo_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($rankflow_seo_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (empty($rankflow_seo_seo_analysis['problems']) && empty($rankflow_seo_seo_analysis['good'])): ?>
					<p class="no-analysis">
						<?php esc_html_e('Enter a focus keyphrase and save the post to see SEO analysis.', 'rankflow-seo'); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Advanced Options Accordion -->
		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle" data-accordion="advanced-options">
				<span class="toggle-icon dashicons dashicons-arrow-down-alt2"></span>
				<?php esc_html_e('Advanced Options', 'rankflow-seo'); ?>
				<span class="analysis-summary">
					<span
						class="summary-info"><?php esc_html_e('Canonical, Robots, Social Tags', 'rankflow-seo'); ?></span>
				</span>
			</button>
			<div class="accordion-content rankflow-seo-accordion-content" id="advanced-options">
				<div class="advanced-options-inner">
					<!-- Sitemap Settings -->
					<div class="meta-field sitemap-field">
						<label class="toggle-option exclude-sitemap-toggle">
							<input type="checkbox" name="rankflow_seo_exclude_sitemap" id="rankflow_seo_exclude_sitemap"
								value="1" <?php checked($rankflow_seo_exclude_sitemap, '1'); ?>>
							<span><strong><?php esc_html_e('Exclude from XML Sitemap', 'rankflow-seo'); ?></strong></span>
						</label>
						<p class="description rankflow-seo-ml-24">
							<?php esc_html_e('When enabled, this page will not appear in the XML sitemap.', 'rankflow-seo'); ?>
						</p>
					</div>

					<hr class="rankflow-seo-divider">

					<!-- Canonical URL -->
					<div class="meta-field">
						<label
							for="rankflow_seo_canonical"><strong><?php esc_html_e('Canonical URL', 'rankflow-seo'); ?></strong></label>
						<input type="url" id="rankflow_seo_canonical" name="rankflow_seo_canonical"
							value="<?php echo esc_url($rankflow_seo_meta_data['canonical']); ?>" class="widefat"
							placeholder="<?php echo esc_url(get_permalink($post->ID)); ?>">
						<p class="description">
							<?php esc_html_e('Override the default canonical URL for this page', 'rankflow-seo'); ?>
						</p>
					</div>

					<!-- Robots Meta -->
					<div class="meta-field">
						<label
							for="rankflow_seo_robots"><strong><?php esc_html_e('Robots Meta', 'rankflow-seo'); ?></strong></label>
						<select id="rankflow_seo_robots" name="rankflow_seo_robots" class="widefat">
							<option value=""><?php esc_html_e('Default (index, follow)', 'rankflow-seo'); ?></option>
							<option value="noindex" <?php selected($rankflow_seo_meta_data['robots'], 'noindex'); ?>>
								<?php esc_html_e('No Index', 'rankflow-seo'); ?>
							</option>
							<option value="nofollow" <?php selected($rankflow_seo_meta_data['robots'], 'nofollow'); ?>>
								<?php esc_html_e('No Follow', 'rankflow-seo'); ?>
							</option>
							<option value="noindex,nofollow" <?php selected($rankflow_seo_meta_data['robots'], 'noindex,nofollow'); ?>><?php esc_html_e('No Index, No Follow', 'rankflow-seo'); ?>
							</option>
						</select>
					</div>

					<!-- Open Graph Title -->
					<div class="meta-field">
						<label
							for="rankflow_seo_og_title"><strong><?php esc_html_e('Open Graph Title', 'rankflow-seo'); ?></strong></label>
						<input type="text" id="rankflow_seo_og_title" name="rankflow_seo_og_title"
							value="<?php echo esc_attr($rankflow_seo_meta_data['og_title']); ?>" class="widefat"
							maxlength="60"
							placeholder="<?php esc_attr_e('Leave blank to use SEO title', 'rankflow-seo'); ?>">
					</div>

					<!-- Open Graph Description -->
					<div class="meta-field">
						<label
							for="rankflow_seo_og_description"><strong><?php esc_html_e('Open Graph Description', 'rankflow-seo'); ?></strong></label>
						<textarea id="rankflow_seo_og_description" name="rankflow_seo_og_description" rows="2"
							class="widefat" maxlength="160"
							placeholder="<?php esc_attr_e('Leave blank to use meta description', 'rankflow-seo'); ?>"><?php echo esc_textarea($rankflow_seo_meta_data['og_description']); ?></textarea>
					</div>

					<!-- Open Graph Image -->
					<div class="meta-field og-image-field">
						<label
							for="rankflow_seo_og_image"><strong><?php esc_html_e('Open Graph Image', 'rankflow-seo'); ?></strong></label>
						<div class="og-image-upload-wrapper">
							<div class="og-image-preview" id="rankflow_seo_og_image_preview">
								<?php if (!empty($rankflow_seo_og_image)): ?>
									<img src="<?php echo esc_url($rankflow_seo_og_image); ?>" alt="">
									<button type="button" class="og-image-remove" id="rankflow_seo_og_image_remove"
										title="<?php esc_attr_e('Remove image', 'rankflow-seo'); ?>">
										<span class="dashicons dashicons-no-alt"></span>
									</button>
								<?php endif; ?>
							</div>
							<input type="hidden" id="rankflow_seo_og_image" name="rankflow_seo_og_image"
								value="<?php echo esc_url($rankflow_seo_og_image); ?>">
							<button type="button" class="button" id="rankflow_seo_og_image_upload">
								<span class="dashicons dashicons-upload"></span>
								<?php esc_html_e('Upload Image', 'rankflow-seo'); ?>
							</button>
						</div>
						<p class="description">
							<?php esc_html_e('Recommended: 1200x630 pixels. Used when sharing on Facebook, LinkedIn, etc.', 'rankflow-seo'); ?>
						</p>
					</div>

					<hr class="rankflow-seo-divider">

					<!-- Twitter Title -->
					<div class="meta-field">
						<label
							for="rankflow_seo_twitter_title"><strong><?php esc_html_e('Twitter Title', 'rankflow-seo'); ?></strong></label>
						<input type="text" id="rankflow_seo_twitter_title" name="rankflow_seo_twitter_title"
							value="<?php echo esc_attr($rankflow_seo_meta_data['twitter_title']); ?>" class="widefat"
							maxlength="60"
							placeholder="<?php esc_attr_e('Leave blank to use SEO title', 'rankflow-seo'); ?>">
					</div>

					<!-- Twitter Description -->
					<div class="meta-field">
						<label
							for="rankflow_seo_twitter_description"><strong><?php esc_html_e('Twitter Description', 'rankflow-seo'); ?></strong></label>
						<textarea id="rankflow_seo_twitter_description" name="rankflow_seo_twitter_description" rows="2"
							class="widefat" maxlength="160"
							placeholder="<?php esc_attr_e('Leave blank to use meta description', 'rankflow-seo'); ?>"><?php echo esc_textarea($rankflow_seo_meta_data['twitter_description']); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Readability Tab Content -->
	<div class="ai-seo-tab-content" data-tab="readability">
		<?php
		$rankflow_seo_flesch_score = isset($rankflow_seo_readability_analysis['flesch_score']) ? $rankflow_seo_readability_analysis['flesch_score'] : 0;
		$rankflow_seo_grade_level = isset($rankflow_seo_readability_analysis['grade_level']) ? $rankflow_seo_readability_analysis['grade_level'] : '';

		if ($rankflow_seo_flesch_score >= 80) {
			$rankflow_seo_readability_status = array('status' => 'excellent', 'label' => __('Very Easy to Read', 'rankflow-seo'), 'color' => '#46b450');
		} elseif ($rankflow_seo_flesch_score >= 60) {
			$rankflow_seo_readability_status = array('status' => 'good', 'label' => __('Easy to Read', 'rankflow-seo'), 'color' => '#46b450');
		} elseif ($rankflow_seo_flesch_score >= 40) {
			$rankflow_seo_readability_status = array('status' => 'moderate', 'label' => __('Fairly Readable', 'rankflow-seo'), 'color' => '#ffb900');
		} elseif ($rankflow_seo_flesch_score >= 20) {
			$rankflow_seo_readability_status = array('status' => 'difficult', 'label' => __('Difficult to Read', 'rankflow-seo'), 'color' => '#dc3232');
		} else {
			$rankflow_seo_readability_status = array('status' => 'very-difficult', 'label' => __('Very Difficult to Read', 'rankflow-seo'), 'color' => '#dc3232');
		}
		?>
		<div class="readability-score-section">
			<div class="score-display">
				<div class="score-circle rankflow-seo-score-<?php echo esc_attr($rankflow_seo_readability_status['status']); ?>">
					<span class="score-number"><?php echo esc_html(round($rankflow_seo_flesch_score)); ?></span>
					<span class="score-label">/100</span>
				</div>
				<div class="score-info">
					<h4><?php esc_html_e('Readability Score', 'rankflow-seo'); ?></h4>
					<p class="score-status <?php echo esc_attr($rankflow_seo_readability_status['status']); ?>">
						<?php echo esc_html($rankflow_seo_readability_status['label']); ?>
					</p>
					<?php if ($rankflow_seo_grade_level): ?>
						<p class="grade-level"><?php echo esc_html($rankflow_seo_grade_level); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="analysis-accordion">
			<button type="button" class="accordion-toggle active" data-accordion="readability-analysis">
				<span class="toggle-icon dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e('Readability Analysis', 'rankflow-seo'); ?>
				<span class="analysis-summary">
					<?php if ($rankflow_seo_readability_problems > 0): ?>
						<span class="summary-problems">
							<?php
							printf(
								/* translators: %d: number of readability problems found */
								esc_html(_n('%d problem', '%d problems', $rankflow_seo_readability_problems, 'rankflow-seo')),
								esc_html($rankflow_seo_readability_problems)
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ($rankflow_seo_readability_good > 0): ?>
						<span class="summary-good">
							<?php
							printf(
								/* translators: %d: number of good readability results */
								esc_html(_n('%d good result', '%d good results', $rankflow_seo_readability_good, 'rankflow-seo')),
								esc_html($rankflow_seo_readability_good)
							);
							?>
						</span>
					<?php endif; ?>
				</span>
			</button>
			<div class="accordion-content" id="readability-analysis">
				<?php if (!empty($rankflow_seo_readability_analysis['problems'])): ?>
					<div class="analysis-group problems-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-warning"></span>
							<?php
							/* translators: %d: number of readability problems */
							printf(esc_html__('Problems (%d)', 'rankflow-seo'), count($rankflow_seo_readability_analysis['problems']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($rankflow_seo_readability_analysis['problems'] as $rankflow_seo_item): ?>
								<li class="analysis-item problem">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($rankflow_seo_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($rankflow_seo_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (!empty($rankflow_seo_readability_analysis['good'])): ?>
					<div class="analysis-group good-group">
						<h5 class="group-title">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php
							/* translators: %d: number of good readability results */
							printf(esc_html__('Good results (%d)', 'rankflow-seo'), count($rankflow_seo_readability_analysis['good']));
							?>
						</h5>
						<ul class="analysis-list">
							<?php foreach ($rankflow_seo_readability_analysis['good'] as $rankflow_seo_item): ?>
								<li class="analysis-item good">
									<span class="item-indicator"></span>
									<span class="item-title"><?php echo esc_html($rankflow_seo_item['title']); ?>:</span>
									<span class="item-message"><?php echo esc_html($rankflow_seo_item['message']); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (empty($rankflow_seo_readability_analysis['problems']) && empty($rankflow_seo_readability_analysis['good'])): ?>
					<p class="no-analysis"><?php esc_html_e('Add content to see readability analysis.', 'rankflow-seo'); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="readability-tips">
			<h4><?php esc_html_e('Readability Tips', 'rankflow-seo'); ?></h4>
			<ul>
				<li><?php esc_html_e('Use short sentences (under 20 words)', 'rankflow-seo'); ?></li>
				<li><?php esc_html_e('Break up long paragraphs', 'rankflow-seo'); ?></li>
				<li><?php esc_html_e('Use subheadings to structure content', 'rankflow-seo'); ?></li>
				<li><?php esc_html_e('Use transition words for better flow', 'rankflow-seo'); ?></li>
				<li><?php esc_html_e('Avoid passive voice when possible', 'rankflow-seo'); ?></li>
			</ul>
		</div>
	</div>
</div>

<!-- Google-Style Search Preview CSS -->
