<?php
/**
 * Site Connections Settings Tab View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings.
$mpseo_ahrefs_code = get_option('mpseo_ahrefs_verification', '');
$mpseo_baidu_code = get_option('mpseo_baidu_verification', '');
$mpseo_bing_code = get_option('mpseo_bing_verification', '');
$mpseo_google_code = get_option('mpseo_google_verification', '');
$mpseo_pinterest_code = get_option('mpseo_pinterest_verification', '');
$mpseo_yandex_code = get_option('mpseo_yandex_verification', '');
?>

<div class="mpseo-site-connections-tab">
    <h2><?php esc_html_e('Site Connections', 'metapilot-smart-seo'); ?></h2>

    <p class="description mpseo-description">
        <?php esc_html_e('Connect your site with analytics tools and verify ownership with search engines. Add your tracking IDs and verification codes below.', 'metapilot-smart-seo'); ?>
    </p>

    <hr class="mpseo-divider-20">

    <form method="post" action="options.php">
        <?php settings_fields('mpseo_site_connections'); ?>

        <!-- Webmaster Verification Section -->
        <div class="mpseo-section-card">
            <h3><?php esc_html_e('Webmaster Verification', 'metapilot-smart-seo'); ?></h3>
            <p class="description mpseo-mb-20">
                <?php esc_html_e('Verify your site with search engines. You can paste the full meta tag or just the verification code.', 'metapilot-smart-seo'); ?>
            </p>

            <!-- Google -->
            <div class="mpseo-connection-field">
                <label for="mpseo_google_verification">
                    <strong><?php esc_html_e('Google Search Console', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_google_verification" name="mpseo_google_verification"
                    value="<?php echo esc_attr($mpseo_google_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Google Search console link */
                        esc_html__('Get your verification code in %s.', 'metapilot-smart-seo'),
                        '<a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">' . esc_html__('Google Search Console', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Bing -->
            <div class="mpseo-connection-field">
                <label for="mpseo_bing_verification">
                    <strong><?php esc_html_e('Bing Webmaster Tools', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_bing_verification" name="mpseo_bing_verification"
                    value="<?php echo esc_attr($mpseo_bing_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Bing Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'metapilot-smart-seo'),
                        '<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener noreferrer">' . esc_html__('Bing Webmaster Tools', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Yandex -->
            <div class="mpseo-connection-field">
                <label for="mpseo_yandex_verification">
                    <strong><?php esc_html_e('Yandex Webmaster', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_yandex_verification" name="mpseo_yandex_verification"
                    value="<?php echo esc_attr($mpseo_yandex_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Yandex Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'metapilot-smart-seo'),
                        '<a href="https://webmaster.yandex.com/sites/" target="_blank" rel="noopener noreferrer">' . esc_html__('Yandex Webmaster', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Baidu -->
            <div class="mpseo-connection-field">
                <label for="mpseo_baidu_verification">
                    <strong><?php esc_html_e('Baidu Webmaster', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_baidu_verification" name="mpseo_baidu_verification"
                    value="<?php echo esc_attr($mpseo_baidu_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Baidu Webmaster tools link */
                        esc_html__('Get your verification code in %s.', 'metapilot-smart-seo'),
                        '<a href="https://ziyuan.baidu.com/site/index" target="_blank" rel="noopener noreferrer">' . esc_html__('Baidu Webmaster', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Pinterest -->
            <div class="mpseo-connection-field">
                <label for="mpseo_pinterest_verification">
                    <strong><?php esc_html_e('Pinterest', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_pinterest_verification" name="mpseo_pinterest_verification"
                    value="<?php echo esc_attr($mpseo_pinterest_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('Add verification code', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Pinterest link */
                        esc_html__('Claim your site over at %s.', 'metapilot-smart-seo'),
                        '<a href="https://www.pinterest.com/settings/claim" target="_blank" rel="noopener noreferrer">' . esc_html__('Pinterest', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>

            <!-- Ahrefs -->
            <div class="mpseo-connection-field">
                <label for="mpseo_ahrefs_verification">
                    <strong><?php esc_html_e('Ahrefs Analytics', 'metapilot-smart-seo'); ?></strong>
                </label>
                <input type="text" id="mpseo_ahrefs_verification" name="mpseo_ahrefs_verification"
                    value="<?php echo esc_attr($mpseo_ahrefs_code); ?>" class="widefat"
                    placeholder="<?php esc_attr_e('e.g., ddiIjj8TPYy6yVSuq5lQ0w', 'metapilot-smart-seo'); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: Ahrefs link */
                        esc_html__('Enter your Ahrefs analytics data-key from %s.', 'metapilot-smart-seo'),
                        '<a href="https://ahrefs.com/webmaster-tools" target="_blank" rel="noopener noreferrer">' . esc_html__('Ahrefs Webmaster Tools', 'metapilot-smart-seo') . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <!-- Submit -->
        <?php submit_button(__('Save Settings', 'metapilot-smart-seo')); ?>
    </form>
</div>