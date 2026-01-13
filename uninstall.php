<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    AI_SEO_Pro
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
function ai_seo_pro_delete_options()
{
	$options = array(
		'ai_seo_pro_api_provider',
		'ai_seo_pro_api_key',
		'ai_seo_pro_auto_generate',
		'ai_seo_pro_enable_content_analysis',
		'ai_seo_pro_enable_seo_score',
		'ai_seo_pro_enable_schema',
		'ai_seo_pro_focus_keyword',
		'ai_seo_pro_readability_analysis',
		'ai_seo_pro_og_tags',
		'ai_seo_pro_twitter_cards',
		'ai_seo_pro_post_types',
		'ai_seo_pro_title_separator',
		'ai_seo_pro_homepage_title',
		'ai_seo_pro_homepage_description',
		'ai_seo_pro_404_monitoring',
		'ai_seo_pro_404_retention',
		'ai_seo_pro_db_version',
		// Sitemap options.
		'ai_seo_pro_sitemap_enabled',
		'ai_seo_pro_sitemap_entries_per_page',
		'ai_seo_pro_sitemap_include_images',
		'ai_seo_pro_sitemap_include_taxonomies',
		'ai_seo_pro_sitemap_include_authors',
		'ai_seo_pro_sitemap_ping_search_engines',
		'ai_seo_pro_sitemap_post_types',
		'ai_seo_pro_sitemap_taxonomies',
		// Site connections options.
		'ai_seo_pro_ahrefs_verification',
		'ai_seo_pro_baidu_verification',
		'ai_seo_pro_bing_verification',
		'ai_seo_pro_google_verification',
		'ai_seo_pro_pinterest_verification',
		'ai_seo_pro_yandex_verification',
		// Robots.txt options.
		'ai_seo_pro_robots_enabled',
		'ai_seo_pro_robots_custom_rules',
		'ai_seo_pro_robots_include_sitemap',
		'ai_seo_pro_robots_block_ai',
		'ai_seo_pro_robots_block_bad_bots',
		'ai_seo_pro_robots_disallow_wp_admin',
		'ai_seo_pro_robots_disallow_wp_includes',
		'ai_seo_pro_robots_allow_ajax',
		'ai_seo_pro_robots_disallow_search',
		'ai_seo_pro_robots_sitemap_urls',
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
function ai_seo_pro_delete_post_meta()
{
	global $wpdb;

	$meta_keys = array(
		'_ai_seo_title',
		'_ai_seo_description',
		'_ai_seo_keywords',
		'_ai_seo_focus_keyword',
		'_ai_seo_og_title',
		'_ai_seo_og_description',
		'_ai_seo_twitter_title',
		'_ai_seo_twitter_description',
		'_ai_seo_robots',
		'_ai_seo_canonical',
		'_ai_seo_score',
		'_ai_seo_content_analysis',
		'_ai_seo_auto_generate',
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
function ai_seo_pro_delete_tables()
{
	global $wpdb;

	$tables = array(
		$wpdb->prefix . 'ai_seo_analysis',
		$wpdb->prefix . 'ai_seo_redirects',
		$wpdb->prefix . 'ai_seo_404_logs',
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
function ai_seo_pro_delete_transients()
{
	global $wpdb;

	// Delete all transients with our prefix.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE %s 
			OR option_name LIKE %s",
			'%\_transient\_ai\_seo\_pro\_%',
			'%\_transient\_timeout\_ai\_seo\_pro\_%'
		)
	);
}

// Run uninstall cleanup.
ai_seo_pro_delete_options();
ai_seo_pro_delete_post_meta();
ai_seo_pro_delete_tables();
ai_seo_pro_delete_transients();

// Clear any cached data.
wp_cache_flush();