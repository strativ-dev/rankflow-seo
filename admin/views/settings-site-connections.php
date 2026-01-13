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
$ai_seo_pro_gtm_id = get_option('ai_seo_pro_gtm_id', '');
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
        <?php esc_html_e('Connect your site with analytics tools and verify ownership with search engines. Add your tracking IDs and verification codes below.', 'ai-seo-pro'); ?>
    </p>

    <hr style="margin: 20px 0;">

    <form method="post" action="options.php">
        <?php settings_fields('ai_seo_pro_site_connections'); ?>

        <!-- Google Tag Manager Section -->
        <div class="ai-seo-pro-section">
            <h3><?php esc_html_e('Analytics', 'ai-seo-pro'); ?></h3>

            <!-- Google Tag Manager -->
            <div class="ai-seo-pro-connection-field gtm-field">
                <label for="ai_seo_pro_gtm_id">
                    <strong><?php esc_html_e('Google Tag Manager', 'ai-seo-pro'); ?></strong>
                </label>
                <input type="text" id="ai_seo_pro_gtm_id" name="ai_seo_pro_gtm_id"
                    value="<?php echo esc_attr($ai_seo_pro_gtm_id); ?>" class="widefat gtm-input-full"
                    placeholder="<?php esc_attr_e('GTM-XXXXXXX', 'ai-seo-pro'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Google Tag Manager link */
                        esc_html__('Enter your Container ID (e.g., GTM-K4JQ77J4). Find it in %s.', 'ai-seo-pro'),
                        '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Tag Manager', 'ai-seo-pro') . '</a>'
                    );
                    ?>
                </p>
                <?php if (!empty($ai_seo_pro_gtm_id)): ?>
                    <div class="gtm-status">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php
                        printf(
                            /* translators: %s: GTM Container ID */
                            esc_html__('%s is active on your site', 'ai-seo-pro'),
                            esc_html($ai_seo_pro_gtm_id)
                        );
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr style="margin: 30px 0;">

        <!-- Webmaster Verification Section -->
        <div class="ai-seo-pro-section">
            <h3><?php esc_html_e('Webmaster Verification', 'ai-seo-pro'); ?></h3>
            <p class="description" style="margin-bottom: 20px;">
                <?php esc_html_e('Verify your site with search engines. You can paste the full meta tag or just the verification code.', 'ai-seo-pro'); ?>
            </p>

            <!-- Google -->
            <div class="ai-seo-pro-connection-field">
                <label for="ai_seo_pro_google_verification">
                    <strong><?php esc_html_e('Google Search Console', 'ai-seo-pro'); ?></strong>
                </label>
                <input type="text" id="ai_seo_pro_google_verification" name="ai_seo_pro_google_verification"
                    value="<?php echo esc_attr($ai_seo_pro_google_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Google Search console link */
                        esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                        '<a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Search Console', 'ai-seo-pro') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Bing -->
            <div class="ai-seo-pro-connection-field">
                <label for="ai_seo_pro_bing_verification">
                    <strong><?php esc_html_e('Bing Webmaster Tools', 'ai-seo-pro'); ?></strong>
                </label>
                <input type="text" id="ai_seo_pro_bing_verification" name="ai_seo_pro_bing_verification"
                    value="<?php echo esc_attr($ai_seo_pro_bing_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Bing Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                        '<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener noreferrer">' . esc_html__('Bing Webmaster Tools', 'ai-seo-pro') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Yandex -->
            <div class="ai-seo-pro-connection-field">
                <label for="ai_seo_pro_yandex_verification">
                    <strong><?php esc_html_e('Yandex Webmaster', 'ai-seo-pro'); ?></strong>
                </label>
                <input type="text" id="ai_seo_pro_yandex_verification" name="ai_seo_pro_yandex_verification"
                    value="<?php echo esc_attr($ai_seo_pro_yandex_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Yandex Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                        '<a href="https://webmaster.yandex.com/sites/" target="_blank" rel="noopener noreferrer">' . esc_html__('Yandex Webmaster', 'ai-seo-pro') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Baidu -->
            <div class="ai-seo-pro-connection-field">
                <label for="ai_seo_pro_baidu_verification">
                    <strong><?php esc_html_e('Baidu Webmaster', 'ai-seo-pro'); ?></strong>
                </label>
                <input type="text" id="ai_seo_pro_baidu_verification" name="ai_seo_pro_baidu_verification"
                    value="<?php echo esc_attr($ai_seo_pro_baidu_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'ai-seo-pro'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Baidu Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'ai-seo-pro'),
                        '<a href="https://ziyuan.baidu.com/site/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Baidu Webmaster', 'ai-seo-pro') . '</a>'
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
                        '<a href="https://ahrefs.com/webmaster-tools" target="_blank" rel="noopener noreferrer">' . esc_html__('Ahrefs Webmaster Tools', 'ai-seo-pro') . '</a>'
                    );
                    ?>
                </p>
            </div>
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

    .ai-seo-pro-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1d2327;
        margin: 0 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
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

    /* GTM Field Styling */
    .gtm-field {
        background: #f8f9fa;
        border: 1px solid #e2e4e7;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .gtm-input-full {
        max-width: 300px !important;
        text-transform: uppercase;
    }

    .gtm-status {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 8px 12px;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 4px;
        color: #155724;
        font-size: 13px;
        max-width: fit-content;
    }

    .gtm-status .dashicons {
        color: #28a745;
        font-size: 16px;
        width: 16px;
        height: 16px;
    }
</style>