<?php
/**
 * Schema markup output.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/public/partials
 *
 * @var WP_Post $post Post object.
 */

if (!defined('ABSPATH')) {
	exit;
}

$rankflow_seo_schema_data = array();

// Article schema for posts.
if ('post' === $post->post_type) {
	$rankflow_seo_schema_data[] = $this->schema_generator->generate_article_schema($post->ID);
} else {
	// WebPage schema for pages.
	$rankflow_seo_schema_data[] = $this->schema_generator->generate_webpage_schema($post->ID);
}

// Add breadcrumb schema if enabled.
if (get_option('rankflow_seo_breadcrumbs', false)) {
	$rankflow_seo_schema_data[] = $this->schema_generator->generate_breadcrumb_schema($post->ID);
}

// Organization schema for homepage.
if (is_front_page()) {
	$rankflow_seo_schema_data[] = $this->schema_generator->generate_organization_schema();
}

// Filter to allow customization.
$rankflow_seo_schema_data = apply_filters('rankflow_seo_schema_data', $rankflow_seo_schema_data, $post->ID);

if (!empty($rankflow_seo_schema_data)):
	?>
	<!-- RankFlow SEO Schema Markup -->
	<script type="application/ld+json">
					<?php echo wp_json_encode($rankflow_seo_schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
					</script>
	<!-- /RankFlow SEO Schema Markup -->
	<?php
endif;