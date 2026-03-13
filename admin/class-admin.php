<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin
 * @author     Strativ AB
 */
class MPSEO_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @var string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string $version
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_styles($hook)
	{

		// Load on all post edit screens and plugin settings page.
		if (
			in_array($hook, array('post.php', 'post-new.php'), true) ||
			strpos($hook, 'metapilot-smart-seo') !== false
		) {

			wp_enqueue_style(
				$this->plugin_name . '-admin',
				MPSEO_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				$this->plugin_name . '-metabox',
				MPSEO_PLUGIN_URL . 'assets/css/metabox.css',
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				$this->plugin_name . '-settings',
				MPSEO_PLUGIN_URL . 'assets/css/settings.css',
				array($this->plugin_name . '-admin'),
				$this->version,
				'all'
			);

			// Admin views styles - consolidated from inline styles.
			wp_enqueue_style(
				$this->plugin_name . '-admin-views',
				MPSEO_PLUGIN_URL . 'assets/css/admin-views.css',
				array($this->plugin_name . '-admin'),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts($hook)
	{

		if (in_array($hook, array('post.php', 'post-new.php'), true)) {

			wp_enqueue_script(
				$this->plugin_name . '-character-counter',
				MPSEO_PLUGIN_URL . 'assets/js/character-counter.js',
				array('jquery'),
				$this->version,
				true
			);

			wp_enqueue_script(
				$this->plugin_name . '-metabox',
				MPSEO_PLUGIN_URL . 'assets/js/metabox.js',
				array('jquery', $this->plugin_name . '-character-counter'),
				$this->version,
				true
			);

			wp_enqueue_script(
				$this->plugin_name . '-admin',
				MPSEO_PLUGIN_URL . 'assets/js/admin.js',
				array('jquery', 'wp-util'),
				$this->version,
				true
			);

			// FIXED: Properly localize script with all required data.
			$post_id = get_the_ID();
			wp_localize_script(
				$this->plugin_name . '-metabox',
				'mpseoData',
				array(
					'ajaxUrl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('mpseo_nonce'),
					'postId' => $post_id ? $post_id : 0,
					'strings' => array(
						'generating' => __('Generating...', 'metapilot-smart-seo'),
						'analyzing' => __('Analyzing content...', 'metapilot-smart-seo'),
						'success' => __('Successfully generated!', 'metapilot-smart-seo'),
						'error' => __('An error occurred. Please try again.', 'metapilot-smart-seo'),
						'noContent' => __('Please add content first.', 'metapilot-smart-seo'),
						'confirmRegenerate' => __('This will replace existing meta tags. Continue?', 'metapilot-smart-seo'),
					),
				)
			);
		}

		if (strpos($hook, 'metapilot-smart-seo') !== false) {
			wp_enqueue_script(
				$this->plugin_name . '-settings',
				MPSEO_PLUGIN_URL . 'assets/js/settings.js',
				array('jquery'),
				$this->version,
				true
			);

			// Localize for settings page too.
			wp_localize_script(
				$this->plugin_name . '-settings',
				'mpseoData',
				array(
					'ajaxUrl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('mpseo_nonce'),
				)
			);
		}

		// Admin views scripts - consolidated from inline scripts.
		// Load on all post edit screens and plugin settings pages.
		if (
			in_array($hook, array('post.php', 'post-new.php'), true) ||
			strpos($hook, 'metapilot-smart-seo') !== false
		) {
			wp_enqueue_script(
				$this->plugin_name . '-admin-views',
				MPSEO_PLUGIN_URL . 'assets/js/admin-views.js',
				array('jquery'),
				$this->version,
				true
			);

			// Localize for admin-views.js
			wp_localize_script(
				$this->plugin_name . '-admin-views',
				'mpseoViewsData',
				array(
					'ajaxUrl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('mpseo_nonce'),
					'strings' => array(
						'selectOgImage' => __('Select Open Graph Image', 'metapilot-smart-seo'),
						'selectDefaultOgImage' => __('Select Default OG Image', 'metapilot-smart-seo'),
						'useThisImage' => __('Use this image', 'metapilot-smart-seo'),
						'removeImage' => __('Remove image', 'metapilot-smart-seo'),
						'noImage' => __('No image', 'metapilot-smart-seo'),
						'copied' => __('Copied!', 'metapilot-smart-seo'),
						'copyToClipboard' => __('Copy to Clipboard', 'metapilot-smart-seo'),
						'pinging' => __('Pinging...', 'metapilot-smart-seo'),
						'pingSitemap' => __('Ping Search Engines', 'metapilot-smart-seo'),
						'pingSuccess' => __('Sitemap pinged successfully!', 'metapilot-smart-seo'),
					),
				)
			);
		}

		// Ensure media uploader is available
		wp_enqueue_media();
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function add_action_links($links)
	{
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url('admin.php?page=metapilot-smart-seo'),
			__('Settings', 'metapilot-smart-seo')
		);

		array_unshift($links, $settings_link);

		return $links;
	}

	/**
	 * Show admin notices.
	 */
	public function show_admin_notices()
	{
		// Check if API key is configured.
		$api_key = get_option('mpseo_api_key');

		if (empty($api_key) && current_user_can('manage_options')) {
			$this->render_notice(
				sprintf(
					/* translators: %s: URL to the API settings page */
					__('Metapilot Smart SEO requires an API key to function. <a href="%s">Configure it now</a>.', 'metapilot-smart-seo'),
					admin_url('admin.php?page=metapilot-smart-seo&tab=api')
				),
				'warning'
			);
		}

		// Show activation notice.
		if (get_transient('mpseo_activation_redirect')) {
			delete_transient('mpseo_activation_redirect');

			$this->render_notice(
				sprintf(
					/* translators: %s: URL to the plugin settings page */
					__('Thank you for installing Metapilot Smart SEO! <a href="%s">Get started</a> by configuring your settings.', 'metapilot-smart-seo'),
					admin_url('admin.php?page=metapilot-smart-seo')
				),
				'success',
				true
			);
		}
	}

	/**
	 * Render admin notice.
	 *
	 * @param string $message     The notice message.
	 * @param string $type        Notice type (success, error, warning, info).
	 * @param bool   $dismissible Whether the notice is dismissible.
	 */
	private function render_notice($message, $type = 'info', $dismissible = false)
	{
		$class = 'notice notice-' . $type;
		if ($dismissible) {
			$class .= ' is-dismissible';
		}

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			esc_attr($class),
			wp_kses_post($message)
		);
	}

	/**
	 * Check for conflicting plugins.
	 *
	 * @return bool True if conflicts detected.
	 */
	private function check_plugin_conflicts()
	{
		$conflicting_plugins = array(
			'wordpress-seo/wp-seo.php' => 'Yoast SEO',
			'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All in One SEO',
			'seo-by-rank-math/rank-math.php' => 'Rank Math',
			'autodescription/autodescription.php' => 'The SEO Framework',
		);

		$active_plugins = get_option('active_plugins');
		$conflicts = array();

		foreach ($conflicting_plugins as $plugin => $name) {
			if (in_array($plugin, $active_plugins, true)) {
				$conflicts[] = $name;
			}
		}

		if (!empty($conflicts)) {
			$this->render_notice(
				sprintf(
					/* translators: %s: comma-separated list of conflicting plugin names */
					__('Metapilot Smart SEO has detected conflicting SEO plugins: %s. For best results, please deactivate them.', 'metapilot-smart-seo'),
					implode(', ', $conflicts)
				),
				'warning',
				true
			);
			return true;
		}

		return false;
	}
}