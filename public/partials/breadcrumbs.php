<?php
/**
 * Breadcrumbs output.
 *
 * @package    MPSEO
 * @subpackage MPSEO/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (is_front_page()) {
	return;
}

$mpseo_breadcrumb_items = array();

// Home.
$mpseo_breadcrumb_items[] = array(
	'title' => __('Home', 'metapilot-smart-seo'),
	'url' => home_url(),
);

// Category for posts.
if ('post' === $post->post_type) {
	$mpseo_categories = get_the_category($post->ID);
	if (!empty($mpseo_categories)) {
		$mpseo_category = $mpseo_categories[0];
		$mpseo_breadcrumb_items[] = array(
			'title' => $mpseo_category->name,
			'url' => get_category_link($mpseo_category->term_id),
		);
	}
}

// Parent pages.
if ($post->post_parent) {
	$mpseo_parent_id = $post->post_parent;
	$mpseo_parent_crumbs = array();

	while ($mpseo_parent_id) {
		$mpseo_parent_page = get_post($mpseo_parent_id);
		$mpseo_parent_crumbs[] = array(
			'title' => get_the_title($mpseo_parent_page),
			'url' => get_permalink($mpseo_parent_page),
		);
		$mpseo_parent_id = $mpseo_parent_page->post_parent;
	}

	$mpseo_breadcrumb_items = array_merge($mpseo_breadcrumb_items, array_reverse($mpseo_parent_crumbs));
}

// Current page.
$mpseo_breadcrumb_items[] = array(
	'title' => get_the_title($post->ID),
	'url' => '',
);
?>

<nav class="mpseo-breadcrumbs" aria-label="Breadcrumb">
	<?php foreach ($mpseo_breadcrumb_items as $mpseo_index => $mpseo_item): ?>
		<?php if ($mpseo_index > 0): ?>
			<span class="separator">/</span>
		<?php endif; ?>

		<?php if (!empty($mpseo_item['url'])): ?>
			<a href="<?php echo esc_url($mpseo_item['url']); ?>">
				<?php echo esc_html($mpseo_item['title']); ?>
			</a>
		<?php else: ?>
			<span class="current"><?php echo esc_html($mpseo_item['title']); ?></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>