<?php
/**
 * Help sidebar partial.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/partials
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get current settings for system status.
$mpseo_api_key = get_option('mpseo_api_key');
$mpseo_api_provider = get_option('mpseo_api_provider', 'anthropic');
$mpseo_sitemap_enabled = get_option('mpseo_sitemap_enabled', false);
$mpseo_schema_enabled = get_option('mpseo_schema_enabled', true);
$mpseo_robots_enabled = get_option('mpseo_robots_enabled', false);
$mpseo_schemas = get_option('mpseo_schemas', array());
?>

<div class="mpseo-help-sidebar">
	<!-- Quick Links -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Links', 'metapilot-smart-seo'); ?></h3>
		<ul class="quick-links">
			<li>
				<span class="dashicons dashicons-admin-generic"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=metapilot-smart-seo-settings')); ?>">
					<?php esc_html_e('Settings', 'metapilot-smart-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-randomize"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=metapilot-smart-seo-redirects')); ?>">
					<?php esc_html_e('Redirects', 'metapilot-smart-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-media-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=metapilot-smart-seo-robots-txt')); ?>">
					<?php esc_html_e('Robots.txt', 'metapilot-smart-seo'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-editor-code"></span>
				<a href="<?php echo esc_url(admin_url('admin.php?page=mpseo-schema')); ?>">
					<?php esc_html_e('Schema', 'metapilot-smart-seo'); ?>
				</a>
			</li>
		</ul>
	</div>

	<!-- Quick Start -->
	<div class="help-section">
		<h3><?php esc_html_e('Quick Start', 'metapilot-smart-seo'); ?></h3>
		<ol class="quick-start-list">
			<li><?php esc_html_e('Configure API key in Settings', 'metapilot-smart-seo'); ?></li>
			<li><?php esc_html_e('Enable XML Sitemap', 'metapilot-smart-seo'); ?></li>
			<li><?php esc_html_e('Add Schema markup', 'metapilot-smart-seo'); ?></li>
			<li><?php esc_html_e('Configure Robots.txt', 'metapilot-smart-seo'); ?></li>
			<li><?php esc_html_e('Verify webmaster tools', 'metapilot-smart-seo'); ?></li>
			<li><?php esc_html_e('Optimize your content!', 'metapilot-smart-seo'); ?></li>
		</ol>
	</div>

	<!-- Feature Status -->
	<div class="help-section">
		<h3><?php esc_html_e('Feature Status', 'metapilot-smart-seo'); ?></h3>
		<table class="feature-status">
			<tr>
				<td><?php esc_html_e('AI Generation', 'metapilot-smart-seo'); ?></td>
				<td>
					<?php if (!empty($mpseo_api_key)): ?>
						<span class="status-on">✓ <?php echo esc_html(ucfirst($mpseo_api_provider)); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Not configured', 'metapilot-smart-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('XML Sitemap', 'metapilot-smart-seo'); ?></td>
				<td>
					<?php if ($mpseo_sitemap_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'metapilot-smart-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'metapilot-smart-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Schema Markup', 'metapilot-smart-seo'); ?></td>
				<td>
					<?php if ($mpseo_schema_enabled && !empty($mpseo_schemas)): ?>
						<span class="status-on">✓ <?php echo esc_html(count($mpseo_schemas)); ?>
							<?php esc_html_e('active', 'metapilot-smart-seo'); ?></span>
					<?php elseif ($mpseo_schema_enabled): ?>
						<span class="status-warning">○ <?php esc_html_e('No schemas', 'metapilot-smart-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'metapilot-smart-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Robots.txt', 'metapilot-smart-seo'); ?></td>
				<td>
					<?php if ($mpseo_robots_enabled): ?>
						<span class="status-on">✓ <?php esc_html_e('Enabled', 'metapilot-smart-seo'); ?></span>
					<?php else: ?>
						<span class="status-off">✗ <?php esc_html_e('Disabled', 'metapilot-smart-seo'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		</table>
	</div>

	<!-- System Status -->
	<div class="help-section">
		<h3><?php esc_html_e('System Info', 'metapilot-smart-seo'); ?></h3>
		<table class="system-status">
			<tr>
				<td><?php esc_html_e('WordPress', 'metapilot-smart-seo'); ?></td>
				<td><?php echo esc_html(get_bloginfo('version')); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP', 'metapilot-smart-seo'); ?></td>
				<td><?php echo esc_html(PHP_VERSION); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e('Plugin', 'metapilot-smart-seo'); ?></td>
				<td><?php echo esc_html(MPSEO_VERSION); ?></td>
			</tr>
		</table>
	</div>

	<!-- Useful URLs -->
	<div class="help-section">
		<h3><?php esc_html_e('Your URLs', 'metapilot-smart-seo'); ?></h3>
		<ul class="url-list">
			<?php if ($mpseo_sitemap_enabled): ?>
				<li>
					<span class="url-label"><?php esc_html_e('Sitemap:', 'metapilot-smart-seo'); ?></span>
					<a href="<?php echo esc_url(home_url('/sitemap_index.xml')); ?>" target="_blank" rel="noopener">
						/sitemap_index.xml
					</a>
				</li>
			<?php endif; ?>
			<li>
				<span class="url-label"><?php esc_html_e('Robots.txt:', 'metapilot-smart-seo'); ?></span>
				<a href="<?php echo esc_url(home_url('/robots.txt')); ?>" target="_blank" rel="noopener">
					/robots.txt
				</a>
			</li>
		</ul>
	</div>

	<!-- External Resources -->
	<div class="help-section">
		<h3><?php esc_html_e('Testing Tools', 'metapilot-smart-seo'); ?></h3>
		<ul class="external-links">
			<li>
				<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">
					<?php esc_html_e('Rich Results Test', 'metapilot-smart-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://search.google.com/search-console" target="_blank" rel="noopener">
					<?php esc_html_e('Search Console', 'metapilot-smart-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
			<li>
				<a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">
					<?php esc_html_e('Facebook Debugger', 'metapilot-smart-seo'); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</li>
		</ul>
	</div>

	<!-- Need Help -->
	<div class="help-section help-cta">
		<h3><?php esc_html_e('Need Help?', 'metapilot-smart-seo'); ?></h3>
		<p><?php esc_html_e('Check the documentation or contact support for assistance.', 'metapilot-smart-seo'); ?></p>
		<a href="<?php echo esc_url(admin_url('admin.php?page=metapilot-smart-seo-settings')); ?>"
			class="button button-primary">
			<?php esc_html_e('View Full Documentation', 'metapilot-smart-seo'); ?>
		</a>
	</div>
</div>