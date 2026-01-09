<?php
/**
 * Site Connections Settings Tab View
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings.
$ai_seo_pro_ahrefs_code = get_option('ai_seo_pro_ahrefs_verification', '');
$ai_seo_pro_baidu_code = get_option('ai_seo_pro_baidu_verification', '');
$ai_seo_pro_bing_code = get_option('ai_seo_pro_bing_verification', '');
$ai_seo_pro_google_code = get_option('ai_seo_pro_google_verification', '');
$ai_seo_pro_pinterest_code = get_option('ai_seo_pro_pinterest_verification', '');
$ai_seo_pro_yandex_code = get_option('ai_seo_pro_yandex_verification', '');
?>

<div class="ai-seo-pro-site-connections-tab">
    <h2><?php esc_html_e('Site Connections', 'ai-seo-pro'); ?></h2>

    <p class="description" style="font-size: 14px; margin-bottom: 20px;">
        <?php esc_html_e('Verify your site with different tools. This will add a verification meta tag to your homepage. You can find instructions on how to verify your site for each platform by following the link in the description.', 'ai-seo-pro'); ?>
    </p>

    <hr style="margin: 20px 0;">

    <form method="post" action="options.php">
        <?php settings_fields('ai_seo_pro_site_connections'); ?>

        <!-- Ahrefs -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_ahrefs_verification">
                <strong><?php esc_html_e('Ahrefs', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_ahrefs_verification" name="ai_seo_pro_ahrefs_verification"
                value="<?php echo esc_attr($ai_seo_pro_ahrefs_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Ahrefs link */
                    esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                    '<a href="https://ahrefs.com/webmaster-tools" target="_blank" rel="noopener noreferrer">' . esc_html__('Ahrefs', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Baidu -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_baidu_verification">
                <strong><?php esc_html_e('Baidu', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_baidu_verification" name="ai_seo_pro_baidu_verification"
                value="<?php echo esc_attr($ai_seo_pro_baidu_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Baidu Webmaster tools link */
                    esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                    '<a href="https://ziyuan.baidu.com/site/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Baidu Webmaster tools', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Bing -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_bing_verification">
                <strong><?php esc_html_e('Bing', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_bing_verification" name="ai_seo_pro_bing_verification"
                value="<?php echo esc_attr($ai_seo_pro_bing_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Bing Webmaster tools link */
                    esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                    '<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener noreferrer">' . esc_html__('Bing Webmaster tools', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Google -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_google_verification">
                <strong><?php esc_html_e('Google', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_google_verification" name="ai_seo_pro_google_verification"
                value="<?php echo esc_attr($ai_seo_pro_google_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Google Search console link */
                    esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                    '<a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Search console', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Pinterest -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_pinterest_verification">
                <strong><?php esc_html_e('Pinterest', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_pinterest_verification" name="ai_seo_pro_pinterest_verification"
                value="<?php echo esc_attr($ai_seo_pro_pinterest_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Pinterest link */
                    esc_html__('Claim your site over at %s.', 'ai-seo-pro'),
                    '<a href="https://www.pinterest.com/settings/claim" target="_blank" rel="noopener noreferrer">' . esc_html__('Pinterest', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Yandex -->
        <div class="ai-seo-pro-connection-field">
            <label for="ai_seo_pro_yandex_verification">
                <strong><?php esc_html_e('Yandex', 'ai-seo-pro'); ?></strong>
            </label>
            <input type="text" id="ai_seo_pro_yandex_verification" name="ai_seo_pro_yandex_verification"
                value="<?php echo esc_attr($ai_seo_pro_yandex_code); ?>" class="widefat"
                placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
            <p class="description">
                <?php
                printf(
                    /* translators: %s: Yandex Webmaster tools link */
                    esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                    '<a href="https://webmaster.yandex.com/sites/" target="_blank" rel="noopener noreferrer">' . esc_html__('Yandex Webmaster tools', 'ai-seo-pro') . '</a>'
                );
                ?>
            </p>
        </div>

        <!-- Submit -->
        <?php submit_button(__('Save Settings', 'ai-seo-pro')); ?>
    </form>
</div>

<style>
    .ai-seo-pro-site-connections-tab {
        max-width: 700px;
    }

    .ai-seo-pro-site-connections-tab h2 {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .ai-seo-pro-connection-field {
        margin-bottom: 25px;
    }

    .ai-seo-pro-connection-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        color: #1d2327;
    }

    .ai-seo-pro-connection-field input[type="text"] {
        width: 100%;
        max-width: 500px;
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        transition: border-color 0.2s ease;
    }

    .ai-seo-pro-connection-field input[type="text"]:focus {
        border-color: #2271b1;
        outline: none;
        box-shadow: 0 0 0 1px #2271b1;
    }

    .ai-seo-pro-connection-field input[type="text"]::placeholder {
        color: #a0a5aa;
    }

    .ai-seo-pro-connection-field .description {
        margin-top: 8px;
        font-size: 13px;
        color: #646970;
    }

    .ai-seo-pro-connection-field .description a {
        color: #2271b1;
        text-decoration: none;
    }

    .ai-seo-pro-connection-field .description a:hover {
        text-decoration: underline;
    }
</style>