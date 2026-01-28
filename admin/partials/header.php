<?php
/**
 * Admin page header partial.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="rankflow-seo-header">
	<div class="rankflow-seo-header-logo">
		<img src="<?php echo esc_url( RANKFLOW_SEO_PLUGIN_URL . 'assets/images/logo-icon-orange.png' ); ?>" alt="RankFlow SEO" class="rankflow-seo-icon-40">
		<span><?php echo esc_html( get_admin_page_title() ); ?></span>
	</div>
</div>
