<?php
/**
 * Plugin Name: RankFlow SEO
 * Plugin URI: https://github.com/strativ-dev/rankflow-seo
 * Description: AI-powered SEO plugin with meta generation, content analysis, redirects, and 404 monitoring.
 * Version: 1.0.0
 * Author: Strativ AB
 * Author URI: https://github.com/strativ-dev/rankflow-seo
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rankflow-seo
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
define('RANKFLOW_SEO_VERSION', '1.0.0');
define('RANKFLOW_SEO_PLUGIN_FILE', __FILE__);
define('RANKFLOW_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RANKFLOW_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RANKFLOW_SEO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Activation hook
 */
function rankflow_seo_activate()
{
	require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/class-activator.php';
	RankFlow_SEO_Activator::activate();
}
register_activation_hook(__FILE__, 'rankflow_seo_activate');

/**
 * Deactivation hook
 */
function rankflow_seo_deactivate()
{
	require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/class-deactivator.php';
	RankFlow_SEO_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'rankflow_seo_deactivate');

/**
 * Load the core plugin class
 */
require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/class-rankflow-seo.php';

/**
 * Initialize the plugin
 */
function rankflow_seo_run()
{
	$plugin = new RankFlow_SEO();
	$plugin->run();
}
rankflow_seo_run();