<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    RankFlow_SEO
 * @author     Strativ AB
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

/**
 * Delete plugin options.
 *
 * @since 1.0.0
 */
function rankflow_seo_delete_options()
{
	$options = array(
		'rankflow_seo_api_provider',
		'rankflow_seo_api_key',
		'rankflow_seo_auto_generate',
		'rankflow_seo_enable_content_analysis',
		'rankflow_seo_enable_seo_score',
		'rankflow_seo_enable_schema',
		'rankflow_seo_focus_keyword',
		'rankflow_seo_readability_analysis',
		'rankflow_seo_og_tags',
		'rankflow_seo_twitter_cards',
		'rankflow_seo_post_types',
		'rankflow_seo_title_separator',
		'rankflow_seo_homepage_title',
		'rankflow_seo_homepage_description',
		'rankflow_seo_404_monitoring',
		'rankflow_seo_404_retention',
		'rankflow_seo_db_version',
		// Sitemap options.
		'rankflow_seo_sitemap_enabled',
		'rankflow_seo_sitemap_entries_per_page',
		'rankflow_seo_sitemap_include_images',
		'rankflow_seo_sitemap_include_taxonomies',
		'rankflow_seo_sitemap_include_authors',
		'rankflow_seo_sitemap_ping_search_engines',
		'rankflow_seo_sitemap_post_types',
		'rankflow_seo_sitemap_taxonomies',
		// Site connections options.
		'rankflow_seo_ahrefs_verification',
		'rankflow_seo_baidu_verification',
		'rankflow_seo_bing_verification',
		'rankflow_seo_google_verification',
		'rankflow_seo_pinterest_verification',
		'rankflow_seo_yandex_verification',
		// Robots.txt options.
		'rankflow_seo_robots_enabled',
		'rankflow_seo_robots_custom_rules',
		'rankflow_seo_robots_include_sitemap',
		'rankflow_seo_robots_block_ai',
		'rankflow_seo_robots_block_bad_bots',
		'rankflow_seo_robots_disallow_wp_admin',
		'rankflow_seo_robots_disallow_wp_includes',
		'rankflow_seo_robots_allow_ajax',
		'rankflow_seo_robots_disallow_search',
		'rankflow_seo_robots_sitemap_urls',
	);

	foreach ($options as $option) {
		delete_option($option);
	}
}

/**
 * Delete post meta using a single optimized query.
 *
 * Uses a single DELETE with IN clause instead of multiple queries,
 * which is more efficient for bulk deletion.
 *
 * @since 1.0.0
 */
function rankflow_seo_delete_post_meta()
{
	global $wpdb;

	$meta_keys = array(
		'_rankflow_seo_title',
		'_rankflow_seo_description',
		'_rankflow_seo_keywords',
		'_rankflow_seo_focus_keyword',
		'_rankflow_seo_og_title',
		'_rankflow_seo_og_description',
		'_rankflow_seo_twitter_title',
		'_rankflow_seo_twitter_description',
		'_rankflow_seo_robots',
		'_rankflow_seo_canonical',
		'_rankflow_seo_score',
		'_rankflow_seo_content_analysis',
		'_rankflow_seo_auto_generate',
	);

	// Build placeholders for prepared statement.
	$placeholders = implode(', ', array_fill(0, count($meta_keys), '%s'));

	// Build the SQL query string.
	$sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ({$placeholders})";

	// Single optimized query with IN clause.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup.
	$wpdb->query(
		$wpdb->prepare(
			$sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			...$meta_keys
		)
	);
}

/**
 * Delete custom database tables.
 *
 * @since 1.0.0
 */
function rankflow_seo_delete_tables()
{
	global $wpdb;

	$tables = array(
		$wpdb->prefix . 'rankflow_seo_analysis',
		$wpdb->prefix . 'rankflow_seo_redirects',
		$wpdb->prefix . 'rankflow_seo_404_logs',
	);

	foreach ($tables as $table) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Uninstall cleanup, table name safely constructed.
		$wpdb->query("DROP TABLE IF EXISTS {$table}");
	}
}

/**
 * Delete transients.
 *
 * @since 1.0.0
 */
function rankflow_seo_delete_transients()
{
	global $wpdb;

	// Delete all transients with our prefix.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE %s 
			OR option_name LIKE %s",
			'%\_transient\_rankflow\_seo\_%',
			'%\_transient\_timeout\_rankflow\_seo\_%'
		)
	);
}

// Run uninstall cleanup.
rankflow_seo_delete_options();
rankflow_seo_delete_post_meta();
rankflow_seo_delete_tables();
rankflow_seo_delete_transients();

// Clear any cached data.
wp_cache_flush();