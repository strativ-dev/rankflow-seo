<?php
/**
 * Data sanitization.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/utils
 * @author     Strativ AB
 */
class RankFlow_SEO_Sanitizer
{

	/**
	 * Sanitize meta title.
	 *
	 * @param    string    $title    Title
	 * @return   string
	 */
	public static function sanitize_meta_title($title)
	{
		$title = sanitize_text_field($title);
		$title = substr($title, 0, 60);
		return $title;
	}

	/**
	 * Sanitize meta description.
	 *
	 * @param    string    $description    Description
	 * @return   string
	 */
	public static function sanitize_meta_description($description)
	{
		$description = sanitize_textarea_field($description);
		$description = substr($description, 0, 160);
		return $description;
	}

	/**
	 * Sanitize keywords.
	 *
	 * @param    string    $keywords    Keywords
	 * @return   string
	 */
	public static function sanitize_keywords($keywords)
	{
		$keywords = sanitize_text_field($keywords);
		$keywords = strtolower($keywords);

		// Remove extra spaces
		$keywords = preg_replace('/\s+/', ' ', $keywords);

		// Remove duplicate keywords
		$keywords_array = array_map('trim', explode(',', $keywords));
		$keywords_array = array_unique($keywords_array);

		return implode(', ', $keywords_array);
	}

	/**
	 * Sanitize API key.
	 *
	 * @param    string    $api_key    API key
	 * @return   string
	 */
	public static function sanitize_api_key($api_key)
	{
		return sanitize_text_field(trim($api_key));
	}

	/**
	 * Sanitize robots meta value.
	 *
	 * @param    string    $robots    Robots value
	 * @return   string
	 */
	public static function sanitize_robots($robots)
	{
		$allowed = array('index', 'noindex', 'follow', 'nofollow', 'noarchive', 'nosnippet');
		$robots = sanitize_text_field($robots);

		$values = array_map('trim', explode(',', $robots));
		$values = array_filter($values, function ($value) use ($allowed) {
			return in_array($value, $allowed);
		});

		return implode(', ', $values);
	}
}