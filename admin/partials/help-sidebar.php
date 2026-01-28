<?php
/**
 * Help sidebar partial.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/partials
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get current settings for system status.
$rankflow_seo_api_key = get_option('rankflow_seo_api_key');
$rankflow_seo_api_provider = get_option('rankflow_seo_api_provider', 'anthropic');
$rankflow_seo_sitemap_enabled = get_option('rankflow_seo_sitemap_enabled', false);
$rankflow_seo_schema_enabled = get_option('rankflow_seo_schema_enabled', true);
$rankflow_seo_robots_enabled = get_option('rankflow_seo_robots_enabled', false);
$rankflow_seo_schemas = get_option('rankflow_seo_schemas', array());
?>

<div class="rankflow-seo-help-sidebar">
	<!-- Quick Links -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Links', 'rankflow-seo'); ?></h3>
		<ul class="quick-links">
			<li>
				<span class="dashicons dashicons-admin-generic"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=rankflow-seo-settings')); ?>">
					<?php esc_html_e('Settings', 'rankflow-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-randomize"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=rankflow-seo-redirects')); ?>">
					<?php esc_html_e('Redirects', 'rankflow-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-media-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=rankflow-seo-robots-txt')); ?>">
					<?php esc_html_e('Robots.txt', 'rankflow-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-editor-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=rankflow-seo-schema')); ?>">
					<?php esc_html_e('Schema', 'rankflow-seo'); ?>
				</a>
			</li>
		</ul>
	</div>

	<!-- Quick Start -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Start', 'rankflow-seo'); ?></h3>
		<ol class="quick-start-list">
			<li><?php esc_html_e('Configure API key in Settings', 'rankflow-seo'); ?></li>
			<li><?php esc_html_e('Enable XML Sitemap', 'rankflow-seo'); ?></li>
			<li><?php esc_html_e('Add Schema markup', 'rankflow-seo'); ?></li>
			<li><?php esc_html_e('Configure Robots.txt', 'rankflow-seo'); ?></li>
			<li><?php esc_html_e('Verify webmaster tools', 'rankflow-seo'); ?></li>
			<li><?php esc_html_e('Optimize your content!', 'rankflow-seo'); ?></li>
		</ol>
	</div>

	<!-- Feature Status -->
	<div class="help-section">
		<h3><?php esc_html_e('Feature Status', 'rankflow-seo'); ?></h3>
		<table class="feature-status">
			<tr>
				<td><?php esc_html_e('AI Generation', 'rankflow-seo'); ?></td>
				<td>
					<?php if (!empty($rankflow_seo_api_key)): ?>
						<span class="status-on">✓ <?php echo esc_html(ucfirst($rankflow_seo_api_provider)); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Not configured', 'rankflow-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('XML Sitemap', 'rankflow-seo'); ?></td>
				<td>
					<?php if ($rankflow_seo_sitemap_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'rankflow-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'rankflow-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Schema Markup', 'rankflow-seo'); ?></td>
				<td>
					<?php if ($rankflow_seo_schema_enabled && !empty($rankflow_seo_schemas)): ?>
						<span class="status-on">✓ <?php echo esc_html(count($rankflow_seo_schemas)); ?>
							<?php esc_html_e('active', 'rankflow-seo'); ?></span>
					<?php elseif ($rankflow_seo_schema_enabled): ?>
						<span class="status-warning">○ <?php esc_html_e('No schemas', 'rankflow-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'rankflow-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Robots.txt', 'rankflow-seo'); ?></td>
				<td>
					<?php if ($rankflow_seo_robots_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'rankflow-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'rankflow-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		</table>
	</div>

	<!-- System Status -->
	<div class="help-section">
		<h3><?php esc_html_e('System Info', 'rankflow-seo'); ?></h3>
		<table class="system-status">
			<tr>
				<td><?php esc_html_e('WordPress', 'rankflow-seo'); ?></td>
				<td><?php echo esc_html(get_bloginfo('version')); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP', 'rankflow-seo'); ?></td>
				<td><?php echo esc_html(PHP_VERSION); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('Plugin', 'rankflow-seo'); ?></td>
				<td><?php echo esc_html(RANKFLOW_SEO_VERSION); ?></td>
			</tr>
		</table>
	</div>

	<!-- Useful URLs -->
	<div class="help-section">
		<h3><?php esc_html_e('Your URLs', 'rankflow-seo'); ?></h3>
		<ul class="url-list">
			<?php if ($rankflow_seo_sitemap_enabled): ?>
				<li>
					<span class="url-label"><?php esc_html_e('Sitemap:', 'rankflow-seo'); ?></span>
					<a href="<?php echo esc_url(home_url('/sitemap_index.xml')); ?>" target="_blank" rel="noopener">
						/sitemap_index.xml
					</a>
				</li>
			<?php endif; ?>
			<li>
				<span class="url-label"><?php esc_html_e('Robots.txt:', 'rankflow-seo'); ?></span>
				<a href="<?php echo esc_url(home_url('/robots.txt')); ?>" target="_blank" rel="noopener">
					/robots.txt
				</a>
			</li>
		</ul>
	</div>

	<!-- External Resources -->
	<div class="help-section">
		<h3><?php esc_html_e('Testing Tools', 'rankflow-seo'); ?></h3>
		<ul class="external-links">
			<li>
				<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">
					<?php esc_html_e('Rich Results Test', 'rankflow-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://search.google.com/search-console" target="_blank" rel="noopener">
					<?php esc_html_e('Search Console', 'rankflow-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">
					<?php esc_html_e('Facebook Debugger', 'rankflow-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
		</ul>
	</div>

	<!-- Need Help -->
	<div class="help-section help-cta">
		<h3><?php esc_html_e('Need Help?', 'rankflow-seo'); ?></h3>
		<p><?php esc_html_e('Check the documentation or contact support for assistance.', 'rankflow-seo'); ?></p>
		<a href="<?php echo esc_url(admin_url('admin.php?page=rankflow-seo-help')); ?>" class="button button-primary">
			<?php esc_html_e('View Full Documentation', 'rankflow-seo'); ?>
		</a>
	</div>
</div>

