<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes
 * @author     Strativ AB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}
class AI_SEO_Pro
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var      AI_SEO_Pro_Loader    $loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct()
	{
		$this->version = AI_SEO_PRO_VERSION;
		$this->plugin_name = 'ai-seo-pro';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies()
	{

		// Core classes
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/class-loader.php';

		// Utility classes
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/utils/class-helper.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/utils/class-validator.php';

		// API classes
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/api/class-api-base.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/api/class-anthropic-api.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/api/class-gemini-api.php';

		// Redirect system
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/redirects/class-redirect-manager.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/redirects/class-redirect-handler.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/redirects/class-404-monitor.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-redirect-admin.php';

		// Core functionality
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-meta-manager.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-content-analyzer.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-seo-score.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-schema-generator.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-seo-analyzer.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/core/class-readability-analyzer.php';

		// Admin classes
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-admin.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-settings.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-metabox.php';

		// Public classes
		require_once AI_SEO_PRO_PLUGIN_DIR . 'public/class-public.php';

		// In ai-seo-pro.php, after loading the main class
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/sitemap/class-sitemap-loader.php';

		// Robots.txt
		require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/class-robots-txt.php';

		// Schema
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-schema-admin.php';
		require_once AI_SEO_PRO_PLUGIN_DIR . 'public/class-schema-output.php';

		$this->loader = new AI_SEO_Pro_Loader();
	}

	/**
	 * Register all hooks related to admin functionality.
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new AI_SEO_Pro_Admin($this->get_plugin_name(), $this->get_version());

		// Enqueue styles and scripts
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// Settings page
		$plugin_settings = new AI_SEO_Pro_Settings($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_menu', $plugin_settings, 'add_plugin_admin_menu');
		$this->loader->add_action('admin_init', $plugin_settings, 'register_settings');

		// Meta box
		$plugin_metabox = new AI_SEO_Pro_Metabox($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('add_meta_boxes', $plugin_metabox, 'add_meta_boxes');
		$this->loader->add_action('save_post', $plugin_metabox, 'save_meta_data', 10, 2);

		// Redirect admin
		$redirect_admin = new AI_SEO_Pro_Redirect_Admin($this->get_plugin_name());
		$this->loader->add_action('admin_notices', $redirect_admin, 'display_notices');

		// AJAX handlers
		$this->loader->add_action('wp_ajax_ai_seo_generate_meta', $plugin_metabox, 'ajax_generate_meta');
		$this->loader->add_action('wp_ajax_ai_seo_analyze_content', $plugin_metabox, 'ajax_analyze_content');
		$this->loader->add_action('wp_ajax_ai_seo_calculate_score', $plugin_metabox, 'ajax_calculate_score');

		// Plugin action links
		$this->loader->add_filter('plugin_action_links_' . AI_SEO_PRO_PLUGIN_BASENAME, $plugin_admin, 'add_action_links');

		// Robots.txt
		$robots_txt = new AI_SEO_Pro_Robots_Txt($this->get_plugin_name());
		$this->loader->add_action('admin_init', $robots_txt, 'register_settings');
		$this->loader->add_action('wp_ajax_ai_seo_pro_preview_robots', $robots_txt, 'ajax_preview_robots');
		$this->loader->add_action('wp_ajax_ai_seo_pro_reset_robots', $robots_txt, 'ajax_reset_robots');

		// Schema Admin
		$schema_admin = new AI_SEO_Pro_Schema_Admin($this->get_plugin_name());
		$this->loader->add_action('admin_init', $schema_admin, 'register_settings');
		$this->loader->add_action('admin_enqueue_scripts', $schema_admin, 'enqueue_scripts');
	}

	/**
	 * Register all hooks related to public-facing functionality.
	 */
	private function define_public_hooks()
	{
		$plugin_public = new AI_SEO_Pro_Public($this->get_plugin_name(), $this->get_version());

		// Flush rewrite rules after activation (for sitemap)
		$this->loader->add_action('init', $this, 'maybe_flush_rewrite_rules', 999);

		// Meta tags output
		$this->loader->add_action('wp_head', $plugin_public, 'output_meta_tags', 1);

		// Schema markup
		$this->loader->add_action('wp_head', $plugin_public, 'output_schema_markup', 2);

		// Site verification meta tags
		$this->loader->add_action('wp_head', 'AI_SEO_Pro_Settings', 'output_verification_meta', 1);

		// Google Tag Manager.
		$this->loader->add_action('wp_head', 'AI_SEO_Pro_Settings', 'output_gtm_head', 1);
		$this->loader->add_action('wp_body_open', 'AI_SEO_Pro_Settings', 'output_gtm_body', 1);

		// Redirect handler
		$redirect_handler = new AI_SEO_Pro_Redirect_Handler();
		$redirect_handler->init();

		// Remove default WordPress meta
		$this->loader->add_action('init', $plugin_public, 'remove_default_meta');

		// ADDED: Filter document title
		$this->loader->add_filter('pre_get_document_title', $plugin_public, 'filter_document_title', 10);
		$this->loader->add_filter('document_title_parts', $plugin_public, 'filter_document_title_parts', 10);

		// Robots.txt filter
		$robots_txt = new AI_SEO_Pro_Robots_Txt($this->get_plugin_name());
		$this->loader->add_filter('robots_txt', $robots_txt, 'output_robots_txt', 9999, 2);

		// Schema Output
		$schema_output = new AI_SEO_Pro_Schema_Output();
		$this->loader->add_action('wp_head', $schema_output, 'output_schema', 5);
	}

	/**
	 * Run the loader to execute all hooks.
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin.
	 *
	 * @return    string
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the loader class.
	 *
	 * @return    AI_SEO_Pro_Loader
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number.
	 *
	 * @return    string
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Maybe flush rewrite rules after activation.
	 */
	public function maybe_flush_rewrite_rules()
	{
		if (get_option('ai_seo_pro_flush_rewrite_rules')) {
			flush_rewrite_rules();
			delete_option('ai_seo_pro_flush_rewrite_rules');
		}
	}
}