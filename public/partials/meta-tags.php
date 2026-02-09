<?php
/**
 * Meta tags output.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/public/partials
 *
 * @var WP_Post $post                  Post object.
 * @var array   $rankflow_seo_meta_data  Meta data.
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get meta values with fallbacks.
$rankflow_seo_meta_title = !empty($rankflow_seo_meta_data['title'])
	? $rankflow_seo_meta_data['title']
	: $this->meta_manager->get_title($post->ID);

$rankflow_seo_meta_description = !empty($rankflow_seo_meta_data['description'])
	? $rankflow_seo_meta_data['description']
	: $this->meta_manager->get_description($post->ID);

$rankflow_seo_meta_keywords = $rankflow_seo_meta_data['keywords'];
$rankflow_seo_canonical_url = !empty($rankflow_seo_meta_data['canonical'])
	? $rankflow_seo_meta_data['canonical']
	: get_permalink($post->ID);

$rankflow_seo_robots = $rankflow_seo_meta_data['robots'];

// Open Graph.
$rankflow_seo_og_title = !empty($rankflow_seo_meta_data['og_title'])
	? $rankflow_seo_meta_data['og_title']
	: $rankflow_seo_meta_title;

$rankflow_seo_og_description = !empty($rankflow_seo_meta_data['og_description'])
	? $rankflow_seo_meta_data['og_description']
	: $rankflow_seo_meta_description;

$rankflow_seo_og_image = has_post_thumbnail($post->ID)
	? get_the_post_thumbnail_url($post->ID, 'large')
	: get_option('rankflow_seo_default_og_image', '');

// Twitter Card.
$rankflow_seo_twitter_title = !empty($rankflow_seo_meta_data['twitter_title'])
	? $rankflow_seo_meta_data['twitter_title']
	: $rankflow_seo_meta_title;

$rankflow_seo_twitter_description = !empty($rankflow_seo_meta_data['twitter_description'])
	? $rankflow_seo_meta_data['twitter_description']
	: $rankflow_seo_meta_description;

$rankflow_seo_twitter_username = get_option('rankflow_seo_twitter_username', '');
?>

<!-- RankFlow SEO Meta Tags -->

<?php if (!empty($rankflow_seo_meta_description)): ?>
	<meta name="description" content="<?php echo esc_attr($rankflow_seo_meta_description); ?>">
<?php endif; ?>

<?php if (!empty($rankflow_seo_meta_keywords)): ?>
	<meta name="keywords" content="<?php echo esc_attr($rankflow_seo_meta_keywords); ?>">
<?php endif; ?>

<?php if (!empty($rankflow_seo_robots)): ?>
	<meta name="robots" content="<?php echo esc_attr($rankflow_seo_robots); ?>">
<?php endif; ?>

<link rel="canonical" href="<?php echo esc_url($rankflow_seo_canonical_url); ?>">

<?php if (get_option('rankflow_seo_og_tags', true)): ?>
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="<?php echo esc_attr(is_front_page() ? 'website' : 'article'); ?>">
	<meta property="og:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta property="og:title" content="<?php echo esc_attr($rankflow_seo_og_title); ?>">
	<meta property="og:description" content="<?php echo esc_attr($rankflow_seo_og_description); ?>">
	<?php if (!empty($rankflow_seo_og_image)): ?>
		<meta property="og:image" content="<?php echo esc_url($rankflow_seo_og_image); ?>">
	<?php endif; ?>
	<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
	<meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">
	<?php if (!is_front_page()): ?>
		<meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c', $post->ID)); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c', $post->ID)); ?>">
	<?php endif; ?>
<?php endif; ?>

<?php if (get_option('rankflow_seo_twitter_cards', true)): ?>
	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta name="twitter:title" content="<?php echo esc_attr($rankflow_seo_twitter_title); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr($rankflow_seo_twitter_description); ?>">
	<?php if (!empty($rankflow_seo_og_image)): ?>
		<meta name="twitter:image" content="<?php echo esc_url($rankflow_seo_og_image); ?>">
	<?php endif; ?>
	<?php if (!empty($rankflow_seo_twitter_username)): ?>
		<meta name="twitter:site" content="<?php echo esc_attr($rankflow_seo_twitter_username); ?>">
		<meta name="twitter:creator" content="<?php echo esc_attr($rankflow_seo_twitter_username); ?>">
	<?php endif; ?>
<?php endif; ?>

<!-- /RankFlow SEO Meta Tags -->