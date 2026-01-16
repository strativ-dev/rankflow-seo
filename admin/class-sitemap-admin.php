<?php
/**
 * Sitemap Admin - Handle sitemap settings
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin
 * @author     Strativ AB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

class RankFlow_SEO_Sitemap_Admin
{

	/**
	 * Initialize admin hooks
	 */
	public function init()
	{
		add_action('admin_init', array($this, 'register_settings'));
		add_action('wp_ajax_rankflow_seo_flush_sitemap', array($this, 'ajax_flush_sitemap'));
		add_action('update_option_rankflow_seo_sitemap_enabled', array($this, 'on_settings_saved'), 10, 2);
		add_action('update_option_rankflow_seo_sitemap_post_types', array($this, 'on_settings_saved'), 10, 2);
	}

	/**
	 * Register settings
	 */
	public function register_settings()
	{
		// Register setting group
		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_enabled',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => true,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_entries_per_page',
			array(
				'type' => 'integer',
				'sanitize_callback' => 'absint',
				'default' => 1000,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_include_images',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => true,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_include_taxonomies',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => true,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_include_authors',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => false,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_ping_search_engines',
			array(
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => true,
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_post_types',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_post_types'),
				'default' => array(),
			)
		);

		register_setting(
			'rankflow_seo_sitemap_settings',
			'rankflow_seo_sitemap_taxonomies',
			array(
				'type' => 'array',
				'sanitize_callback' => array($this, 'sanitize_taxonomies'),
				'default' => array(),
			)
		);
	}

	/**
	 * Sanitize post types
	 *
	 * @param array $input Input array
	 * @return array
	 */
	public function sanitize_post_types($input)
	{
		if (!is_array($input)) {
			return array();
		}

		$valid_post_types = get_post_types(array('public' => true), 'names');
		return array_values(array_intersect($input, $valid_post_types));
	}

	/**
	 * Sanitize taxonomies
	 *
	 * @param array $input Input array
	 * @return array
	 */
	public function sanitize_taxonomies($input)
	{
		if (!is_array($input)) {
			return array();
		}

		$valid_taxonomies = get_taxonomies(array('public' => true), 'names');
		return array_values(array_intersect($input, $valid_taxonomies));
	}

	/**
	 * AJAX handler to flush sitemap cache
	 */
	public function ajax_flush_sitemap()
	{
		check_ajax_referer('rankflow_seo_sitemap', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'rankflow-seo')));
		}

		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear any cached sitemaps
		delete_transient('rankflow_seo_sitemap_last_ping');

		wp_send_json_success(array('message' => __('Sitemap cache flushed.', 'rankflow-seo')));
	}

	/**
	 * On settings saved, flush rewrite rules
	 *
	 * @param mixed $old_value Old value
	 * @param mixed $new_value New value
	 */
	public function on_settings_saved($old_value, $new_value)
	{
		if ($old_value !== $new_value) {
			flush_rewrite_rules();
		}
	}
}
