<?php
/**
 * Admin page header partial.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/partials
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="rankflow-seo-header">
	<div class="rankflow-seo-header-logo">
		<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/logo-icon-orange.png'); ?>"
			alt="RankFlow SEO" style="height: 40px;">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	</div>
</div>

<style>
	.rankflow-seo-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px 0 20px 15px;
		border-bottom: 1px solid #ddd;
		margin-bottom: 20px;
	}

	.rankflow-seo-header-logo {
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.rankflow-seo-header-logo h1 {
		margin: 0;
		font-size: 24px;
	}

	.rankflow-seo-header-actions {
		display: flex;
		gap: 10px;
	}
</style>