<?php
/**
 * Redirect Handler - Execute redirects
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/redirects
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class AI_SEO_Pro_Redirect_Handler
 */
class AI_SEO_Pro_Redirect_Handler
{

	/**
	 * Redirect manager instance
	 *
	 * @var AI_SEO_Pro_Redirect_Manager
	 */
	private $manager;

	/**
	 * 404 monitor instance
	 *
	 * @var AI_SEO_Pro_404_Monitor
	 */
	private $monitor_404;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->manager = new AI_SEO_Pro_Redirect_Manager();
		$this->monitor_404 = new AI_SEO_Pro_404_Monitor();
	}

	/**
	 * Initialize hooks
	 */
	public function init()
	{
		// Use multiple hooks to ensure we catch the redirect.
		add_action('init', array($this, 'handle_redirect_early'), 1);
		add_action('wp', array($this, 'handle_404'));
	}

	/**
	 * Handle redirect early in WordPress lifecycle
	 * This runs before WordPress resolves any URLs
	 */
	public function handle_redirect_early()
	{
		// Don't redirect in admin or during AJAX.
		if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
			return;
		}

		// Get current URL.
		$current_url = $this->get_current_url();

		// Normalize URL for matching.
		$normalized_url = $this->normalize_url_for_matching($current_url);

		// Find matching redirect.
		$redirect = $this->manager->find_redirect($normalized_url);

		if (!$redirect) {
			return;
		}

		// Increment hit counter.
		$this->manager->increment_hits($redirect->id);

		// Get target URL.
		$target_url = $redirect->target_url;

		// Handle regex replacements.
		if ($redirect->is_regex) {
			$target_url = preg_replace($redirect->source_url, $redirect->target_url, $normalized_url);
		}

		// Ensure full URL.
		if (0 !== strpos($target_url, 'http')) {
			$target_url = home_url($target_url);
		}

		// Perform redirect based on type.
		$this->perform_redirect($target_url, $redirect->redirect_type);
	}

	/**
	 * Perform redirect
	 *
	 * @param string $target_url    Target URL.
	 * @param string $redirect_type Redirect type.
	 */
	private function perform_redirect($target_url, $redirect_type)
	{
		$status_code = 301;

		switch ($redirect_type) {
			case '302':
				$status_code = 302;
				break;
			case '307':
				$status_code = 307;
				break;
			case '410':
				$status_code = 410;
				// Send proper 410 headers and exit.
				status_header(410);
				nocache_headers();
				wp_die(
					esc_html__('This content has been permanently removed.', 'ai-seo-pro'),
					esc_html__('Gone', 'ai-seo-pro'),
					array('response' => 410)
				);
				exit;
			case '451':
				$status_code = 451;
				// Send proper 451 headers and exit.
				status_header(451);
				nocache_headers();
				wp_die(
					esc_html__('This content is unavailable for legal reasons.', 'ai-seo-pro'),
					esc_html__('Unavailable For Legal Reasons', 'ai-seo-pro'),
					array('response' => 451)
				);
				exit;
			default:
				$status_code = 301;
		}

		// Allow filtering.
		$target_url = apply_filters('ai_seo_pro_redirect_url', $target_url, $redirect_type);
		$status_code = apply_filters('ai_seo_pro_redirect_status', $status_code, $redirect_type);

		// Sanitize URL.
		$target_url = esc_url_raw($target_url);

		// Validate URL before redirecting.
		if (empty($target_url)) {
			return;
		}

		// Perform redirect.
		nocache_headers();

		// Use wp_safe_redirect for internal URLs, wp_redirect for external.
		if (wp_validate_redirect($target_url, false)) {
			wp_safe_redirect($target_url, $status_code);
		} else {
			// For external URLs, use wp_redirect with sanitized URL.
			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- External redirect intentional.
			wp_redirect($target_url, $status_code);
		}
		exit;
	}

	/**
	 * Handle 404 monitoring
	 */
	public function handle_404()
	{
		if (is_404() && get_option('ai_seo_pro_404_monitoring', true)) {
			$this->monitor_404->log_404($this->get_current_url());
		}
	}

	/**
	 * Get current URL from REQUEST_URI
	 * This gets the raw URL BEFORE WordPress processes it
	 *
	 * @return string
	 */
	private function get_current_url()
	{
		if (!isset($_SERVER['REQUEST_URI'])) {
			return '/';
		}

		// Get the request URI.
		$request_uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));

		// Parse to get just the path (remove query string for now).
		$parsed = wp_parse_url($request_uri);
		$path = isset($parsed['path']) ? $parsed['path'] : '/';

		// Remove the site path if WordPress is in a subdirectory.
		$site_path = wp_parse_url(home_url(), PHP_URL_PATH);
		if ($site_path && '/' !== $site_path) {
			$path = preg_replace('#^' . preg_quote($site_path, '#') . '#', '', $path);
		}

		// Add query string back if it exists.
		if (!empty($parsed['query'])) {
			$path .= '?' . $parsed['query'];
		}

		return $path;
	}

	/**
	 * Normalize URL for matching against redirect rules
	 *
	 * @param string $url URL to normalize.
	 * @return string Normalized URL.
	 */
	private function normalize_url_for_matching($url)
	{
		// Parse URL to get path only (no query string for matching).
		$parsed = wp_parse_url($url);
		$path = isset($parsed['path']) ? $parsed['path'] : '/';

		// Remove trailing slash for consistency (except for homepage).
		$path = rtrim($path, '/');
		if (empty($path)) {
			$path = '/';
		}

		return $path;
	}
}