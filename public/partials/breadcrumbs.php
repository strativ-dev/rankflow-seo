<?php
/**
 * Breadcrumbs output.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (is_front_page()) {
	return;
}

$rankflow_seo_breadcrumb_items = array();

// Home.
$rankflow_seo_breadcrumb_items[] = array(
	'title' => __('Home', 'rankflow-seo'),
	'url' => home_url(),
);

// Category for posts.
if ('post' === $post->post_type) {
	$rankflow_seo_categories = get_the_category($post->ID);
	if (!empty($rankflow_seo_categories)) {
		$rankflow_seo_category = $rankflow_seo_categories[0];
		$rankflow_seo_breadcrumb_items[] = array(
			'title' => $rankflow_seo_category->name,
			'url' => get_category_link($rankflow_seo_category->term_id),
		);
	}
}

// Parent pages.
if ($post->post_parent) {
	$rankflow_seo_parent_id = $post->post_parent;
	$rankflow_seo_parent_crumbs = array();

	while ($rankflow_seo_parent_id) {
		$rankflow_seo_parent_page = get_post($rankflow_seo_parent_id);
		$rankflow_seo_parent_crumbs[] = array(
			'title' => get_the_title($rankflow_seo_parent_page),
			'url' => get_permalink($rankflow_seo_parent_page),
		);
		$rankflow_seo_parent_id = $rankflow_seo_parent_page->post_parent;
	}

	$rankflow_seo_breadcrumb_items = array_merge($rankflow_seo_breadcrumb_items, array_reverse($rankflow_seo_parent_crumbs));
}

// Current page.
$rankflow_seo_breadcrumb_items[] = array(
	'title' => get_the_title($post->ID),
	'url' => '',
);
?>

<nav class="rankflow-seo-breadcrumbs" aria-label="Breadcrumb">
	<?php foreach ($rankflow_seo_breadcrumb_items as $rankflow_seo_index => $rankflow_seo_item): ?>
		<?php if ($rankflow_seo_index > 0): ?>
			<span class="separator">/</span>
		<?php endif; ?>

		<?php if (!empty($rankflow_seo_item['url'])): ?>
			<a href="<?php echo esc_url($rankflow_seo_item['url']); ?>">
				<?php echo esc_html($rankflow_seo_item['title']); ?>
			</a>
		<?php else: ?>
			<span class="current"><?php echo esc_html($rankflow_seo_item['title']); ?></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>