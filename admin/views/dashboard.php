<?php
/**
 * Dashboard page.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get statistics.
global $wpdb;
$ai_seo_pro_total_posts = wp_count_posts();

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dashboard statistics.
$ai_seo_pro_posts_with_meta = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_ai_seo_title'
	)
);

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dashboard statistics.
$ai_seo_pro_avg_score = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT AVG(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_ai_seo_score'
	)
);
?>

<div class="wrap ai-seo-pro-dashboard">
	<?php require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<div class="dashboard-content">
		<div class="dashboard-main">
			<div class="stats-grid">
				<div class="stat-card">
					<div class="stat-icon">📝</div>
					<div class="stat-content">
						<h3><?php echo esc_html(number_format($ai_seo_pro_total_posts->publish)); ?></h3>
						<p><?php esc_html_e('Published Posts', 'ai-seo-pro'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">✅</div>
					<div class="stat-content">
						<h3><?php echo esc_html(number_format($ai_seo_pro_posts_with_meta)); ?></h3>
						<p><?php esc_html_e('Optimized Posts', 'ai-seo-pro'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">📊</div>
					<div class="stat-content">
						<h3><?php echo esc_html(round($ai_seo_pro_avg_score)); ?>/100</h3>
						<p><?php esc_html_e('Average SEO Score', 'ai-seo-pro'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">🤖</div>
					<div class="stat-content">
						<h3><?php echo esc_html(AI_SEO_Pro_Helper::get_provider_name(get_option('ai_seo_pro_api_provider', 'gemini'))); ?>
						</h3>
						<p><?php esc_html_e('AI Provider', 'ai-seo-pro'); ?></p>
					</div>
				</div>
			</div>

			<div class="recent-activity">
				<h2><?php esc_html_e('Recent Posts', 'ai-seo-pro'); ?></h2>

				<?php
				$ai_seo_pro_recent_posts = get_posts(
					array(
						'numberposts' => 10,
						'post_status' => 'publish',
					)
				);

				if ($ai_seo_pro_recent_posts):
					?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e('Title', 'ai-seo-pro'); ?></th>
								<th><?php esc_html_e('SEO Score', 'ai-seo-pro'); ?></th>
								<th><?php esc_html_e('Meta Title', 'ai-seo-pro'); ?></th>
								<th><?php esc_html_e('Actions', 'ai-seo-pro'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($ai_seo_pro_recent_posts as $ai_seo_pro_post):
								$ai_seo_pro_score = get_post_meta($ai_seo_pro_post->ID, '_ai_seo_score', true);
								$ai_seo_pro_meta_title = get_post_meta($ai_seo_pro_post->ID, '_ai_seo_title', true);
								?>
								<tr>
									<td>
										<strong><?php echo esc_html($ai_seo_pro_post->post_title); ?></strong>
									</td>
									<td>
										<?php
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- AI_SEO_Pro_Helper::format_score() returns safely escaped HTML.
										echo AI_SEO_Pro_Helper::format_score($ai_seo_pro_score ? $ai_seo_pro_score : 0);
										?>
									</td>
									<td>
										<?php echo $ai_seo_pro_meta_title ? esc_html('✓') : esc_html('✗'); ?>
									</td>
									<td>
										<a href="<?php echo esc_url(get_edit_post_link($ai_seo_pro_post->ID)); ?>"
											class="button button-small">
											<?php esc_html_e('Edit', 'ai-seo-pro'); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<p><?php esc_html_e('No posts found.', 'ai-seo-pro'); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="dashboard-sidebar">
			<?php require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/partials/help-sidebar.php'; ?>
		</div>
	</div>
</div>

<style>
	.ai-seo-pro-dashboard .dashboard-content {
		display: flex;
		gap: 20px;
		margin-top: 20px;
	}

	.dashboard-main {
		flex: 1;
	}

	.dashboard-sidebar {
		width: 300px;
	}

	.stats-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-bottom: 30px;
	}

	.stat-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 20px;
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.stat-icon {
		font-size: 36px;
	}

	.stat-content h3 {
		margin: 0 0 5px 0;
		font-size: 20px;
		color: #23282d;
	}

	.stat-content p {
		margin: 0;
		color: #666;
		font-size: 14px;
	}

	.recent-activity {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 20px;
	}

	.recent-activity h2 {
		margin-top: 0;
		font-size: 18px;
	}

	@media (max-width: 1200px) {
		.ai-seo-pro-dashboard .dashboard-content {
			flex-direction: column;
		}

		.dashboard-sidebar {
			width: 100%;
		}
	}
</style>