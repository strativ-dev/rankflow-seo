<?php
/**
 * Plugin Name: Metapilot Smart SEO
 * Plugin URI: https://github.com/strativ-dev/rankflow-seo
 * Description: AI-powered SEO plugin with meta generation, content analysis, redirects, and 404 monitoring.
 * Version: 1.0.0
 * Author: Strativ AB
 * Author URI: https://profiles.wordpress.org/wpstrativ/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: metapilot-smart-seo
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Plugin constants
 */
define('MPSEO_VERSION', '1.0.0');
define('MPSEO_PLUGIN_FILE', __FILE__);
define('MPSEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MPSEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MPSEO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Activation hook
 */
function mpseo_activate()
{
	require_once MPSEO_PLUGIN_DIR . 'includes/class-activator.php';
	MPSEO_Activator::activate();
}
register_activation_hook(__FILE__, 'mpseo_activate');

/**
 * Deactivation hook
 */
function mpseo_deactivate()
{
	require_once MPSEO_PLUGIN_DIR . 'includes/class-deactivator.php';
	MPSEO_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'mpseo_deactivate');

/**
 * Load the core plugin class
 */
require_once MPSEO_PLUGIN_DIR . 'includes/class-metapilot-smart-seo.php';

/**
 * Initialize the plugin
 */
function mpseo_run()
{
	$plugin = new MPSEO();
	$plugin->run();
}
mpseo_run();