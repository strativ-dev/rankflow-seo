<?php
/**
 * 404 Monitor Dashboard View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get filter parameters.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification not required for display filters.
$mpseo_days = isset( $_GET['days'] ) ? intval( $_GET['days'] ) : 30;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$mpseo_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$mpseo_search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

// Get statistics.
$mpseo_stats = $this->monitor_404->get_statistics( $mpseo_days );

// Get logs.
$mpseo_results = $this->monitor_404->get_404_logs(
	array(
		'page'     => $mpseo_page,
		'per_page' => 20,
		'search'   => $mpseo_search,
		'days'     => $mpseo_days,
	)
);
?>

<div class="wrap mpseo-404-monitor">
	<?php require_once MPSEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<!-- Statistics Cards -->
	<div class="mpseo-stats-grid">
		<div class="mpseo-stat-card mpseo-stat-card-error">
			<h3><?php esc_html_e( 'Total 404 Errors', 'metapilot-smart-seo' ); ?></h3>
			<div class="mpseo-stat-value mpseo-stat-value-error">
				<?php echo esc_html( number_format( $mpseo_stats['total_404s'] ) ); ?>
			</div>
			<p class="mpseo-stat-subtitle">
				<?php
				printf(
					/* translators: %d: number of days */
					esc_html__( 'Last %d days', 'metapilot-smart-seo' ),
					absint( $mpseo_days )
				);
				?>
			</p>
		</div>

		<div class="mpseo-stat-card mpseo-stat-card-warning">
			<h3><?php esc_html_e( 'Unique URLs', 'metapilot-smart-seo' ); ?></h3>
			<div class="mpseo-stat-value mpseo-stat-value-warning">
				<?php echo esc_html( number_format( $mpseo_stats['unique_urls'] ) ); ?>
			</div>
			<p class="mpseo-stat-subtitle">
				<?php esc_html_e( 'Different broken URLs', 'metapilot-smart-seo' ); ?>
			</p>
		</div>
	</div>

	<!-- Top 404s -->
	<?php if ( ! empty( $mpseo_stats['top_404s'] ) ) : ?>
		<div class="mpseo-card-white">
			<h2><?php esc_html_e( 'Top 10 Most Hit 404 URLs', 'metapilot-smart-seo' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'URL', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Hits', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Action', 'metapilot-smart-seo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $mpseo_stats['top_404s'] as $mpseo_top ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $mpseo_top->url ); ?></strong></td>
							<td>
								<span class="mpseo-badge-count">
									<?php echo esc_html( number_format( $mpseo_top->total_hits ) ); ?>
								</span>
							</td>
							<td>
								<button class="button button-small create-redirect-btn" data-source="<?php echo esc_attr( $mpseo_top->url ); ?>">
									<?php esc_html_e( 'Create Redirect', 'metapilot-smart-seo' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<!-- Filters -->
	<div class="mpseo-filters">
		<form method="get" class="mpseo-filters-form">
			<input type="hidden" name="page" value="<?php echo esc_attr( $this->plugin_name ); ?>-404-monitor">

			<label>
				<?php esc_html_e( 'Time Period:', 'metapilot-smart-seo' ); ?>
				<select name="days" class="mpseo-days-filter">
					<option value="7" <?php selected( $mpseo_days, 7 ); ?>><?php esc_html_e( 'Last 7 days', 'metapilot-smart-seo' ); ?></option>
					<option value="30" <?php selected( $mpseo_days, 30 ); ?>><?php esc_html_e( 'Last 30 days', 'metapilot-smart-seo' ); ?></option>
					<option value="90" <?php selected( $mpseo_days, 90 ); ?>><?php esc_html_e( 'Last 90 days', 'metapilot-smart-seo' ); ?></option>
					<option value="0" <?php selected( $mpseo_days, 0 ); ?>><?php esc_html_e( 'All time', 'metapilot-smart-seo' ); ?></option>
				</select>
			</label>

			<div class="mpseo-filters-flex">
				<input type="search" name="s" value="<?php echo esc_attr( $mpseo_search ); ?>" placeholder="<?php esc_attr_e( 'Search 404 URLs...', 'metapilot-smart-seo' ); ?>" class="mpseo-search-input">
				<button type="submit" class="button"><?php esc_html_e( 'Search', 'metapilot-smart-seo' ); ?></button>
			</div>

			<div class="mpseo-filters-right">
				<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=export_404&days=' . $mpseo_days, 'export_404_logs' ) ); ?>" class="button">
					<?php esc_html_e( 'Export CSV', 'metapilot-smart-seo' ); ?>
				</a>
				<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=clear_all', 'clear_all_404' ) ); ?>" class="button mpseo-confirm" data-confirm="<?php echo esc_attr( __( 'Are you sure you want to delete all 404 logs? This cannot be undone.', 'metapilot-smart-seo' ) ); ?>">
					<?php esc_html_e( 'Clear All', 'metapilot-smart-seo' ); ?>
				</a>
			</div>
		</form>
	</div>

	<!-- 404 Logs Table -->
	<?php if ( empty( $mpseo_results['logs'] ) ) : ?>
		<div class="notice notice-info">
			<p><?php esc_html_e( 'No 404 errors found. Great job!', 'metapilot-smart-seo' ); ?></p>
		</div>
	<?php else : ?>
		<div class="mpseo-card-white">
			<h2><?php esc_html_e( '404 Error Logs', 'metapilot-smart-seo' ); ?></h2>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'URL', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Hits', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Referrer', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Last Hit', 'metapilot-smart-seo' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'metapilot-smart-seo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $mpseo_results['logs'] as $mpseo_log ) : ?>
						<tr>
							<td>
								<strong><?php echo esc_html( $mpseo_log->url ); ?></strong>
								<?php if ( $mpseo_log->hits > 10 ) : ?>
									<span class="mpseo-badge mpseo-badge-error">
										<?php esc_html_e( 'HIGH PRIORITY', 'metapilot-smart-seo' ); ?>
									</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="mpseo-hits-count">
									<?php echo esc_html( number_format( $mpseo_log->hits ) ); ?>
								</span>
							</td>
							<td>
								<?php if ( $mpseo_log->referrer ) : ?>
									<a href="<?php echo esc_url( $mpseo_log->referrer ); ?>" target="_blank" class="mpseo-referrer-link">
										<?php echo esc_html( wp_trim_words( $mpseo_log->referrer, 6, '...' ) ); ?>
									</a>
								<?php else : ?>
									<span class="mpseo-status-inactive">—</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="mpseo-time-ago">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %s: human-readable time difference */
											__( '%s ago', 'metapilot-smart-seo' ),
											human_time_diff( strtotime( $mpseo_log->updated_at ), current_time( 'timestamp' ) )
										)
									);
									?>
								</span>
							</td>
							<td>
								<button class="button button-small create-redirect-btn" data-source="<?php echo esc_attr( $mpseo_log->url ); ?>" data-log-id="<?php echo esc_attr( $mpseo_log->id ); ?>">
									<?php esc_html_e( 'Create Redirect', 'metapilot-smart-seo' ); ?>
								</button>
								<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-404-monitor&action=delete&id=' . $mpseo_log->id, 'delete_404_' . $mpseo_log->id ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Delete', 'metapilot-smart-seo' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- Pagination -->
			<?php if ( $mpseo_results['pages'] > 1 ) : ?>
				<div class="tablenav bottom">
					<div class="tablenav-pages">
						<?php
						$mpseo_pagination = paginate_links(
							array(
								'base'      => add_query_arg( 'paged', '%#%' ),
								'format'    => '',
								'prev_text' => '&laquo;',
								'next_text' => '&raquo;',
								'total'     => $mpseo_results['pages'],
								'current'   => $mpseo_page,
							)
						);

						if ( $mpseo_pagination ) {
							echo wp_kses_post( $mpseo_pagination );
						}
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<!-- Create Redirect Modal -->
<div id="create-redirect-modal" class="mpseo-hidden">
	<div class="mpseo-modal-overlay"></div>
	<div class="mpseo-modal-content">
		<h2><?php esc_html_e( 'Create Redirect', 'metapilot-smart-seo' ); ?></h2>

		<form method="post" id="create-redirect-form">
			<?php wp_nonce_field( 'create_redirect_404', 'mpseo_404_nonce' ); ?>
			<input type="hidden" name="action" value="create_redirect_from_404">
			<input type="hidden" name="log_id" id="redirect-log-id">

			<table class="form-table">
				<tr>
					<th>
						<label for="redirect-source"><?php esc_html_e( 'From (404 URL):', 'metapilot-smart-seo' ); ?></label>
					</th>
					<td>
						<input type="text" id="redirect-source" name="source_url" class="large-text" readonly>
					</td>
				</tr>
				<tr>
					<th>
						<label for="redirect-target"><?php esc_html_e( 'To (Target URL):', 'metapilot-smart-seo' ); ?></label>
					</th>
					<td>
						<input type="text" id="redirect-target" name="target_url" class="large-text" required placeholder="/new-page">
						<p class="description"><?php esc_html_e( 'Enter the URL to redirect to', 'metapilot-smart-seo' ); ?></p>
					</td>
				</tr>
			</table>

			<p>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Create Redirect', 'metapilot-smart-seo' ); ?></button>
				<button type="button" class="button" id="cancel-redirect"><?php esc_html_e( 'Cancel', 'metapilot-smart-seo' ); ?></button>
			</p>
		</form>
	</div>
</div>
