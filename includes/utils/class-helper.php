<?php
/**
 * Helper functions.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/utils
 * @author     Strativ AB
 */
class AI_SEO_Pro_Helper
{

	/**
	 * Format SEO score for display.
	 *
	 * @param    int    $score    Score (0-100)
	 * @return   string
	 */
	public static function format_score($score)
	{
		if ($score >= 80) {
			return '<span style="color: #46b450;">●</span> ' . $score;
		} elseif ($score >= 60) {
			return '<span style="color: #ffb900;">●</span> ' . $score;
		} elseif ($score >= 40) {
			return '<span style="color: #f56e28;">●</span> ' . $score;
		} else {
			return '<span style="color: #dc3232;">●</span> ' . $score;
		}
	}

	/**
	 * Truncate text to specific length.
	 *
	 * @param    string    $text      Text to truncate
	 * @param    int       $length    Maximum length
	 * @param    string    $suffix    Suffix to append
	 * @return   string
	 */
	public static function truncate($text, $length = 100, $suffix = '...')
	{
		if (strlen($text) <= $length) {
			return $text;
		}

		return substr($text, 0, $length) . $suffix;
	}

	/**
	 * Get post types enabled for SEO.
	 *
	 * @return   array
	 */
	public static function get_enabled_post_types()
	{
		$enabled = get_option('ai_seo_pro_post_types', array('post', 'page'));
		return is_array($enabled) ? $enabled : array('post', 'page');
	}

	/**
	 * Check if post type is enabled.
	 *
	 * @param    string    $post_type    Post type
	 * @return   bool
	 */
	public static function is_post_type_enabled($post_type)
	{
		return in_array($post_type, self::get_enabled_post_types());
	}

	/**
	 * Get API provider name.
	 *
	 * @param    string    $provider    Provider key
	 * @return   string
	 */
	public static function get_provider_name($provider)
	{
		$providers = array(
			'anthropic' => 'Anthropic (Claude)',
			'gemini' => 'Google (Gemini)',
		);

		return isset($providers[$provider]) ? $providers[$provider] : $provider;
	}

	/**
	 * Check if API is configured.
	 *
	 * @return   bool
	 */
	public static function is_api_configured()
	{
		$api_key = get_option('ai_seo_pro_api_key');
		return !empty($api_key);
	}

	/**
	 * Get character count status.
	 *
	 * @param    int    $count    Current character count
	 * @param    int    $min      Minimum recommended
	 * @param    int    $max      Maximum recommended
	 * @return   string
	 */
	public static function get_character_status($count, $min, $max)
	{
		if ($count >= $min && $count <= $max) {
			return 'good';
		} elseif ($count < $min || $count > $max) {
			return 'warning';
		} else {
			return 'error';
		}
	}

	/**
	 * Format bytes to human readable.
	 *
	 * @param    int    $bytes    Bytes
	 * @return   string
	 */
	public static function format_bytes($bytes)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		for ($i = 0; $bytes > 1024; $i++) {
			$bytes /= 1024;
		}

		return round($bytes, 2) . ' ' . $units[$i];
	}
}