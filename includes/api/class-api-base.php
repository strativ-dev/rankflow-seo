<?php
/**
 * Base API class for all AI providers
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/api
 * @author     Strativ AB
 */
abstract class AI_SEO_Pro_API_Base
{

	/**
	 * API key
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * API endpoint
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Request timeout
	 *
	 * @var int
	 */
	protected $timeout = 30;

	/**
	 * Constructor
	 *
	 * @param string $api_key API key.
	 */
	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	/**
	 * Generate meta tags
	 *
	 * @param string $title         Post title.
	 * @param string $content       Post content.
	 * @param string $focus_keyword Focus keyword.
	 * @return array|WP_Error
	 */
	abstract public function generate_meta_tags($title, $content, $focus_keyword = '');

	/**
	 * Prepare content for API
	 *
	 * @param string $content Content.
	 * @return string
	 */
	protected function prepare_content($content)
	{
		// Strip HTML tags.
		$clean_content = wp_strip_all_tags($content);

		// Remove extra whitespace.
		$clean_content = preg_replace('/\s+/', ' ', $clean_content);

		// Limit to 2000 words.
		$clean_content = wp_trim_words($clean_content, 2000, '');

		return trim($clean_content);
	}

	/**
	 * Build prompt for AI
	 *
	 * @param string $title         Title.
	 * @param string $content       Content.
	 * @param string $focus_keyword Focus keyword.
	 * @return string
	 */
	protected function build_prompt($title, $content, $focus_keyword = '')
	{
		$prompt = "You are an expert SEO specialist. Generate SEO-optimized meta tags for the following content.\n\n";
		$prompt .= "Title: {$title}\n\n";

		if (!empty($focus_keyword)) {
			$prompt .= "Focus Keyword: {$focus_keyword}\n\n";
		}

		$prompt .= "Content: {$content}\n\n";
		$prompt .= "Requirements:\n";
		$prompt .= "1. Meta Title: 50-60 characters, engaging and includes focus keyword (if provided)\n";
		$prompt .= "2. Meta Description: 150-160 characters, compelling with call-to-action\n";
		$prompt .= "3. Keywords: 5-8 relevant keywords, comma-separated\n";
		$prompt .= "4. Open Graph Title: Engaging social media title (can be different from meta title)\n";
		$prompt .= "5. Open Graph Description: Compelling social media description\n\n";
		$prompt .= "Return ONLY valid JSON in this exact format:\n";
		$prompt .= '{"title": "...", "description": "...", "keywords": "...", "og_title": "...", "og_description": "..."}';

		return $prompt;
	}

	/**
	 * Parse JSON response - IMPROVED VERSION
	 *
	 * @param string $response Response content.
	 * @return array|WP_Error
	 */
	protected function parse_json_response($response)
	{
		// Try to extract JSON from response (handles cases where AI adds explanation text).
		preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $response, $matches);

		if (empty($matches[0])) {
			return new WP_Error('invalid_response', __('Could not find JSON in API response. AI may have returned plain text instead of JSON.', 'ai-seo-pro'));
		}

		$json_string = $matches[0];

		$data = json_decode($json_string, true);

		if (JSON_ERROR_NONE !== json_last_error()) {
			return new WP_Error(
				'json_error',
				sprintf(
					/* translators: %s: JSON error message */
					__('Invalid JSON in API response: %s', 'ai-seo-pro'),
					json_last_error_msg()
				)
			);
		}

		// Validate required fields.
		$required_fields = array('title', 'description', 'keywords');
		$missing_fields = array();

		foreach ($required_fields as $field) {
			if (empty($data[$field])) {
				$missing_fields[] = $field;
			}
		}

		if (!empty($missing_fields)) {
			return new WP_Error(
				'missing_fields',
				sprintf(
					/* translators: %s: comma-separated list of missing field names */
					__('Missing required fields in API response: %s', 'ai-seo-pro'),
					implode(', ', $missing_fields)
				)
			);
		}

		// Ensure character limits.
		$data['title'] = substr($data['title'], 0, 60);
		$data['description'] = substr($data['description'], 0, 160);

		// Clean up og_ fields if they exist.
		if (isset($data['og_title'])) {
			$data['og_title'] = substr($data['og_title'], 0, 60);
		}
		if (isset($data['og_description'])) {
			$data['og_description'] = substr($data['og_description'], 0, 160);
		}

		return $data;
	}

	/**
	 * Make HTTP request
	 *
	 * @param string $url     Request URL.
	 * @param array  $headers Headers.
	 * @param mixed  $body    Body.
	 * @return array|WP_Error
	 */
	protected function make_request($url, $headers, $body)
	{
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => $this->timeout,
				'headers' => $headers,
				'body' => is_array($body) ? wp_json_encode($body) : $body,
			)
		);

		if (is_wp_error($response)) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code($response);
		$response_body = wp_remote_retrieve_body($response);

		if (200 !== $status_code) {
			$error_data = json_decode($response_body, true);
			$error_message = isset($error_data['error']['message'])
				? $error_data['error']['message']
				: sprintf(
					/* translators: %d: HTTP status code */
					__('API request failed with status code: %d', 'ai-seo-pro'),
					$status_code
				);

			return new WP_Error('api_error', $error_message);
		}

		return json_decode($response_body, true);
	}
}