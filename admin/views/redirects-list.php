<?php
/**
 * Redirects List View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 *
 * @var array  $rankflow_seo_results Redirects results.
 * @var string $rankflow_seo_search  Search query.
 * @var int    $rankflow_seo_page    Current page number.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="rankflow-seo-actions-row">
	<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects&action=import" class="button">
		<?php esc_html_e( 'Import CSV', 'rankflow-seo' ); ?>
	</a>
	<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=export', 'export_redirects' ) ); ?>" class="button">
		<?php esc_html_e( 'Export CSV', 'rankflow-seo' ); ?>
	</a>
</div>

<!-- Search Form -->
<form method="get" class="rankflow-seo-my-20">
	<input type="hidden" name="page" value="<?php echo esc_attr( $this->plugin_name ); ?>-redirects">
	<p class="search-box">
		<input type="search" name="s" value="<?php echo esc_attr( $rankflow_seo_search ); ?>" placeholder="<?php esc_attr_e( 'Search redirects...', 'rankflow-seo' ); ?>">
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'rankflow-seo' ); ?></button>
	</p>
</form>

<?php if ( empty( $rankflow_seo_results['redirects'] ) ) : ?>
	<div class="notice notice-info">
		<p><?php esc_html_e( 'No redirects found. Click "Add New" to create your first redirect.', 'rankflow-seo' ); ?></p>
	</div>
<?php else : ?>
	<form method="post" id="bulk-action-form">
		<?php wp_nonce_field( 'bulk_delete_redirects', 'rankflow_seo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="bulk_delete">

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<button type="submit" class="button action" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete selected redirects?', 'rankflow-seo' ) ); ?>');">
					<?php esc_html_e( 'Delete Selected', 'rankflow-seo' ); ?>
				</button>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="check-column">
						<input type="checkbox" id="select-all">
					</td>
					<th><?php esc_html_e( 'Source URL', 'rankflow-seo' ); ?></th>
					<th><?php esc_html_e( 'Target URL', 'rankflow-seo' ); ?></th>
					<th><?php esc_html_e( 'Type', 'rankflow-seo' ); ?></th>
					<th><?php esc_html_e( 'Hits', 'rankflow-seo' ); ?></th>
					<th><?php esc_html_e( 'Status', 'rankflow-seo' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'rankflow-seo' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rankflow_seo_results['redirects'] as $rankflow_seo_redirect ) : ?>
					<tr>
						<th class="check-column">
							<input type="checkbox" name="redirect_ids[]" value="<?php echo esc_attr( $rankflow_seo_redirect->id ); ?>">
						</th>
						<td>
							<strong><?php echo esc_html( $rankflow_seo_redirect->source_url ); ?></strong>
							<?php if ( $rankflow_seo_redirect->is_regex ) : ?>
								<span class="rankflow-seo-badge rankflow-seo-badge-blue">REGEX</span>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo esc_url( home_url( $rankflow_seo_redirect->target_url ) ); ?>" target="_blank">
								<?php echo esc_html( $rankflow_seo_redirect->target_url ); ?>
							</a>
						</td>
						<td>
							<span class="redirect-type redirect-type-<?php echo esc_attr( $rankflow_seo_redirect->redirect_type ); ?>">
								<?php echo esc_html( $rankflow_seo_redirect->redirect_type ); ?>
							</span>
						</td>
						<td><?php echo esc_html( number_format( $rankflow_seo_redirect->hits ) ); ?></td>
						<td>
							<?php if ( $rankflow_seo_redirect->is_active ) : ?>
								<span class="rankflow-seo-status-active">● <?php esc_html_e( 'Active', 'rankflow-seo' ); ?></span>
							<?php else : ?>
								<span class="rankflow-seo-status-inactive">● <?php esc_html_e( 'Inactive', 'rankflow-seo' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects&action=edit&id=<?php echo esc_attr( $rankflow_seo_redirect->id ); ?>" class="button button-small">
								<?php esc_html_e( 'Edit', 'rankflow-seo' ); ?>
							</a>
							<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=delete&id=' . $rankflow_seo_redirect->id, 'delete_redirect_' . $rankflow_seo_redirect->id ) ); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete this redirect?', 'rankflow-seo' ) ); ?>');">
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
	</form>
<?php endif; ?>
