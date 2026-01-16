<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/public
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_Public
 */
class RankFlow_SEO_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @var string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string $version
	 */
	private $version;

	/**
	 * Meta manager instance.
	 *
	 * @var RankFlow_SEO_Meta_Manager $meta_manager
	 */
	private $meta_manager;

	/**
	 * Schema generator instance.
	 *
	 * @var RankFlow_SEO_Schema_Generator $schema_generator
	 */
	private $schema_generator;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->meta_manager = new RankFlow_SEO_Meta_Manager();
		$this->schema_generator = new RankFlow_SEO_Schema_Generator();
	}

	/**
	 * Output meta tags.
	 */
	public function output_meta_tags()
	{
		if (!is_singular()) {
			return;
		}

		global $post;

		if (!$post) {
			return;
		}

		// Check if post type is enabled.
		if (!RankFlow_SEO_Helper::is_post_type_enabled($post->post_type)) {
			return;
		}

		// Get meta data with prefixed variable name.
		$rankflow_seo_meta_data = $this->meta_manager->get_post_meta($post->ID);

		// Include the meta tags partial.
		include RANKFLOW_SEO_PLUGIN_DIR . 'public/partials/meta-tags.php';
	}

	/**
	 * Output schema markup.
	 */
	public function output_schema_markup()
	{
		if (!get_option('rankflow_seo_enable_schema', true)) {
			return;
		}

		if (!is_singular()) {
			return;
		}

		global $post;

		if (!$post) {
			return;
		}

		// Check if post type is enabled.
		if (!RankFlow_SEO_Helper::is_post_type_enabled($post->post_type)) {
			return;
		}

		// Include the schema output partial.
		include RANKFLOW_SEO_PLUGIN_DIR . 'public/partials/schema-output.php';
	}

	/**
	 * Remove default WordPress meta tags that might conflict.
	 */
	public function remove_default_meta()
	{
		// Remove WordPress generator meta tag.
		remove_action('wp_head', 'wp_generator');

		// Remove Windows Live Writer manifest link.
		remove_action('wp_head', 'wlwmanifest_link');

		// Remove RSD link.
		remove_action('wp_head', 'rsd_link');

		// Remove shortlink.
		remove_action('wp_head', 'wp_shortlink_wp_head');

		// Remove adjacent posts links.
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
	}

	/**
	 * Output breadcrumbs (if enabled).
	 */
	public function output_breadcrumbs()
	{
		if (!get_option('rankflow_seo_breadcrumbs', false)) {
			return;
		}

		if (!is_singular()) {
			return;
		}

		include RANKFLOW_SEO_PLUGIN_DIR . 'public/partials/breadcrumbs.php';
	}

	/**
	 * Filter document title.
	 *
	 * @param string $title Document title.
	 * @return string
	 */
	public function filter_document_title($title)
	{
		if (!is_singular()) {
			return $title;
		}

		global $post;

		if (!$post) {
			return $title;
		}

		// Check if post type is enabled.
		if (!RankFlow_SEO_Helper::is_post_type_enabled($post->post_type)) {
			return $title;
		}

		// Get custom meta title.
		$rankflow_seo_custom_title = get_post_meta($post->ID, '_rankflow_seo_title', true);

		if (!empty($rankflow_seo_custom_title)) {
			return $rankflow_seo_custom_title;
		}

		return $title;
	}

	/**
	 * Filter document title parts.
	 *
	 * @param array $title_parts Title parts.
	 * @return array
	 */
	public function filter_document_title_parts($title_parts)
	{
		if (!is_singular()) {
			return $title_parts;
		}

		global $post;

		if (!$post) {
			return $title_parts;
		}

		// Check if post type is enabled.
		if (!RankFlow_SEO_Helper::is_post_type_enabled($post->post_type)) {
			return $title_parts;
		}

		// Get custom meta title.
		$rankflow_seo_custom_title = get_post_meta($post->ID, '_rankflow_seo_title', true);

		if (!empty($rankflow_seo_custom_title)) {
			// Replace the title part completely.
			$title_parts['title'] = $rankflow_seo_custom_title;

			// Remove site name if custom title already includes it.
			if (false !== strpos($rankflow_seo_custom_title, get_bloginfo('name'))) {
				unset($title_parts['site']);
			}
		}

		return $title_parts;
	}
}