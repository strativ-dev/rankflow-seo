<?php
/**
 * API Factory
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/api
 * @author     Strativ AB
 */
class RankFlow_SEO_API_Factory
{

	/**
	 * Create API instance based on provider
	 *
	 * @param    string    $provider    Provider name
	 * @param    string    $api_key     API key
	 * @return   RankFlow_SEO_API_Base|WP_Error
	 */
	public static function create($provider, $api_key)
	{

		if (empty($api_key)) {
			return new WP_Error('no_api_key', __('API key is required', 'rankflow-seo'));
		}

		switch ($provider) {
			case 'anthropic':
				return new RankFlow_SEO_Anthropic_API($api_key);

			case 'gemini':
				return new RankFlow_SEO_Gemini_API($api_key);

			default:
				return new WP_Error('invalid_provider', __('Invalid API provider', 'rankflow-seo'));
		}
	}

	/**
	 * Get available providers
	 *
	 * @return   array
	 */
	public static function get_providers()
	{
		return array(
			'anthropic' => array(
				'name' => 'Anthropic',
				'description' => __('Claude AI models', 'rankflow-seo'),
				'url' => 'https://www.anthropic.com/',
			),
			'gemini' => array(
				'name' => 'Google Gemini',
				'description' => __('Google\'s Gemini AI models', 'rankflow-seo'),
				'url' => 'https://ai.google.dev/',
			),
		);
	}

	/**
	 * Test API connection
	 *
	 * @param    string    $provider    Provider name
	 * @param    string    $api_key     API key
	 * @return   bool|WP_Error
	 */
	public static function test_connection($provider, $api_key)
	{
		$api = self::create($provider, $api_key);

		if (is_wp_error($api)) {
			return $api;
		}

		// Test with a simple request
		$result = $api->generate_meta_tags(
			'Test Title',
			'This is a test content to verify the API connection is working properly.',
			'test'
		);

		if (is_wp_error($result)) {
			return $result;
		}

		return true;
	}
}