<?php
/**
 * Admin page header partial.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php settings_errors(); ?>

<div class="wp-header-end"></div>

<div class="mpseo-header">
	<div class="mpseo-header-logo">
		<img src="<?php echo esc_url( MPSEO_PLUGIN_URL . 'assets/images/logo-icon-orange.png' ); ?>" alt="Metapilot Smart SEO" class="mpseo-icon-40">
		<span><?php echo esc_html( get_admin_page_title() ); ?></span>
	</div>
</div>
