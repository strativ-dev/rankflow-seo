<?php
/**
 * Define the internationalization functionality.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes
 * @author     Strativ AB
 */
class AI_SEO_Pro_i18n
{

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'ai-seo-pro',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}