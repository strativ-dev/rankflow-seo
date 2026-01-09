<?php
/**
 * Breadcrumbs output.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (is_front_page()) {
	return;
}

$ai_seo_pro_breadcrumb_items = array();

// Home.
$ai_seo_pro_breadcrumb_items[] = array(
	'title' => __('Home', 'ai-seo-pro'),
	'url' => home_url(),
);

// Category for posts.
if ('post' === $post->post_type) {
	$ai_seo_pro_categories = get_the_category($post->ID);
	if (!empty($ai_seo_pro_categories)) {
		$ai_seo_pro_category = $ai_seo_pro_categories[0];
		$ai_seo_pro_breadcrumb_items[] = array(
			'title' => $ai_seo_pro_category->name,
			'url' => get_category_link($ai_seo_pro_category->term_id),
		);
	}
}

// Parent pages.
if ($post->post_parent) {
	$ai_seo_pro_parent_id = $post->post_parent;
	$ai_seo_pro_parent_crumbs = array();

	while ($ai_seo_pro_parent_id) {
		$ai_seo_pro_parent_page = get_post($ai_seo_pro_parent_id);
		$ai_seo_pro_parent_crumbs[] = array(
			'title' => get_the_title($ai_seo_pro_parent_page),
			'url' => get_permalink($ai_seo_pro_parent_page),
		);
		$ai_seo_pro_parent_id = $ai_seo_pro_parent_page->post_parent;
	}

	$ai_seo_pro_breadcrumb_items = array_merge($ai_seo_pro_breadcrumb_items, array_reverse($ai_seo_pro_parent_crumbs));
}

// Current page.
$ai_seo_pro_breadcrumb_items[] = array(
	'title' => get_the_title($post->ID),
	'url' => '',
);
?>

<nav class="ai-seo-breadcrumbs" aria-label="Breadcrumb">
	<?php foreach ($ai_seo_pro_breadcrumb_items as $ai_seo_pro_index => $ai_seo_pro_item): ?>
		<?php if ($ai_seo_pro_index > 0): ?>
			<span class="separator">/</span>
		<?php endif; ?>

		<?php if (!empty($ai_seo_pro_item['url'])): ?>
			<a href="<?php echo esc_url($ai_seo_pro_item['url']); ?>">
				<?php echo esc_html($ai_seo_pro_item['title']); ?>
			</a>
		<?php else: ?>
			<span class="current"><?php echo esc_html($ai_seo_pro_item['title']); ?></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>