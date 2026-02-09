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
$rankflow_seo_ahrefs_code = get_option('rankflow_seo_ahrefs_verification', '');
$rankflow_seo_baidu_code = get_option('rankflow_seo_baidu_verification', '');
$rankflow_seo_bing_code = get_option('rankflow_seo_bing_verification', '');
$rankflow_seo_google_code = get_option('rankflow_seo_google_verification', '');
$rankflow_seo_pinterest_code = get_option('rankflow_seo_pinterest_verification', '');
$rankflow_seo_yandex_code = get_option('rankflow_seo_yandex_verification', '');
?>

<div class="rankflow-seo-site-connections-tab">
    <h2><?php esc_html_e('Site Connections', 'rankflow-seo'); ?></h2>

    <p class="description rankflow-seo-description">
        <?php esc_html_e('Connect your site with analytics tools and verify ownership with search engines. Add your tracking IDs and verification codes below.', 'rankflow-seo'); ?>
    </p>

    <hr class="rankflow-seo-divider-20">

    <form method="post" action="options.php">
        <?php settings_fields('rankflow_seo_site_connections'); ?>

        <!-- Webmaster Verification Section -->
        <div class="rankflow-seo-section">
            <h3><?php esc_html_e('Webmaster Verification', 'rankflow-seo'); ?></h3>
            <p class="description rankflow-seo-mb-20">
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

