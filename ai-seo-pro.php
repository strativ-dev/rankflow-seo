<?php
/**
 * Plugin Name: AI SEO Pro
 * Plugin URI: https://github.com/strativ-dev/ai-seo-pro
 * Description: AI-powered SEO plugin with meta generation, content analysis, redirects, and 404 monitoring.
 * Version: 1.0.0
 * Author: Strativ AB
 * Author URI: https://github.com/strativ-dev/ai-seo-pro
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-seo-pro
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
define('AI_SEO_PRO_VERSION', '1.0.0');
define('AI_SEO_PRO_PLUGIN_FILE', __FILE__);
define('AI_SEO_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_SEO_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_SEO_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Activation hook
 */
function ai_seo_pro_activate()
{
	require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/class-activator.php';
	AI_SEO_Pro_Activator::activate();
}
register_activation_hook(__FILE__, 'ai_seo_pro_activate');

/**
 * Deactivation hook
 */
function ai_seo_pro_deactivate()
{
	require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/class-deactivator.php';
	AI_SEO_Pro_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'ai_seo_pro_deactivate');

/**
 * Load the core plugin class
 */
require_once AI_SEO_PRO_PLUGIN_DIR . 'includes/class-ai-seo-pro.php';

/**
 * Initialize the plugin
 */
function ai_seo_pro_run()
{
	$plugin = new AI_SEO_Pro();
	$plugin->run();
}
ai_seo_pro_run();