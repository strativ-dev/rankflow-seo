<?php
/**
 * Fired during plugin deactivation.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_Deactivator
 */
class RankFlow_SEO_Deactivator
{

	/**
	 * Deactivation tasks.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate()
	{

		// Clear scheduled hooks (if any).
		wp_clear_scheduled_hook('rankflow_seo_daily_cleanup');

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Clear transients.
		self::clear_transients();

		// Clear cache.
		wp_cache_flush();
	}

	/**
	 * Clear plugin transients.
	 *
	 * @since 1.0.0
	 */
	private static function clear_transients()
	{
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Clearing plugin transients on deactivation.
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				OR option_name LIKE %s",
				'_transient_rankflow_seo_%',
				'_transient_timeout_rankflow_seo_%'
			)
		);

		foreach ($transients as $transient) {
			delete_option($transient);
		}
	}
}