<?php
/**
 * Google Gemini API implementation
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/api
 * @author     Strativ AB
 */
class AI_SEO_Pro_Gemini_API extends AI_SEO_Pro_API_Base
{

	/**
	 * API endpoint
	 *
	 * @var string
	 */
	protected $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

	/**
	 * Generate meta tags using Google Gemini
	 *
	 * @param string $title         Post title.
	 * @param string $content       Post content.
	 * @param string $focus_keyword Focus keyword.
	 * @return array|WP_Error
	 */
	public function generate_meta_tags($title, $content, $focus_keyword = '')
	{

		if (empty($this->api_key)) {
			return new WP_Error('no_api_key', __('Google API key is not configured', 'ai-seo-pro'));
		}

		// Prepare content.
		$clean_content = $this->prepare_content($content);

		if (empty($clean_content)) {
			return new WP_Error('no_content', __('Content is empty', 'ai-seo-pro'));
		}

		// Build prompt.
		$prompt = $this->build_prompt($title, $clean_content, $focus_keyword);

		// Make API request.
		$url = $this->endpoint . '?key=' . $this->api_key;

		$headers = array(
			'Content-Type' => 'application/json',
		);

		$body = array(
			'contents' => array(
				array(
					'parts' => array(
						array(
							'text' => $prompt,
						),
					),
				),
			),
			'generationConfig' => array(
				'temperature' => 0.7,
				'maxOutputTokens' => 4096,
			),
		);

		$response = $this->make_request($url, $headers, $body);

		if (is_wp_error($response)) {
			return $response;
		}

		// Check for API errors.
		if (isset($response['error'])) {
			return new WP_Error(
				'gemini_api_error',
				sprintf(
					/* translators: 1: error code, 2: error message */
					__('Gemini API Error (%1$s): %2$s', 'ai-seo-pro'),
					$response['error']['code'] ?? 'unknown',
					$response['error']['message'] ?? 'Unknown error'
				)
			);
		}

		// Check for safety blocking.
		if (
			isset($response['candidates'][0]['finishReason']) &&
			'SAFETY' === $response['candidates'][0]['finishReason']
		) {
			return new WP_Error(
				'safety_blocked',
				__('Content was blocked by Gemini safety filters. Try different content.', 'ai-seo-pro')
			);
		}

		// Extract content from response.
		if (!isset($response['candidates'][0]['content']['parts'][0]['text'])) {
			// More detailed error.
			return new WP_Error(
				'invalid_response',
				__('Invalid response from Google Gemini. Check WordPress error logs for details.', 'ai-seo-pro')
			);
		}

		$content = $response['candidates'][0]['content']['parts'][0]['text'];

		// Parse JSON.
		return $this->parse_json_response($content);
	}
}