<?php
/**
 * Site Connections Settings Tab View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings.
$rankflow_seo_gtm_id = get_option('rankflow_seo_gtm_id', '');
$rankflow_seo_ahrefs_code = get_option('rankflow_seo_ahrefs_verification', '');
$rankflow_seo_baidu_code = get_option('rankflow_seo_baidu_verification', '');
$rankflow_seo_bing_code = get_option('rankflow_seo_bing_verification', '');
$rankflow_seo_google_code = get_option('rankflow_seo_google_verification', '');
$rankflow_seo_pinterest_code = get_option('rankflow_seo_pinterest_verification', '');
$rankflow_seo_yandex_code = get_option('rankflow_seo_yandex_verification', '');
?>

<div class="rankflow-seo-site-connections-tab">
    <h2><?php esc_html_e('Site Connections', 'rankflow-seo'); ?></h2>

    <p class="description" style="font-size: 14px; margin-bottom: 20px;">
        <?php esc_html_e('Connect your site with analytics tools and verify ownership with search engines. Add your tracking IDs and verification codes below.', 'rankflow-seo'); ?>
    </p>

    <hr style="margin: 20px 0;">

    <form method="post" action="options.php">
        <?php settings_fields('rankflow_seo_site_connections'); ?>

        <!-- Google Tag Manager Section -->
        <div class="rankflow-seo-section">
            <h3><?php esc_html_e('Analytics', 'rankflow-seo'); ?></h3>

            <!-- Google Tag Manager -->
            <div class="rankflow-seo-connection-field gtm-field">
                <label for="rankflow_seo_gtm_id">
                    <strong><?php esc_html_e('Google Tag Manager', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_gtm_id" name="rankflow_seo_gtm_id"
                    value="<?php echo esc_attr($rankflow_seo_gtm_id); ?>" class="widefat gtm-input-full"
                    placeholder="<?php esc_attr_e('GTM-XXXXXXX', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Google Tag Manager link */
                        esc_html__('Enter your Container ID (e.g., GTM-K4JQ77J4). Find it in %s.', 'rankflow-seo'),
                        '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Tag Manager', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
                <?php if (!empty($rankflow_seo_gtm_id)): ?>
                    <div class="gtm-status">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php
                        printf(
                            /* translators: %s: GTM Container ID */
                            esc_html__('%s is active on your site', 'rankflow-seo'),
                            esc_html($rankflow_seo_gtm_id)
                        );
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr style="margin: 30px 0;">

        <!-- Webmaster Verification Section -->
        <div class="rankflow-seo-section">
            <h3><?php esc_html_e('Webmaster Verification', 'rankflow-seo'); ?></h3>
            <p class="description" style="margin-bottom: 20px;">
                <?php esc_html_e('Verify your site with search engines. You can paste the full meta tag or just the verification code.', 'rankflow-seo'); ?>
            </p>

            <!-- Google -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_google_verification">
                    <strong><?php esc_html_e('Google Search Console', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_google_verification" name="rankflow_seo_google_verification"
                    value="<?php echo esc_attr($rankflow_seo_google_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Google Search console link */
                        esc_html__('Get your verification code in %s.', 'rankflow-seo'),
                        '<a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Search Console', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Bing -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_bing_verification">
                    <strong><?php esc_html_e('Bing Webmaster Tools', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_bing_verification" name="rankflow_seo_bing_verification"
                    value="<?php echo esc_attr($rankflow_seo_bing_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Bing Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'rankflow-seo'),
                        '<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener noreferrer">' . esc_html__('Bing Webmaster Tools', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Yandex -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_yandex_verification">
                    <strong><?php esc_html_e('Yandex Webmaster', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_yandex_verification" name="rankflow_seo_yandex_verification"
                    value="<?php echo esc_attr($rankflow_seo_yandex_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Yandex Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'rankflow-seo'),
                        '<a href="https://webmaster.yandex.com/sites/" target="_blank" rel="noopener noreferrer">' . esc_html__('Yandex Webmaster', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Baidu -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_baidu_verification">
                    <strong><?php esc_html_e('Baidu Webmaster', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_baidu_verification" name="rankflow_seo_baidu_verification"
                    value="<?php echo esc_attr($rankflow_seo_baidu_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Baidu Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'rankflow-seo'),
                        '<a href="https://ziyuan.baidu.com/site/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Baidu Webmaster', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Pinterest -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_pinterest_verification">
                    <strong><?php esc_html_e('Pinterest', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_pinterest_verification" name="rankflow_seo_pinterest_verification"
                    value="<?php echo esc_attr($rankflow_seo_pinterest_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Pinterest link */
                        esc_html__('Claim your site over at %s.', 'rankflow-seo'),
                        '<a href="https://www.pinterest.com/settings/claim" target="_blank" rel="noopener noreferrer">' . esc_html__('Pinterest', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Ahrefs -->
            <div class="rankflow-seo-connection-field">
                <label for="rankflow_seo_ahrefs_verification">
                    <strong><?php esc_html_e('Ahrefs Analytics', 'rankflow-seo'); ?></strong>
                </label>
                <input type="text" id="rankflow_seo_ahrefs_verification" name="rankflow_seo_ahrefs_verification"
                    value="<?php echo esc_attr($rankflow_seo_ahrefs_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('e.g., ddiIjj8TPYy6yVSuq5lQ0w', 'rankflow-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Ahrefs link */
                        esc_html__('Enter your Ahrefs analytics data-key from %s.', 'rankflow-seo'),
                        '<a href="https://ahrefs.com/webmaster-tools" target="_blank" rel="noopener noreferrer">' . esc_html__('Ahrefs Webmaster Tools', 'rankflow-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <!-- Submit -->
        <?php submit_button(__('Save Settings', 'rankflow-seo')); ?>
    </form>
</div>

<style>
    .rankflow-seo-site-connections-tab {
        max-width: 700px;
    }

    .rankflow-seo-site-connections-tab h2 {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .rankflow-seo-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1d2327;
        margin: 0 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .rankflow-seo-connection-field {
        margin-bottom: 25px;
    }

    .rankflow-seo-connection-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        color: #1d2327;
    }

    .rankflow-seo-connection-field input[type="text"] {
        width: 100%;
        max-width: 500px;
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        transition: border-color 0.2s ease;
    }

    .rankflow-seo-connection-field input[type="text"]:focus {
        border-color: #2271b1;
        outline: none;
        box-shadow: 0 0 0 1px #2271b1;
    }

    .rankflow-seo-connection-field input[type="text"]::placeholder {
        color: #a0a5aa;
    }

    .rankflow-seo-connection-field .description {
        margin-top: 8px;
        font-size: 13px;
        color: #646970;
    }

    .rankflow-seo-connection-field .description a {
        color: #2271b1;
        text-decoration: none;
    }

    .rankflow-seo-connection-field .description a:hover {
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