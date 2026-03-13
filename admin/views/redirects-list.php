<?php
/**
 * Redirects List View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 *
 * @var array  $mpseo_results Redirects results.
 * @var string $mpseo_search  Search query.
 * @var int    $mpseo_page    Current page number.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mpseo-actions-row">
	<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects&action=import" class="button">
		<?php esc_html_e( 'Import CSV', 'metapilot-smart-seo' ); ?>
	</a>
	<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=export', 'export_redirects' ) ); ?>" class="button">
		<?php esc_html_e( 'Export CSV', 'metapilot-smart-seo' ); ?>
	</a>
</div>

<!-- Search Form -->
<form method="get" class="mpseo-my-20">
	<input type="hidden" name="page" value="<?php echo esc_attr( $this->plugin_name ); ?>-redirects">
	<p class="search-box">
		<input type="search" name="s" value="<?php echo esc_attr( $mpseo_search ); ?>" placeholder="<?php esc_attr_e( 'Search redirects...', 'metapilot-smart-seo' ); ?>">
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'metapilot-smart-seo' ); ?></button>
	</p>
</form>

<?php if ( empty( $mpseo_results['redirects'] ) ) : ?>
	<div class="notice notice-info">
		<p><?php esc_html_e( 'No redirects found. Click "Add New" to create your first redirect.', 'metapilot-smart-seo' ); ?></p>
	</div>
<?php else : ?>
	<form method="post" id="bulk-action-form">
		<?php wp_nonce_field( 'bulk_delete_redirects', 'mpseo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="bulk_delete">

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<button type="submit" class="button action mpseo-confirm" data-confirm="<?php echo esc_attr( __( 'Are you sure you want to delete selected redirects?', 'metapilot-smart-seo' ) ); ?>">
					<?php esc_html_e( 'Delete Selected', 'metapilot-smart-seo' ); ?>
				</button>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="check-column">
						<input type="checkbox" id="select-all">
					</td>
					<th><?php esc_html_e( 'Source URL', 'metapilot-smart-seo' ); ?></th>
					<th><?php esc_html_e( 'Target URL', 'metapilot-smart-seo' ); ?></th>
					<th><?php esc_html_e( 'Type', 'metapilot-smart-seo' ); ?></th>
					<th><?php esc_html_e( 'Hits', 'metapilot-smart-seo' ); ?></th>
					<th><?php esc_html_e( 'Status', 'metapilot-smart-seo' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'metapilot-smart-seo' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $mpseo_results['redirects'] as $mpseo_redirect ) : ?>
					<tr>
						<th class="check-column">
							<input type="checkbox" name="redirect_ids[]" value="<?php echo esc_attr( $mpseo_redirect->id ); ?>">
						</th>
						<td>
							<strong><?php echo esc_html( $mpseo_redirect->source_url ); ?></strong>
							<?php if ( $mpseo_redirect->is_regex ) : ?>
								<span class="mpseo-badge mpseo-badge-blue">REGEX</span>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo esc_url( home_url( $mpseo_redirect->target_url ) ); ?>" target="_blank">
								<?php echo esc_html( $mpseo_redirect->target_url ); ?>
							</a>
						</td>
						<td>
							<span class="redirect-type redirect-type-<?php echo esc_attr( $mpseo_redirect->redirect_type ); ?>">
								<?php echo esc_html( $mpseo_redirect->redirect_type ); ?>
							</span>
						</td>
						<td><?php echo esc_html( number_format( $mpseo_redirect->hits ) ); ?></td>
						<td>
							<?php if ( $mpseo_redirect->is_active ) : ?>
								<span class="mpseo-status-active">● <?php esc_html_e( 'Active', 'metapilot-smart-seo' ); ?></span>
							<?php else : ?>
								<span class="mpseo-status-inactive">● <?php esc_html_e( 'Inactive', 'metapilot-smart-seo' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects&action=edit&id=<?php echo esc_attr( $mpseo_redirect->id ); ?>" class="button button-small">
								<?php esc_html_e( 'Edit', 'metapilot-smart-seo' ); ?>
							</a>
							<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=delete&id=' . $mpseo_redirect->id, 'delete_redirect_' . $mpseo_redirect->id ) ); ?>" class="button button-small mpseo-confirm" data-confirm="<?php echo esc_attr( __( 'Are you sure you want to delete this redirect?', 'metapilot-smart-seo' ) ); ?>">
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
	</form>
<?php endif; ?>
