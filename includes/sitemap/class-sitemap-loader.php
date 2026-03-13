<?php
/**
 * Sitemap Loader - Load all sitemap functionality
 *
 * Include this file from your main plugin class to enable sitemap functionality.
 *
 * @package    MPSEO
 * @subpackage MPSEO/includes/sitemap
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
function mpseo_load_sitemap()
{
	// Always load sitemap admin in admin area so settings can be saved even when disabled.
	if (is_admin()) {
		require_once MPSEO_PLUGIN_DIR . 'admin/class-sitemap-admin.php';

		$sitemap_admin = new MPSEO_Sitemap_Admin();
		$sitemap_admin->init();
	}

	// Only initialize frontend sitemap manager when module is enabled.
	if (!get_option('mpseo_sitemap_enabled', true)) {
		return;
	}

	// Load sitemap manager
	require_once MPSEO_PLUGIN_DIR . 'includes/sitemap/class-sitemap-manager.php';

	// Initialize sitemap manager
	$sitemap_manager = new MPSEO_Sitemap_Manager();
	$sitemap_manager->init();
}

// Initialize sitemap on plugins_loaded with priority 15 (after main plugin init)
add_action('plugins_loaded', 'mpseo_load_sitemap', 15);
