<?php
/**
 * Dashboard page.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

// Get statistics.
global $wpdb;
$rankflow_seo_total_posts = wp_count_posts();

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dashboard statistics.
$rankflow_seo_posts_with_meta = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_rankflow_seo_title'
	)
);

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dashboard statistics.
$rankflow_seo_avg_score = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT AVG(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_rankflow_seo_score'
	)
);
?>

<div class="wrap rankflow-seo-dashboard">
	<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<div class="dashboard-content">
		<div class="dashboard-main">
			<div class="stats-grid">
				<div class="stat-card">
					<div class="stat-icon">📝</div>
					<div class="stat-content">
						<h3><?php echo esc_html(number_format($rankflow_seo_total_posts->publish)); ?></h3>
						<p><?php esc_html_e('Published Posts', 'rankflow-seo'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">✅</div>
					<div class="stat-content">
						<h3><?php echo esc_html(number_format($rankflow_seo_posts_with_meta)); ?></h3>
						<p><?php esc_html_e('Optimized Posts', 'rankflow-seo'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">📊</div>
					<div class="stat-content">
						<h3><?php echo esc_html(round($rankflow_seo_avg_score ?? 0)); ?>/100</h3>
						<p><?php esc_html_e('Average SEO Score', 'rankflow-seo'); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">🤖</div>
					<div class="stat-content">
						<h3><?php echo esc_html(RankFlow_SEO_Helper::get_provider_name(get_option('rankflow_seo_api_provider', 'gemini'))); ?>
						</h3>
						<p><?php esc_html_e('AI Provider', 'rankflow-seo'); ?></p>
					</div>
				</div>
			</div>

			<div class="recent-activity">
				<h2><?php esc_html_e('Recent Posts', 'rankflow-seo'); ?></h2>

				<?php
				$rankflow_seo_recent_posts = get_posts(
					array(
						'numberposts' => 10,
						'post_status' => 'publish',
					)
				);

				if ($rankflow_seo_recent_posts):
					?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e('Title', 'rankflow-seo'); ?></th>
								<th><?php esc_html_e('SEO Score', 'rankflow-seo'); ?></th>
								<th><?php esc_html_e('Meta Title', 'rankflow-seo'); ?></th>
								<th><?php esc_html_e('Actions', 'rankflow-seo'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($rankflow_seo_recent_posts as $rankflow_seo_post):
								$rankflow_seo_score = get_post_meta($rankflow_seo_post->ID, '_rankflow_seo_score', true);
								$rankflow_seo_meta_title = get_post_meta($rankflow_seo_post->ID, '_rankflow_seo_title', true);
								?>
								<tr>
									<td>
										<strong><?php echo esc_html($rankflow_seo_post->post_title); ?></strong>
									</td>
									<td>
										<?php
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- RankFlow_SEO_Helper::format_score() returns safely escaped HTML.
										echo RankFlow_SEO_Helper::format_score($rankflow_seo_score ? $rankflow_seo_score : 0);
										?>
									</td>
									<td>
										<?php echo $rankflow_seo_meta_title ? esc_html('✓') : esc_html('✗'); ?>
									</td>
									<td>
										<a href="<?php echo esc_url(get_edit_post_link($rankflow_seo_post->ID)); ?>"
											class="button button-small">
											<?php esc_html_e('Edit', 'rankflow-seo'); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<p><?php esc_html_e('No posts found.', 'rankflow-seo'); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="dashboard-sidebar">
			<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/help-sidebar.php'; ?>
		</div>
	</div>
</div>

<style>
	.rankflow-seo-dashboard .dashboard-content {
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
		.rankflow-seo-dashboard .dashboard-content {
			flex-direction: column;
		}

		.dashboard-sidebar {
			width: 100%;
		}
	}
</style>