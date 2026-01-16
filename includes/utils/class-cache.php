<?php
/**
 * Cache utility.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/utils
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class RankFlow_SEO_Cache
 */
class RankFlow_SEO_Cache
{

	/**
	 * Cache prefix.
	 */
	const PREFIX = 'rankflow_seo_';

	/**
	 * Default expiration (24 hours).
	 */
	const DEFAULT_EXPIRATION = 86400;

	/**
	 * Get cached data.
	 *
	 * @param string $key Cache key.
	 * @return mixed|false
	 */
	public static function get($key)
	{
		return get_transient(self::PREFIX . $key);
	}

	/**
	 * Set cached data.
	 *
	 * @param string $key        Cache key.
	 * @param mixed  $value      Value to cache.
	 * @param int    $expiration Expiration in seconds.
	 * @return bool
	 */
	public static function set($key, $value, $expiration = null)
	{
		if (null === $expiration) {
			$expiration = self::DEFAULT_EXPIRATION;
		}

		return set_transient(self::PREFIX . $key, $value, $expiration);
	}

	/**
	 * Delete cached data.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public static function delete($key)
	{
		return delete_transient(self::PREFIX . $key);
	}

	/**
	 * Clear all plugin caches.
	 *
	 * @return int Number of caches cleared.
	 */
	public static function clear_all()
	{
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Clearing all plugin transients.
		$count = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				OR option_name LIKE %s",
				'_transient_' . self::PREFIX . '%',
				'_transient_timeout_' . self::PREFIX . '%'
			)
		);

		return $count;
	}

	/**
	 * Generate cache key from parameters.
	 *
	 * @param array $params Parameters.
	 * @return string
	 */
	public static function generate_key($params)
	{
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Used for cache key generation only.
		return md5(serialize($params));
	}
}