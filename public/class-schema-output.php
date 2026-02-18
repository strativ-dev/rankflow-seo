<?php
/**
 * Schema Frontend Output
 *
 * Outputs schema markup on the frontend
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/public
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

class RankFlow_SEO_Schema_Output
{

	/**
	 * Output schema markup in head
	 */
	public function output_schema()
	{
		// Check if schema is enabled.
		if (!get_option('rankflow_seo_schema_enabled', true)) {
			return;
		}

		$schemas = get_option('rankflow_seo_schemas', array());

		if (empty($schemas)) {
			return;
		}

		// Load the admin class for generation methods.
		if (!class_exists('RankFlow_SEO_Schema_Admin')) {
			require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/class-schema-admin.php';
		}

		$schema_admin = new RankFlow_SEO_Schema_Admin();

		foreach ($schemas as $schema) {
			// Check if schema is enabled.
			if (empty($schema['enabled'])) {
				continue;
			}

			// Check display rules.
			if (!$this->should_display_schema($schema)) {
				continue;
			}

			$markup = $schema_admin->generate_schema_markup($schema);

			if ($markup) {
				echo "\n<!-- RankFlow SEO Schema: " . esc_html($schema['type']) . " -->\n";
				wp_print_inline_script_tag(
					wp_json_encode($markup, JSON_HEX_TAG | JSON_HEX_AMP),
					array('type' => 'application/ld+json')
				);
			}
		}
	}

	/**
	 * Check if schema should be displayed on current page
	 *
	 * @param array $schema Schema configuration.
	 * @return bool True if schema should be displayed.
	 */
	private function should_display_schema($schema)
	{
		$display_mode = isset($schema['display_mode']) ? $schema['display_mode'] : 'all';
		$current_id = get_queried_object_id();

		switch ($display_mode) {
			case 'all':
				// Display on all pages.
				return true;

			case 'homepage':
				// Display only on homepage.
				return is_front_page() || is_home();

			case 'post_types':
				// Display on specific post types.
				$post_types = isset($schema['post_types']) ? $schema['post_types'] : array();
				if (empty($post_types)) {
					return true; // If no post types selected, show on all.
				}
				return is_singular($post_types) || $this->is_post_type_archive($post_types);

			case 'include':
				// Display only on specific pages/posts.
				$include_ids = isset($schema['include_ids']) ? $schema['include_ids'] : array();
				if (empty($include_ids)) {
					return true; // If no IDs selected, show on all.
				}
				return in_array($current_id, $include_ids);

			case 'exclude':
				// Display on all except specific pages/posts.
				$exclude_ids = isset($schema['exclude_ids']) ? $schema['exclude_ids'] : array();
				if (empty($exclude_ids)) {
					return true; // If no IDs to exclude, show on all.
				}
				return !in_array($current_id, $exclude_ids);

			default:
				return true;
		}
	}

	/**
	 * Check if current page is a post type archive
	 *
	 * @param array $post_types Post types to check.
	 * @return bool True if current page is archive of given post types.
	 */
	private function is_post_type_archive($post_types)
	{
		foreach ($post_types as $post_type) {
			if (is_post_type_archive($post_type)) {
				return true;
			}
		}
		return false;
	}
}
