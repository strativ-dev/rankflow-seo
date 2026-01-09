<?php
/**
 * Admin page header partial.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/partials
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="ai-seo-pro-header">
	<div class="ai-seo-pro-header-logo">
		<img src="<?php echo esc_url(AI_SEO_PRO_PLUGIN_URL . 'assets/images/logo.png'); ?>" alt="AI SEO Pro"
			style="height: 40px;">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	</div>
</div>

<style>
	.ai-seo-pro-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px 0 20px 15px;
		border-bottom: 1px solid #ddd;
		margin-bottom: 20px;
	}

	.ai-seo-pro-header-logo {
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.ai-seo-pro-header-logo h1 {
		margin: 0;
		font-size: 24px;
	}

	.ai-seo-pro-header-actions {
		display: flex;
		gap: 10px;
	}
</style>