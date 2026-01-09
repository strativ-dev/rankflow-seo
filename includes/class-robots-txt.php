<?php
/**
 * Robots.txt Handler
 *
 * Manages virtual robots.txt file generation and rules
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/includes
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

class AI_SEO_Pro_Robots_Txt
{

    /**
     * The ID of this plugin.
     *
     * @var string $plugin_name
     */
    private $plugin_name;

    /**
     * Constructor
     *
     * @param string $plugin_name The plugin name.
     */
    public function __construct($plugin_name = 'ai-seo-pro')
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * Register settings - called via loader
     */
    public function register_settings()
    {
        // Enable/disable virtual robots.txt.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_enabled',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            )
        );

        // Custom robots.txt content.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_custom_rules',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_robots_rules'),
                'default' => '',
            )
        );

        // Include sitemap.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_include_sitemap',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true,
            )
        );

        // Block AI crawlers - parent toggle.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_block_ai',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            )
        );

        // Individual AI bots array.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_blocked_ai_bots',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_bot_array'),
                'default' => array(),
            )
        );

        // Block bad bots - parent toggle.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_block_bad_bots',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            )
        );

        // Individual bad bots array.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_blocked_bad_bots',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_bot_array'),
                'default' => array(),
            )
        );

        // Disallow wp-admin.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_disallow_wp_admin',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true,
            )
        );

        // Disallow wp-includes.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_disallow_wp_includes',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            )
        );

        // Allow wp-admin/admin-ajax.php.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_allow_ajax',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true,
            )
        );

        // Disallow search results.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_disallow_search',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true,
            )
        );

        // Custom sitemap URLs.
        register_setting(
            'ai_seo_pro_robots_txt',
            'ai_seo_pro_robots_sitemap_urls',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_sitemap_urls'),
                'default' => '',
            )
        );
    }

    /**
     * Sanitize bot array
     *
     * @param mixed $input User input.
     * @return array Sanitized array of bot names.
     */
    public function sanitize_bot_array($input)
    {
        if (!is_array($input)) {
            return array();
        }

        return array_map('sanitize_text_field', $input);
    }

    /**
     * Sanitize robots rules
     *
     * @param string $input User input.
     * @return string Sanitized content.
     */
    public function sanitize_robots_rules($input)
    {
        // Allow robots.txt specific characters and syntax.
        $input = wp_strip_all_tags($input);
        return $input;
    }

    /**
     * Sanitize sitemap URLs
     *
     * @param string $input User input.
     * @return string Sanitized URLs.
     */
    public function sanitize_sitemap_urls($input)
    {
        $lines = explode("\n", $input);
        $clean = array();

        foreach ($lines as $line) {
            $url = trim($line);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $clean[] = esc_url_raw($url);
            }
        }

        return implode("\n", $clean);
    }

    /**
     * Output custom robots.txt - called via filter
     * 
     * Runs at priority 9999 to override any other robots.txt additions.
     *
     * @param string $output Default robots.txt output.
     * @param bool   $public Whether the site is public.
     * @return string Modified robots.txt output.
     */
    public function output_robots_txt($output, $public)
    {
        // Check if custom robots.txt is enabled.
        if (!get_option('ai_seo_pro_robots_enabled', false)) {
            return $output;
        }

        // Generate and return custom robots.txt (replaces everything).
        return $this->generate_robots_txt();
    }

    /**
     * Generate robots.txt content
     *
     * @return string Generated robots.txt content.
     */
    public function generate_robots_txt()
    {
        $lines = array();

        // Add header comment.
        $lines[] = '# Robots.txt generated by AI SEO Pro';
        $lines[] = '# ' . gmdate('Y-m-d H:i:s') . ' UTC';
        $lines[] = '# ' . home_url('/');
        $lines[] = '';

        // Check if site is public.
        if ('0' === get_option('blog_public')) {
            $lines[] = '# Site is not public - blocking all crawlers';
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /';
            $lines[] = '';
            return implode("\n", $lines);
        }

        // Get bot blocking settings.
        $block_all_ai = get_option('ai_seo_pro_robots_block_ai', false);
        $blocked_ai_bots = get_option('ai_seo_pro_robots_blocked_ai_bots', array());
        $block_all_bad = get_option('ai_seo_pro_robots_block_bad_bots', false);
        $blocked_bad_bots = get_option('ai_seo_pro_robots_blocked_bad_bots', array());

        // Determine which AI bots to block.
        $ai_bots_to_block = array();
        if ($block_all_ai) {
            // Block all AI bots.
            $ai_bots_to_block = $this->get_all_ai_bots();
        } elseif (!empty($blocked_ai_bots)) {
            // Block only individually selected bots.
            $ai_bots_to_block = $blocked_ai_bots;
        }

        // Add AI bot blocking rules.
        if (!empty($ai_bots_to_block)) {
            $lines[] = '# Block AI crawlers';
            foreach ($ai_bots_to_block as $bot) {
                $lines[] = 'User-agent: ' . $bot;
                $lines[] = 'Disallow: /';
                $lines[] = '';
            }
        }

        // Determine which bad bots to block.
        $bad_bots_to_block = array();
        if ($block_all_bad) {
            // Block all bad bots.
            $bad_bots_to_block = $this->get_all_bad_bots();
        } elseif (!empty($blocked_bad_bots)) {
            // Block only individually selected bots.
            $bad_bots_to_block = $blocked_bad_bots;
        }

        // Add bad bot blocking rules.
        if (!empty($bad_bots_to_block)) {
            $lines[] = '# Block aggressive SEO crawlers';
            foreach ($bad_bots_to_block as $bot) {
                $lines[] = 'User-agent: ' . $bot;
                $lines[] = 'Disallow: /';
                $lines[] = '';
            }
        }

        // Default User-agent rules.
        $lines[] = '# Rules for all crawlers';
        $lines[] = 'User-agent: *';

        // Disallow wp-admin.
        if (get_option('ai_seo_pro_robots_disallow_wp_admin', true)) {
            $lines[] = 'Disallow: /wp-admin/';
        }

        // Allow admin-ajax.php.
        if (get_option('ai_seo_pro_robots_allow_ajax', true)) {
            $lines[] = 'Allow: /wp-admin/admin-ajax.php';
        }

        // Disallow wp-includes.
        if (get_option('ai_seo_pro_robots_disallow_wp_includes', false)) {
            $lines[] = 'Disallow: /wp-includes/';
        }

        // Disallow search results.
        if (get_option('ai_seo_pro_robots_disallow_search', true)) {
            $lines[] = 'Disallow: /?s=';
            $lines[] = 'Disallow: /search/';
        }

        // Common WordPress paths to consider.
        $lines[] = 'Disallow: /wp-content/plugins/';
        $lines[] = 'Disallow: /wp-content/cache/';
        $lines[] = 'Disallow: /trackback/';
        $lines[] = 'Disallow: /feed/';
        $lines[] = 'Disallow: /comments/feed/';
        $lines[] = 'Disallow: */trackback/';
        $lines[] = 'Disallow: */feed/';

        // Allow important resources.
        $lines[] = '';
        $lines[] = '# Allow important resources';
        $lines[] = 'Allow: /wp-content/uploads/';
        $lines[] = 'Allow: /wp-content/themes/';
        $lines[] = 'Allow: /*.css';
        $lines[] = 'Allow: /*.js';
        $lines[] = 'Allow: /*.jpg';
        $lines[] = 'Allow: /*.jpeg';
        $lines[] = 'Allow: /*.png';
        $lines[] = 'Allow: /*.gif';
        $lines[] = 'Allow: /*.webp';
        $lines[] = 'Allow: /*.svg';

        // Custom rules.
        $custom_rules = get_option('ai_seo_pro_robots_custom_rules', '');
        if (!empty(trim($custom_rules))) {
            $lines[] = '';
            $lines[] = '# Custom rules';
            $lines[] = trim($custom_rules);
        }

        // Sitemap URLs.
        $lines[] = '';
        $lines[] = '# Sitemaps';

        // Include plugin sitemap if enabled.
        if (get_option('ai_seo_pro_robots_include_sitemap', true)) {
            // Check if our sitemap is enabled.
            if (get_option('ai_seo_pro_sitemap_enabled', false)) {
                $lines[] = 'Sitemap: ' . home_url('sitemap_index.xml');
            }
        }

        // Custom sitemap URLs.
        $custom_sitemaps = get_option('ai_seo_pro_robots_sitemap_urls', '');
        if (!empty(trim($custom_sitemaps))) {
            $sitemap_urls = explode("\n", $custom_sitemaps);
            foreach ($sitemap_urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $lines[] = 'Sitemap: ' . $url;
                }
            }
        }

        // Add default WordPress sitemap if exists and no other sitemap added.
        if (!get_option('ai_seo_pro_sitemap_enabled', false) && empty(trim($custom_sitemaps))) {
            $lines[] = 'Sitemap: ' . home_url('wp-sitemap.xml');
        }

        $lines[] = '';
        $lines[] = '# End of robots.txt';

        return implode("\n", $lines);
    }

    /**
     * Get all AI bot keys
     *
     * @return array List of all AI crawler user agents.
     */
    private function get_all_ai_bots()
    {
        return array(
            'GPTBot',
            'ChatGPT-User',
            'Google-Extended',
            'CCBot',
            'anthropic-ai',
            'ClaudeBot',
            'Claude-Web',
            'Bytespider',
            'Omgilibot',
            'FacebookBot',
            'Diffbot',
            'Applebot-Extended',
            'PerplexityBot',
            'cohere-ai',
        );
    }

    /**
     * Get all bad bot keys
     *
     * @return array List of all aggressive SEO crawler user agents.
     */
    private function get_all_bad_bots()
    {
        return array(
            'AhrefsBot',
            'SemrushBot',
            'MJ12bot',
            'DotBot',
            'BLEXBot',
            'SearchmetricsBot',
            'PetalBot',
            'MegaIndex',
            'SEOkicks',
            'linkdexbot',
            'Sogou',
            'BaiduSpider',
            'YandexBot',
            'Exabot',
            'MauiBot',
            'SeznamBot',
            'Rogerbot',
            'MixnodeCache',
            'AspiegelBot',
        );
    }

    /**
     * AJAX handler for preview
     */
    public function ajax_preview_robots()
    {
        check_ajax_referer('ai_seo_pro_robots_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
        }

        // Show appropriate preview based on enabled status.
        if (get_option('ai_seo_pro_robots_enabled', false)) {
            $preview = $this->generate_robots_txt();
        } else {
            $preview = $this->get_default_robots_txt();
        }

        wp_send_json_success(array('content' => $preview));
    }

    /**
     * AJAX handler for reset
     */
    public function ajax_reset_robots()
    {
        check_ajax_referer('ai_seo_pro_robots_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'ai-seo-pro')));
        }

        // Reset all options to defaults.
        update_option('ai_seo_pro_robots_enabled', false);
        update_option('ai_seo_pro_robots_custom_rules', '');
        update_option('ai_seo_pro_robots_include_sitemap', true);
        update_option('ai_seo_pro_robots_block_ai', false);
        update_option('ai_seo_pro_robots_blocked_ai_bots', array());
        update_option('ai_seo_pro_robots_block_bad_bots', false);
        update_option('ai_seo_pro_robots_blocked_bad_bots', array());
        update_option('ai_seo_pro_robots_disallow_wp_admin', true);
        update_option('ai_seo_pro_robots_disallow_wp_includes', false);
        update_option('ai_seo_pro_robots_allow_ajax', true);
        update_option('ai_seo_pro_robots_disallow_search', true);
        update_option('ai_seo_pro_robots_sitemap_urls', '');

        wp_send_json_success(array('message' => __('Settings reset to defaults.', 'ai-seo-pro')));
    }

    /**
     * Get default WordPress robots.txt content
     *
     * Shows what robots.txt will look like when the editor is disabled.
     *
     * @return string Default WordPress robots.txt.
     */
    public function get_default_robots_txt()
    {
        $public = get_option('blog_public');

        $output = "User-agent: *\n";
        if ('0' === (string) $public) {
            $output .= "Disallow: /\n";
        } else {
            $output .= "Disallow: /wp-admin/\n";
            $output .= "Allow: /wp-admin/admin-ajax.php\n";
        }

        $output .= "\n";

        // Check which sitemap to show.
        if (get_option('ai_seo_pro_sitemap_enabled', false)) {
            // AI SEO Pro sitemap is enabled - show that instead of WordPress default.
            $output .= "# AI SEO Pro Sitemap\n";
            $output .= "Sitemap: " . home_url('sitemap_index.xml') . "\n";
        } else {
            // Default WordPress sitemap.
            $output .= "Sitemap: " . home_url('wp-sitemap.xml') . "\n";
        }

        return $output;
    }

    /**
     * Check if physical robots.txt exists
     *
     * @return bool True if physical file exists.
     */
    public function physical_file_exists()
    {
        $robots_path = ABSPATH . 'robots.txt';
        return file_exists($robots_path);
    }

    /**
     * Get physical robots.txt content
     *
     * @return string|false File content or false if not exists.
     */
    public function get_physical_file_content()
    {
        $robots_path = ABSPATH . 'robots.txt';
        if (file_exists($robots_path)) {
            return file_get_contents($robots_path);
        }
        return false;
    }
}