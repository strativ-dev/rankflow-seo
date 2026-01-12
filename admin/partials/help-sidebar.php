<?php
/**
 * Help sidebar partial.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/partials
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get current settings for system status.
$ai_seo_pro_api_key = get_option('ai_seo_pro_api_key');
$ai_seo_pro_api_provider = get_option('ai_seo_pro_api_provider', 'anthropic');
$ai_seo_pro_sitemap_enabled = get_option('ai_seo_pro_sitemap_enabled', false);
$ai_seo_pro_schema_enabled = get_option('ai_seo_pro_schema_enabled', true);
$ai_seo_pro_robots_enabled = get_option('ai_seo_pro_robots_enabled', false);
$ai_seo_pro_schemas = get_option('ai_seo_pro_schemas', array());
?>

<div class="ai-seo-pro-help-sidebar">
	<!-- Quick Links -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Links', 'ai-seo-pro'); ?></h3>
		<ul class="quick-links">
			<li>
				<span class="dashicons dashicons-admin-generic"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=ai-seo-pro-settings')); ?>">
					<?php esc_html_e('Settings', 'ai-seo-pro'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-randomize"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=ai-seo-pro-redirects')); ?>">
					<?php esc_html_e('Redirects', 'ai-seo-pro'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-media-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=ai-seo-pro-robots-txt')); ?>">
					<?php esc_html_e('Robots.txt', 'ai-seo-pro'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-editor-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=ai-seo-pro-schema')); ?>">
					<?php esc_html_e('Schema', 'ai-seo-pro'); ?>
				</a>
			</li>
		</ul>
	</div>

	<!-- Quick Start -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Start', 'ai-seo-pro'); ?></h3>
		<ol class="quick-start-list">
			<li><?php esc_html_e('Configure API key in Settings', 'ai-seo-pro'); ?></li>
			<li><?php esc_html_e('Enable XML Sitemap', 'ai-seo-pro'); ?></li>
			<li><?php esc_html_e('Add Schema markup', 'ai-seo-pro'); ?></li>
			<li><?php esc_html_e('Configure Robots.txt', 'ai-seo-pro'); ?></li>
			<li><?php esc_html_e('Verify webmaster tools', 'ai-seo-pro'); ?></li>
			<li><?php esc_html_e('Optimize your content!', 'ai-seo-pro'); ?></li>
		</ol>
	</div>

	<!-- Feature Status -->
	<div class="help-section">
		<h3><?php esc_html_e('Feature Status', 'ai-seo-pro'); ?></h3>
		<table class="feature-status">
			<tr>
				<td><?php esc_html_e('AI Generation', 'ai-seo-pro'); ?></td>
				<td>
					<?php if (!empty($ai_seo_pro_api_key)): ?>
						<span class="status-on">✓ <?php echo esc_html(ucfirst($ai_seo_pro_api_provider)); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Not configured', 'ai-seo-pro'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('XML Sitemap', 'ai-seo-pro'); ?></td>
				<td>
					<?php if ($ai_seo_pro_sitemap_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'ai-seo-pro'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'ai-seo-pro'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Schema Markup', 'ai-seo-pro'); ?></td>
				<td>
					<?php if ($ai_seo_pro_schema_enabled && !empty($ai_seo_pro_schemas)): ?>
						<span class="status-on">✓ <?php echo esc_html(count($ai_seo_pro_schemas)); ?>
							<?php esc_html_e('active', 'ai-seo-pro'); ?></span>
					<?php elseif ($ai_seo_pro_schema_enabled): ?>
						<span class="status-warning">○ <?php esc_html_e('No schemas', 'ai-seo-pro'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'ai-seo-pro'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Robots.txt', 'ai-seo-pro'); ?></td>
				<td>
					<?php if ($ai_seo_pro_robots_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'ai-seo-pro'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'ai-seo-pro'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		</table>
	</div>

	<!-- System Status -->
	<div class="help-section">
		<h3><?php esc_html_e('System Info', 'ai-seo-pro'); ?></h3>
		<table class="system-status">
			<tr>
				<td><?php esc_html_e('WordPress', 'ai-seo-pro'); ?></td>
				<td><?php echo esc_html(get_bloginfo('version')); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP', 'ai-seo-pro'); ?></td>
				<td><?php echo esc_html(PHP_VERSION); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('Plugin', 'ai-seo-pro'); ?></td>
				<td><?php echo esc_html(AI_SEO_PRO_VERSION); ?></td>
			</tr>
		</table>
	</div>

	<!-- Useful URLs -->
	<div class="help-section">
		<h3><?php esc_html_e('Your URLs', 'ai-seo-pro'); ?></h3>
		<ul class="url-list">
			<?php if ($ai_seo_pro_sitemap_enabled): ?>
				<li>
					<span class="url-label"><?php esc_html_e('Sitemap:', 'ai-seo-pro'); ?></span>
					<a href="<?php echo esc_url(home_url('/sitemap_index.xml')); ?>" target="_blank" rel="noopener">
						/sitemap_index.xml
					</a>
				</li>
			<?php endif; ?>
			<li>
				<span class="url-label"><?php esc_html_e('Robots.txt:', 'ai-seo-pro'); ?></span>
				<a href="<?php echo esc_url(home_url('/robots.txt')); ?>" target="_blank" rel="noopener">
					/robots.txt
				</a>
			</li>
		</ul>
	</div>

	<!-- External Resources -->
	<div class="help-section">
		<h3><?php esc_html_e('Testing Tools', 'ai-seo-pro'); ?></h3>
		<ul class="external-links">
			<li>
				<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">
					<?php esc_html_e('Rich Results Test', 'ai-seo-pro'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://search.google.com/search-console" target="_blank" rel="noopener">
					<?php esc_html_e('Search Console', 'ai-seo-pro'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">
					<?php esc_html_e('Facebook Debugger', 'ai-seo-pro'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
		</ul>
	</div>

	<!-- Need Help -->
	<div class="help-section help-cta">
		<h3><?php esc_html_e('Need Help?', 'ai-seo-pro'); ?></h3>
		<p><?php esc_html_e('Check the documentation or contact support for assistance.', 'ai-seo-pro'); ?></p>
		<a href="<?php echo esc_url(admin_url('admin.php?page=ai-seo-pro-help')); ?>" class="button button-primary">
			<?php esc_html_e('View Full Documentation', 'ai-seo-pro'); ?>
		</a>
	</div>
</div>

<style>
	.ai-seo-pro-help-sidebar {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 0;
	}

	.ai-seo-pro-help-sidebar .help-section {
		padding: 15px 20px;
		border-bottom: 1px solid #eee;
	}

	.ai-seo-pro-help-sidebar .help-section:last-child {
		border-bottom: none;
	}

	.ai-seo-pro-help-sidebar .help-section h3 {
		margin: 0 0 12px 0;
		padding: 0;
		color: #1d2327;
		font-size: 14px;
		font-weight: 600;
	}

	/* Quick Links */
	.quick-links {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.quick-links li {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 6px 0;
	}

	.quick-links .dashicons {
		color: #2271b1;
		font-size: 16px;
		width: 16px;
		height: 16px;
	}

	.quick-links a {
		text-decoration: none;
		color: #2271b1;
		font-size: 13px;
	}

	.quick-links a:hover {
		color: #135e96;
		text-decoration: underline;
	}

	/* Quick Start */
	.quick-start-list {
		margin: 0;
		padding-left: 20px;
		font-size: 13px;
	}

	.quick-start-list li {
		margin-bottom: 6px;
		color: #555;
	}

	/* Feature Status */
	.feature-status {
		width: 100%;
		font-size: 13px;
		border-collapse: collapse;
	}

	.feature-status td {
		padding: 6px 0;
		vertical-align: middle;
	}

	.feature-status td:first-child {
		font-weight: 500;
		color: #1d2327;
	}

	.feature-status td:last-child {
		text-align: right;
	}

	.status-on {
		color: #00a32a;
		font-weight: 500;
	}

	.status-off {
		color: #999;
	}

	.status-warning {
		color: #dba617;
	}

	/* System Status */
	.system-status {
		width: 100%;
		font-size: 13px;
		border-collapse: collapse;
	}

	.system-status td {
		padding: 4px 0;
	}

	.system-status td:first-child {
		font-weight: 500;
		color: #555;
	}

	.system-status td:last-child {
		text-align: right;
		color: #666;
	}

	/* URL List */
	.url-list {
		list-style: none;
		padding: 0;
		margin: 0;
		font-size: 12px;
	}

	.url-list li {
		padding: 5px 0;
		display: flex;
		flex-direction: column;
		gap: 2px;
	}

	.url-label {
		color: #666;
		font-size: 11px;
	}

	.url-list a {
		color: #2271b1;
		text-decoration: none;
		word-break: break-all;
	}

	.url-list a:hover {
		text-decoration: underline;
	}

	/* External Links */
	.external-links {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.external-links li {
		padding: 5px 0;
	}

	.external-links a {
		display: flex;
		align-items: center;
		gap: 5px;
		text-decoration: none;
		color: #2271b1;
		font-size: 13px;
	}

	.external-links a:hover {
		text-decoration: underline;
	}

	.external-links .dashicons {
		font-size: 12px;
		width: 12px;
		height: 12px;
	}

	/* Help CTA */
	.help-cta {
		background: #f8f9fa;
		text-align: center;
	}

	.help-cta p {
		margin: 0 0 12px 0;
		font-size: 13px;
		color: #555;
	}

	.help-cta .button {
		width: 100%;
	}
</style>