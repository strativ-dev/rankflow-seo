<?php
/**
 * Input validation.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/utils
 * @author     Strativ AB
 */
class AI_SEO_Pro_Validator
{

	/**
	 * Validate meta title.
	 *
	 * @param    string    $title    Title to validate
	 * @return   array
	 */
	public static function validate_meta_title($title)
	{
		$errors = array();
		$length = strlen($title);

		if (empty($title)) {
			$errors[] = __('Title cannot be empty', 'ai-seo-pro');
		}

		if ($length < 30) {
			$errors[] = __('Title is too short (minimum 30 characters)', 'ai-seo-pro');
		}

		if ($length > 60) {
			$errors[] = __('Title is too long (maximum 60 characters)', 'ai-seo-pro');
		}

		return array(
			'valid' => empty($errors),
			'errors' => $errors,
		);
	}

	/**
	 * Validate meta description.
	 *
	 * @param    string    $description    Description to validate
	 * @return   array
	 */
	public static function validate_meta_description($description)
	{
		$errors = array();
		$length = strlen($description);

		if (empty($description)) {
			$errors[] = __('Description cannot be empty', 'ai-seo-pro');
		}

		if ($length < 120) {
			$errors[] = __('Description is too short (minimum 120 characters)', 'ai-seo-pro');
		}

		if ($length > 160) {
			$errors[] = __('Description is too long (maximum 160 characters)', 'ai-seo-pro');
		}

		return array(
			'valid' => empty($errors),
			'errors' => $errors,
		);
	}

	/**
	 * Validate API key format.
	 *
	 * @param    string    $api_key     API key
	 * @param    string    $provider    Provider name
	 * @return   bool
	 */
	public static function validate_api_key($api_key, $provider)
	{
		if (empty($api_key)) {
			return false;
		}

		switch ($provider) {
			case 'anthropic':
				return preg_match('/^sk-ant-[a-zA-Z0-9\-_]{95,}$/', $api_key);
			case 'gemini':
				return strlen($api_key) >= 20;
			default:
				return true;
		}
	}

	/**
	 * Validate URL.
	 *
	 * @param    string    $url    URL to validate
	 * @return   bool
	 */
	public static function validate_url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * Validate post ID.
	 *
	 * @param    int    $post_id    Post ID
	 * @return   bool
	 */
	public static function validate_post_id($post_id)
	{
		return is_numeric($post_id) && $post_id > 0 && get_post($post_id) !== null;
	}
}