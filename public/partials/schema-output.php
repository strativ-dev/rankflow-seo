<?php
/**
 * Schema markup output.
 *
 * @package    MPSEO
 * @subpackage MPSEO/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

$mpseo_schema_data = array();

// Article schema for posts.
if ('post' === $post->post_type) {
	$mpseo_schema_data[] = $this->schema_generator->generate_article_schema($post->ID);
} else {
	// WebPage schema for pages.
	$mpseo_schema_data[] = $this->schema_generator->generate_webpage_schema($post->ID);
}

// Add breadcrumb schema if enabled.
if (get_option('mpseo_breadcrumbs', false)) {
	$mpseo_schema_data[] = $this->schema_generator->generate_breadcrumb_schema($post->ID);
}

// Organization schema for homepage.
if (is_front_page()) {
	$mpseo_schema_data[] = $this->schema_generator->generate_organization_schema();
}

// Filter to allow customization.
$mpseo_schema_data = apply_filters('mpseo_schema_data', $mpseo_schema_data, $post->ID);

if (!empty($mpseo_schema_data)):
	?>
	<!-- Metapilot Smart SEO Schema Markup -->
	<?php
	wp_print_inline_script_tag(
		wp_json_encode($mpseo_schema_data, JSON_HEX_TAG | JSON_HEX_AMP),
		array('type' => 'application/ld+json')
	);
	?>
	<!-- /Metapilot Smart SEO Schema Markup -->
	<?php
endif;