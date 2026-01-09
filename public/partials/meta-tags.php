<?php
/**
 * Meta tags output.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/public/partials
 *
 * @var WP_Post $post                  Post object.
 * @var array   $ai_seo_pro_meta_data  Meta data.
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get meta values with fallbacks.
$ai_seo_pro_meta_title = !empty($ai_seo_pro_meta_data['title'])
	? $ai_seo_pro_meta_data['title']
	: $this->meta_manager->get_title($post->ID);

$ai_seo_pro_meta_description = !empty($ai_seo_pro_meta_data['description'])
	? $ai_seo_pro_meta_data['description']
	: $this->meta_manager->get_description($post->ID);

$ai_seo_pro_meta_keywords = $ai_seo_pro_meta_data['keywords'];
$ai_seo_pro_canonical_url = !empty($ai_seo_pro_meta_data['canonical'])
	? $ai_seo_pro_meta_data['canonical']
	: get_permalink($post->ID);

$ai_seo_pro_robots = $ai_seo_pro_meta_data['robots'];

// Open Graph.
$ai_seo_pro_og_title = !empty($ai_seo_pro_meta_data['og_title'])
	? $ai_seo_pro_meta_data['og_title']
	: $ai_seo_pro_meta_title;

$ai_seo_pro_og_description = !empty($ai_seo_pro_meta_data['og_description'])
	? $ai_seo_pro_meta_data['og_description']
	: $ai_seo_pro_meta_description;

$ai_seo_pro_og_image = has_post_thumbnail($post->ID)
	? get_the_post_thumbnail_url($post->ID, 'large')
	: get_option('ai_seo_pro_default_og_image', '');

// Twitter Card.
$ai_seo_pro_twitter_title = !empty($ai_seo_pro_meta_data['twitter_title'])
	? $ai_seo_pro_meta_data['twitter_title']
	: $ai_seo_pro_meta_title;

$ai_seo_pro_twitter_description = !empty($ai_seo_pro_meta_data['twitter_description'])
	? $ai_seo_pro_meta_data['twitter_description']
	: $ai_seo_pro_meta_description;

$ai_seo_pro_twitter_username = get_option('ai_seo_pro_twitter_username', '');
?>

<!-- AI SEO Pro Meta Tags -->

<?php if (!empty($ai_seo_pro_meta_description)): ?>
	<meta name="description" content="<?php echo esc_attr($ai_seo_pro_meta_description); ?>">
<?php endif; ?>

<?php if (!empty($ai_seo_pro_meta_keywords)): ?>
	<meta name="keywords" content="<?php echo esc_attr($ai_seo_pro_meta_keywords); ?>">
<?php endif; ?>

<?php if (!empty($ai_seo_pro_robots)): ?>
	<meta name="robots" content="<?php echo esc_attr($ai_seo_pro_robots); ?>">
<?php endif; ?>

<link rel="canonical" href="<?php echo esc_url($ai_seo_pro_canonical_url); ?>">

<?php if (get_option('ai_seo_pro_og_tags', true)): ?>
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="<?php echo is_front_page() ? 'website' : 'article'; ?>">
	<meta property="og:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta property="og:title" content="<?php echo esc_attr($ai_seo_pro_og_title); ?>">
	<meta property="og:description" content="<?php echo esc_attr($ai_seo_pro_og_description); ?>">
	<?php if (!empty($ai_seo_pro_og_image)): ?>
		<meta property="og:image" content="<?php echo esc_url($ai_seo_pro_og_image); ?>">
	<?php endif; ?>
	<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
	<meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">
	<?php if (!is_front_page()): ?>
		<meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c', $post->ID)); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c', $post->ID)); ?>">
	<?php endif; ?>
<?php endif; ?>

<?php if (get_option('ai_seo_pro_twitter_cards', true)): ?>
	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="<?php echo esc_url(get_permalink($post->ID)); ?>">
	<meta name="twitter:title" content="<?php echo esc_attr($ai_seo_pro_twitter_title); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr($ai_seo_pro_twitter_description); ?>">
	<?php if (!empty($ai_seo_pro_og_image)): ?>
		<meta name="twitter:image" content="<?php echo esc_url($ai_seo_pro_og_image); ?>">
	<?php endif; ?>
	<?php if (!empty($ai_seo_pro_twitter_username)): ?>
		<meta name="twitter:site" content="<?php echo esc_attr($ai_seo_pro_twitter_username); ?>">
		<meta name="twitter:creator" content="<?php echo esc_attr($ai_seo_pro_twitter_username); ?>">
	<?php endif; ?>
<?php endif; ?>

<!-- /AI SEO Pro Meta Tags -->