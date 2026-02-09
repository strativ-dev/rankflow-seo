<?php
/**
 * 404 Monitor Dashboard View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get filter parameters.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for display filters.
$rankflow_seo_days = isset( $_GET['days'] ) ? intval( $_GET['days'] ) : 30;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$rankflow_seo_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$rankflow_seo_search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

// Get statistics.
$rankflow_seo_stats = $this->monitor_404->get_statistics( $rankflow_seo_days );

// Get logs.
$rankflow_seo_results = $this->monitor_404->get_404_logs(
	array(
		'page'     => $rankflow_seo_page,
		'per_page' => 20,
		'search'   => $rankflow_seo_search,
		'days'     => $rankflow_seo_days,
	)
);
?>

<div class="wrap rankflow-seo-404-monitor">
	<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<!-- Statistics Cards -->
	<div class="rankflow-seo-stats-grid">
		<div class="rankflow-seo-stat-card rankflow-seo-stat-card-error">
			<h3><?php esc_html_e( 'Total 404 Errors', 'rankflow-seo' ); ?></h3>
			<div class="rankflow-seo-stat-value rankflow-seo-stat-value-error">
				<?php echo esc_html( number_format( $rankflow_seo_stats['total_404s'] ) ); ?>
			</div>
			<p class="rankflow-seo-stat-subtitle">
				<?php
				printf(
					/* translators: %d: number of days */
					esc_html__( 'Last %d days', 'rankflow-seo' ),
					absint( $rankflow_seo_days )
				);
				?>
			</p>
		</div>

		<div class="rankflow-seo-stat-card rankflow-seo-stat-card-warning">
			<h3><?php esc_html_e( 'Unique URLs', 'rankflow-seo' ); ?></h3>
			<div class="rankflow-seo-stat-value rankflow-seo-stat-value-warning">
				<?php echo esc_html( number_format( $rankflow_seo_stats['unique_urls'] ) ); ?>
			</div>
			<p class="rankflow-seo-stat-subtitle">
				<?php esc_html_e( 'Different broken URLs', 'rankflow-seo' ); ?>
			</p>
		</div>
	</div>

	<!-- Top 404s -->
	<?php if ( ! empty( $rankflow_seo_stats['top_404s'] ) ) : ?>
		<div class="rankflow-seo-card-white">
			<h2><?php esc_html_e( 'Top 10 Most Hit 404 URLs', 'rankflow-seo' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'URL', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Hits', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Action', 'rankflow-seo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rankflow_seo_stats['top_404s'] as $rankflow_seo_top ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $rankflow_seo_top->url ); ?></strong></td>
							<td>
								<span class="rankflow-seo-badge-count">
									<?php echo esc_html( number_format( $rankflow_seo_top->total_hits ) ); ?>
								</span>
							</td>
							<td>
								<button class="button button-small create-redirect-btn" data-source="<?php echo esc_attr( $rankflow_seo_top->url ); ?>">
									<?php esc_html_e( 'Create Redirect', 'rankflow-seo' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<!-- Filters -->
	<div class="rankflow-seo-filters">
		<form method="get" class="rankflow-seo-filters-form">
			<input type="hidden" name="page" value="<?php echo esc_attr( $this->plugin_name ); ?>-404-monitor">

			<label>
				<?php esc_html_e( 'Time Period:', 'rankflow-seo' ); ?>
				<select name="days" onchange="this.form.submit()">
					<option value="7" <?php selected( $rankflow_seo_days, 7 ); ?>><?php esc_html_e( 'Last 7 days', 'rankflow-seo' ); ?></option>
					<option value="30" <?php selected( $rankflow_seo_days, 30 ); ?>><?php esc_html_e( 'Last 30 days', 'rankflow-seo' ); ?></option>
					<option value="90" <?php selected( $rankflow_seo_days, 90 ); ?>><?php esc_html_e( 'Last 90 days', 'rankflow-seo' ); ?></option>
					<option value="0" <?php selected( $rankflow_seo_days, 0 ); ?>><?php esc_html_e( 'All time', 'rankflow-seo' ); ?></option>
				</select>
			</label>

			<div class="rankflow-seo-filters-flex">
				<input type="search" name="s" value="<?php echo esc_attr( $rankflow_seo_search ); ?>" placeholder="<?php esc_attr_e( 'Search 404 URLs...', 'rankflow-seo' ); ?>" class="rankflow-seo-search-input">
				<button type="submit" class="button"><?php esc_html_e( 'Search', 'rankflow-seo' ); ?></button>
			</div>

			<div class="rankflow-seo-filters-right">
				<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=export_404&days=' . $rankflow_seo_days, 'export_404_logs' ) ); ?>" class="button">
					<?php esc_html_e( 'Export CSV', 'rankflow-seo' ); ?>
				</a>
				<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=clear_all', 'clear_all_404' ) ); ?>" class="button" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete all 404 logs? This cannot be undone.', 'rankflow-seo' ) ); ?>');">
					<?php esc_html_e( 'Clear All', 'rankflow-seo' ); ?>
				</a>
			</div>
		</form>
	</div>

	<!-- 404 Logs Table -->
	<?php if ( empty( $rankflow_seo_results['logs'] ) ) : ?>
		<div class="notice notice-info">
			<p><?php esc_html_e( 'No 404 errors found. Great job!', 'rankflow-seo' ); ?></p>
		</div>
	<?php else : ?>
		<div class="rankflow-seo-card-white">
			<h2><?php esc_html_e( '404 Error Logs', 'rankflow-seo' ); ?></h2>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'URL', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Hits', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Referrer', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Last Hit', 'rankflow-seo' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'rankflow-seo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rankflow_seo_results['logs'] as $rankflow_seo_log ) : ?>
						<tr>
							<td>
								<strong><?php echo esc_html( $rankflow_seo_log->url ); ?></strong>
								<?php if ( $rankflow_seo_log->hits > 10 ) : ?>
									<span class="rankflow-seo-badge rankflow-seo-badge-error">
										<?php esc_html_e( 'HIGH PRIORITY', 'rankflow-seo' ); ?>
									</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="rankflow-seo-hits-count">
									<?php echo esc_html( number_format( $rankflow_seo_log->hits ) ); ?>
								</span>
							</td>
							<td>
								<?php if ( $rankflow_seo_log->referrer ) : ?>
									<a href="<?php echo esc_url( $rankflow_seo_log->referrer ); ?>" target="_blank" class="rankflow-seo-referrer-link">
										<?php echo esc_html( wp_trim_words( $rankflow_seo_log->referrer, 6, '...' ) ); ?>
									</a>
								<?php else : ?>
									<span class="rankflow-seo-status-inactive">—</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="rankflow-seo-time-ago">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %s: human-readable time difference */
											__( '%s ago', 'rankflow-seo' ),
											human_time_diff( strtotime( $rankflow_seo_log->updated_at ), current_time( 'timestamp' ) )
										)
									);
									?>
								</span>
							</td>
							<td>
								<button class="button button-small create-redirect-btn" data-source="<?php echo esc_attr( $rankflow_seo_log->url ); ?>" data-log-id="<?php echo esc_attr( $rankflow_seo_log->id ); ?>">
									<?php esc_html_e( 'Create Redirect', 'rankflow-seo' ); ?>
								</button>
								<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=delete&id=' . $rankflow_seo_log->id, 'delete_404_' . $rankflow_seo_log->id ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Delete', 'rankflow-seo' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- Pagination -->
			<?php if ( $rankflow_seo_results['pages'] > 1 ) : ?>
				<div class="tablenav bottom">
					<div class="tablenav-pages">
						<?php
						$rankflow_seo_pagination = paginate_links(
							array(
								'base'      => add_query_arg( 'paged', '%#%' ),
								'format'    => '',
								'prev_text' => '&laquo;',
								'next_text' => '&raquo;',
								'total'     => $rankflow_seo_results['pages'],
								'current'   => $rankflow_seo_page,
							)
						);

						if ( $rankflow_seo_pagination ) {
							echo wp_kses_post( $rankflow_seo_pagination );
						}
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<!-- Create Redirect Modal -->
<div id="create-redirect-modal" class="rankflow-seo-hidden">
	<div class="rankflow-seo-modal-overlay"></div>
	<div class="rankflow-seo-modal-content">
		<h2><?php esc_html_e( 'Create Redirect', 'rankflow-seo' ); ?></h2>

		<form method="post" id="create-redirect-form">
			<?php wp_nonce_field( 'create_redirect_404', 'rankflow_seo_404_nonce' ); ?>
			<input type="hidden" name="action" value="create_redirect_from_404">
			<input type="hidden" name="log_id" id="redirect-log-id">

			<table class="form-table">
				<tr>
					<th>
						<label for="redirect-source"><?php esc_html_e( 'From (404 URL):', 'rankflow-seo' ); ?></label>
					</th>
					<td>
						<input type="text" id="redirect-source" name="source_url" class="large-text" readonly>
					</td>
				</tr>
				<tr>
					<th>
						<label for="redirect-target"><?php esc_html_e( 'To (Target URL):', 'rankflow-seo' ); ?></label>
					</th>
					<td>
						<input type="text" id="redirect-target" name="target_url" class="large-text" required placeholder="/new-page">
						<p class="description"><?php esc_html_e( 'Enter the URL to redirect to', 'rankflow-seo' ); ?></p>
					</td>
				</tr>
			</table>

			<p>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Create Redirect', 'rankflow-seo' ); ?></button>
				<button type="button" class="button" id="cancel-redirect"><?php esc_html_e( 'Cancel', 'rankflow-seo' ); ?></button>
			</p>
		</form>
	</div>
</div>
