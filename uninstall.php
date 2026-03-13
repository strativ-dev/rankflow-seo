<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    MPSEO
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
function mpseo_delete_options()
{
	$options = array(
		'mpseo_api_provider',
		'mpseo_api_key',
		'mpseo_auto_generate',
		'mpseo_enable_content_analysis',
		'mpseo_enable_seo_score',
		'mpseo_enable_schema',
		'mpseo_focus_keyword',
		'mpseo_readability_analysis',
		'mpseo_og_tags',
		'mpseo_twitter_cards',
		'mpseo_post_types',
		'mpseo_title_separator',
		'mpseo_homepage_title',
		'mpseo_homepage_description',
		'mpseo_404_monitoring',
		'mpseo_404_retention',
		'mpseo_db_version',
		// Sitemap options.
		'mpseo_sitemap_enabled',
		'mpseo_sitemap_entries_per_page',
		'mpseo_sitemap_include_images',
		'mpseo_sitemap_include_taxonomies',
		'mpseo_sitemap_include_authors',
		'mpseo_sitemap_ping_search_engines',
		'mpseo_sitemap_post_types',
		'mpseo_sitemap_taxonomies',
		// Site connections options.
		'mpseo_ahrefs_verification',
		'mpseo_baidu_verification',
		'mpseo_bing_verification',
		'mpseo_google_verification',
		'mpseo_pinterest_verification',
		'mpseo_yandex_verification',
		// Robots.txt options.
		'mpseo_robots_enabled',
		'mpseo_robots_custom_rules',
		'mpseo_robots_include_sitemap',
		'mpseo_robots_block_ai',
		'mpseo_robots_block_bad_bots',
		'mpseo_robots_disallow_wp_admin',
		'mpseo_robots_disallow_wp_includes',
		'mpseo_robots_allow_ajax',
		'mpseo_robots_disallow_search',
		'mpseo_robots_sitemap_urls',
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
function mpseo_delete_post_meta()
{
	global $wpdb;

	$meta_keys = array(
		'_mpseo_title',
		'_mpseo_description',
		'_mpseo_keywords',
		'_mpseo_focus_keyword',
		'_mpseo_og_title',
		'_mpseo_og_description',
		'_mpseo_twitter_title',
		'_mpseo_twitter_description',
		'_mpseo_robots',
		'_mpseo_canonical',
		'_mpseo_score',
		'_mpseo_content_analysis',
		'_mpseo_auto_generate',
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
function mpseo_delete_tables()
{
	global $wpdb;

	$tables = array(
		$wpdb->prefix . 'mpseo_analysis',
		$wpdb->prefix . 'mpseo_redirects',
		$wpdb->prefix . 'mpseo_404_logs',
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
function mpseo_delete_transients()
{
	global $wpdb;

	// Delete all transients with our prefix.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE %s 
			OR option_name LIKE %s",
			'%\_transient\_mpseo\_%',
			'%\_transient\_timeout\_mpseo\_%'
		)
	);
}

// Run uninstall cleanup.
mpseo_delete_options();
mpseo_delete_post_meta();
mpseo_delete_tables();
mpseo_delete_transients();

// Clear any cached data.
wp_cache_flush();