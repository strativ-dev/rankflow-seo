<?php
/**
 * Schema markup output.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

$ai_seo_pro_schema_data = array();

// Article schema for posts.
if ('post' === $post->post_type) {
	$ai_seo_pro_schema_data[] = $this->schema_generator->generate_article_schema($post->ID);
} else {
	// WebPage schema for pages.
	$ai_seo_pro_schema_data[] = $this->schema_generator->generate_webpage_schema($post->ID);
}

// Add breadcrumb schema if enabled.
if (get_option('ai_seo_pro_breadcrumbs', false)) {
	$ai_seo_pro_schema_data[] = $this->schema_generator->generate_breadcrumb_schema($post->ID);
}

// Organization schema for homepage.
if (is_front_page()) {
	$ai_seo_pro_schema_data[] = $this->schema_generator->generate_organization_schema();
}

// Filter to allow customization.
$ai_seo_pro_schema_data = apply_filters('ai_seo_pro_schema_data', $ai_seo_pro_schema_data, $post->ID);

if (!empty($ai_seo_pro_schema_data)):
	?>
	<!-- AI SEO Pro Schema Markup -->
	<script type="application/ld+json">
	<?php echo wp_json_encode($ai_seo_pro_schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
	</script>
	<!-- /AI SEO Pro Schema Markup -->
	<?php
endif;