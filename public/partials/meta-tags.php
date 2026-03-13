<?php
/**
 * Meta tags output.
 *
 * @package    MPSEO
 * @subpackage MPSEO/public/partials
 *
 * @var WP_Post $post                  Post object.
 * @var array   $mpseo_meta_data  Meta data.
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get meta values with fallbacks.
$mpseo_meta_title = !empty($mpseo_meta_data['title'])
	? $mpseo_meta_data['title']
	: $this->meta_manager->get_title($post->ID);

$mpseo_meta_description = !empty($mpseo_meta_data['description'])
	? $mpseo_meta_data['description']
	: $this->meta_manager->get_description($post->ID);

$mpseo_meta_keywords = $mpseo_meta_data['keywords'];
$mpseo_canonical_url = !empty($mpseo_meta_data['canonical'])
	? $mpseo_meta_data['canonical']
	: get_permalink($post->ID);

$mpseo_robots = $mpseo_meta_data['robots'];

// Open Graph.
$mpseo_og_title = !empty($mpseo_meta_data['og_title'])
	? $mpseo_meta_data['og_title']
	: $mpseo_meta_title;

$mpseo_og_description = !empty($mpseo_meta_data['og_description'])
	? $mpseo_meta_data['og_description']
	: $mpseo_meta_description;

$mpseo_og_image = has_post_thumbnail($post->ID)
	? get_the_post_thumbnail_url($post->ID, 'large')
	: get_option('mpseo_default_og_image', '');

// Twitter Card.
$mpseo_twitter_title = !empty($mpseo_meta_data['twitter_title'])
	? $mpseo_meta_data['twitter_title']
	: $mpseo_meta_title;

$mpseo_twitter_description = !empty($mpseo_meta_data['twitter_description'])
	? $mpseo_meta_data['twitter_description']
	: $mpseo_meta_description;

$mpseo_twitter_username = get_option('mpseo_twitter_username', '');
?>

<!-- Metapilot Smart SEO Meta Tags -->

<?php if (!empty($mpseo_meta_description)): ?>
	<meta name="description" content="<?php echo esc_attr($mpseo_meta_description); ?>">
<?php endif; ?>

<?php if (!empty($mpseo_meta_keywords)): ?>
	<meta name="keywords" content="<?php echo esc_attr($mpseo_meta_keywords); ?>">
<?php endif; ?>

<?php if (!empty($mpseo_robots)): ?>
	<meta name="robots" content="<?php echo esc_attr($mpseo_robots); ?>">
<?php endif; ?>

<link rel="canonical" href="<?php echo esc_url($mpseo_canonical_url); ?>">

<?php if (get_option('mpseo_og_tags', true)): ?>
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="<?php echo esc_attr(is_front_page() ? 'website' : 'article'); ?>">
	<meta property="og:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta property="og:title" content="<?php echo esc_attr($mpseo_og_title); ?>">
	<meta property="og:description" content="<?php echo esc_attr($mpseo_og_description); ?>">
	<?php if (!empty($mpseo_og_image)): ?>
		<meta property="og:image" content="<?php echo esc_url($mpseo_og_image); ?>">
	<?php endif; ?>
	<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
	<meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">
	<?php if (!is_front_page()): ?>
		<meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c', $post->ID)); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c', $post->ID)); ?>">
	<?php endif; ?>
<?php endif; ?>

<?php if (get_option('mpseo_twitter_cards', true)): ?>
	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta name="twitter:title" content="<?php echo esc_attr($mpseo_twitter_title); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr($mpseo_twitter_description); ?>">
	<?php if (!empty($mpseo_og_image)): ?>
		<meta name="twitter:image" content="<?php echo esc_url($mpseo_og_image); ?>">
	<?php endif; ?>
	<?php if (!empty($mpseo_twitter_username)): ?>
		<meta name="twitter:site" content="<?php echo esc_attr($mpseo_twitter_username); ?>">
		<meta name="twitter:creator" content="<?php echo esc_attr($mpseo_twitter_username); ?>">
	<?php endif; ?>
<?php endif; ?>

<!-- /Metapilot Smart SEO Meta Tags -->