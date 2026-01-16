<?php
/**
 * Fired during plugin activation
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes
 * @author     Strativ AB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

class RankFlow_SEO_Activator
{

	/**
	 * Activation tasks.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		// Check PHP version
		if (version_compare(PHP_VERSION, '7.4', '<')) {
			deactivate_plugins(RANKFLOW_SEO_PLUGIN_BASENAME);
			wp_die(
				esc_html__('RankFlow SEO requires PHP 7.4 or higher. Please upgrade your PHP version.', 'rankflow-seo'),
				esc_html__('Plugin Activation Error', 'rankflow-seo'),
				array('back_link' => true)
			);
		}

		// Check WordPress version
		if (version_compare(get_bloginfo('version'), '5.8', '<')) {
			deactivate_plugins(RANKFLOW_SEO_PLUGIN_BASENAME);
			wp_die(
				esc_html__('RankFlow SEO requires WordPress 5.8 or higher. Please upgrade your WordPress installation.', 'rankflow-seo'),
				esc_html__('Plugin Activation Error', 'rankflow-seo'),
				array('back_link' => true)
			);
		}

		// Set default options
		self::set_default_options();

		// Create custom database tables
		self::create_tables();

		// Set activation flag for redirect
		set_transient('rankflow_seo_activation_redirect', true, 30);

		// Set flag to flush rewrite rules on next page load
		update_option('rankflow_seo_flush_rewrite_rules', true);
	}

	/**
	 * Set default plugin options.
	 *
	 * @since    1.0.0
	 */
	private static function set_default_options()
	{
		$defaults = array(
			'api_provider' => 'gemini',
			'api_key' => '',
			'auto_generate' => false,
			'enable_content_analysis' => true,
			'enable_seo_score' => true,
			'enable_schema' => true,
			'focus_keyword' => true,
			'readability_analysis' => true,
			'og_tags' => true,
			'twitter_cards' => true,
			'post_types' => array('post', 'page'),
			'title_separator' => '-',
			'homepage_title' => get_bloginfo('name'),
			'homepage_description' => get_bloginfo('description'),
			'404_monitoring' => true,
			'404_retention' => 30,
			// Sitemap options
			'sitemap_enabled' => true,
			'sitemap_entries_per_page' => 1000,
			'sitemap_include_images' => true,
			'sitemap_include_taxonomies' => true,
			'sitemap_include_authors' => false,
			'sitemap_ping_search_engines' => true,
			'sitemap_post_types' => array('post', 'page'),
			'sitemap_taxonomies' => array('category', 'product_cat'),
		);

		foreach ($defaults as $key => $value) {
			if (false === get_option('rankflow_seo_' . $key)) {
				add_option('rankflow_seo_' . $key, $value);
			}
		}
	}

	/**
	 * Create custom database tables.
	 *
	 * @since    1.0.0
	 */
	private static function create_tables()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Analysis table
		$analysis_table = $wpdb->prefix . 'rankflow_seo_analysis';
		$sql1 = "CREATE TABLE $analysis_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			post_id bigint(20) NOT NULL,
			analysis_type varchar(50) NOT NULL,
			score int(3) NOT NULL,
			details longtext,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY post_id (post_id),
			KEY analysis_type (analysis_type)
		) $charset_collate;";

		// Redirects table
		$redirects_table = $wpdb->prefix . 'rankflow_seo_redirects';
		$sql2 = "CREATE TABLE $redirects_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			source_url varchar(500) NOT NULL,
			target_url varchar(500) NOT NULL,
			redirect_type varchar(10) NOT NULL DEFAULT '301',
			hits bigint(20) DEFAULT 0,
			is_regex tinyint(1) DEFAULT 0,
			is_active tinyint(1) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			last_accessed datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY source_url (source_url(191)),
			KEY is_active (is_active),
			KEY redirect_type (redirect_type)
		) $charset_collate;";

		// 404 Logs table
		$logs_table = $wpdb->prefix . 'rankflow_seo_404_logs';
		$sql3 = "CREATE TABLE $logs_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			url varchar(500) NOT NULL,
			referrer varchar(500) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			ip_address varchar(45) DEFAULT NULL,
			hits int(11) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY url (url(191)),
			KEY hits (hits),
			KEY created_at (created_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);

		// Store database version
		update_option('rankflow_seo_db_version', '1.1');
	}
}