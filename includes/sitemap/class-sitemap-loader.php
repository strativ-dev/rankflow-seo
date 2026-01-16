<?php
/**
 * Sitemap Loader - Load all sitemap functionality
 *
 * Include this file from your main plugin class to enable sitemap functionality.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/includes/sitemap
 * @author     Strativ AB
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Load and initialize sitemap functionality
 *
 * @since 1.0.0
 */
function rankflow_seo_load_sitemap()
{
	// Load sitemap manager
	require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/sitemap/class-sitemap-manager.php';

	// Initialize sitemap manager
	$sitemap_manager = new RankFlow_SEO_Sitemap_Manager();
	$sitemap_manager->init();

	// Load and initialize sitemap admin (only in admin area)
	if (is_admin()) {
		require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/class-sitemap-admin.php';

		$sitemap_admin = new RankFlow_SEO_Sitemap_Admin();
		$sitemap_admin->init();
	}
}

// Initialize sitemap on plugins_loaded with priority 15 (after main plugin init)
add_action('plugins_loaded', 'rankflow_seo_load_sitemap', 15);
