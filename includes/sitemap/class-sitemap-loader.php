<?php
/**
 * Sitemap Loader - Load all sitemap functionality
 *
 * Include this file from your main plugin class to enable sitemap functionality.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes/sitemap
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
function ai_seo_pro_load_sitemap()
{
	// Load sitemap manager
	require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/sitemap/class-sitemap-manager.php';

	// Initialize sitemap manager
	$sitemap_manager = new AI_SEO_Pro_Sitemap_Manager();
	$sitemap_manager->init();

	// Load and initialize sitemap admin (only in admin area)
	if (is_admin()) {
		require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/class-sitemap-admin.php';

		$sitemap_admin = new AI_SEO_Pro_Sitemap_Admin();
		$sitemap_admin->init();
	}
}

// Initialize sitemap on plugins_loaded with priority 15 (after main plugin init)
add_action('plugins_loaded', 'ai_seo_pro_load_sitemap', 15);
