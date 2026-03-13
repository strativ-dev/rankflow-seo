<?php
/**
 * Dashboard page.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get statistics.
global $wpdb;
$mpseo_total_posts = wp_count_posts();

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$mpseo_posts_with_meta = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_mpseo_title'
	)
);

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$mpseo_avg_score = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT AVG(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s",
		'_mpseo_score'
	)
);

// SEO Module options.
$mpseo_sitemap_enabled = (bool) get_option( 'mpseo_sitemap_enabled', true );
$mpseo_robots_enabled  = (bool) get_option( 'mpseo_robots_enabled', false );
$mpseo_schema_enabled  = (bool) get_option( 'mpseo_schema_enabled', true );
$mpseo_social_enabled  = ( (bool) get_option( 'mpseo_og_tags', true ) || (bool) get_option( 'mpseo_twitter_cards', true ) );
$mpseo_schemas         = get_option( 'mpseo_schemas', array() );
$mpseo_schema_count    = is_array( $mpseo_schemas ) ? count( $mpseo_schemas ) : 0;

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$mpseo_redirect_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mpseo_redirects WHERE is_active = 1" );

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$mpseo_404_count = (int) $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->prefix}mpseo_404_logs WHERE created_at >= %s",
		gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
	)
);
?>

<div class="wrap mpseo-dashboard">
	<?php require_once MPSEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<div class="dashboard-content">
		<div class="dashboard-main">
			<div class="stats-grid">
				<div class="stat-card">
					<div class="stat-icon">
						<img src="<?php echo esc_url( MPSEO_PLUGIN_URL . 'assets/images/published-posts-icon.png' ); ?>" alt="Published Posts" class="mpseo-icon-45">
					</div>
					<div class="stat-content">
						<h3><?php echo esc_html( number_format( $mpseo_total_posts->publish ) ); ?></h3>
						<p><?php esc_html_e( 'Published Posts', 'metapilot-smart-seo' ); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">
						<img src="<?php echo esc_url( MPSEO_PLUGIN_URL . 'assets/images/optimized-posts-icon.png' ); ?>" alt="Optimized Posts" class="mpseo-icon-45">
					</div>
					<div class="stat-content">
						<h3><?php echo esc_html( number_format( $mpseo_posts_with_meta ) ); ?></h3>
						<p><?php esc_html_e( 'Optimized Posts', 'metapilot-smart-seo' ); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">
						<img src="<?php echo esc_url( MPSEO_PLUGIN_URL . 'assets/images/avarage-seo-score-icon.png' ); ?>" alt="Average SEO Score" class="mpseo-icon-45">
					</div>
					<div class="stat-content">
						<h3><?php echo esc_html( round( $mpseo_avg_score ?? 0 ) ); ?>/100</h3>
						<p><?php esc_html_e( 'Average SEO Score', 'metapilot-smart-seo' ); ?></p>
					</div>
				</div>

				<div class="stat-card">
					<div class="stat-icon">
						<img src="<?php echo esc_url( MPSEO_PLUGIN_URL . 'assets/images/ai-icon.png' ); ?>" alt="AI Provider" class="mpseo-icon-45">
					</div>
					<div class="stat-content">
						<h3><?php echo esc_html( MPSEO_Helper::get_provider_name( get_option( 'mpseo_api_provider', 'gemini' ) ) ); ?></h3>
						<p><?php esc_html_e( 'AI Provider', 'metapilot-smart-seo' ); ?></p>
					</div>
				</div>
			</div>

			<div class="mpseo-modules-section">
				<h2><?php esc_html_e( 'SEO Modules', 'metapilot-smart-seo' ); ?></h2>
				<p class="mpseo-modules-desc"><?php esc_html_e( 'Enable or disable SEO features directly from the dashboard.', 'metapilot-smart-seo' ); ?></p>

				<div class="mpseo-modules-grid">

					<!-- XML Sitemap -->
					<div class="mpseo-module-card <?php echo $mpseo_sitemap_enabled ? 'module-active' : 'module-inactive'; ?>">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-networking"></span></div>
							<?php if ( $mpseo_sitemap_enabled ) : ?>
								<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
							<?php else : ?>
								<div class="mpseo-module-inactive-icon"><span class="dashicons dashicons-minus"></span></div>
							<?php endif; ?>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( 'XML Sitemap', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Generate XML sitemaps for search engines', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php esc_html_e( 'Status:', 'metapilot-smart-seo' ); ?>&nbsp;<strong><?php echo $mpseo_sitemap_enabled ? esc_html__( 'Active', 'metapilot-smart-seo' ) : esc_html__( 'Inactive', 'metapilot-smart-seo' ); ?></strong>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=mpseo-sitemap' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

					<!-- Robots.txt -->
					<div class="mpseo-module-card <?php echo $mpseo_robots_enabled ? 'module-active' : 'module-inactive'; ?>">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-media-text"></span></div>
							<?php if ( $mpseo_robots_enabled ) : ?>
								<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
							<?php else : ?>
								<div class="mpseo-module-inactive-icon"><span class="dashicons dashicons-minus"></span></div>
							<?php endif; ?>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( 'Robots.txt', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Customize your robots.txt file', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php esc_html_e( 'Status:', 'metapilot-smart-seo' ); ?>&nbsp;<strong><?php echo $mpseo_robots_enabled ? esc_html__( 'Active', 'metapilot-smart-seo' ) : esc_html__( 'Inactive', 'metapilot-smart-seo' ); ?></strong>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=metapilot-smart-seo-robots-txt' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

					<!-- Schema Markup -->
					<div class="mpseo-module-card <?php echo $mpseo_schema_enabled ? 'module-active' : 'module-inactive'; ?>">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-editor-code"></span></div>
							<?php if ( $mpseo_schema_enabled ) : ?>
								<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
							<?php else : ?>
								<div class="mpseo-module-inactive-icon"><span class="dashicons dashicons-minus"></span></div>
							<?php endif; ?>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( 'Schema Markup', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Add structured data for rich snippets', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php esc_html_e( 'Status:', 'metapilot-smart-seo' ); ?>&nbsp;<strong><?php echo $mpseo_schema_enabled ? esc_html__( 'Active', 'metapilot-smart-seo' ) : esc_html__( 'Inactive', 'metapilot-smart-seo' ); ?></strong>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=mpseo-schema' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

					<!-- Social Media -->
					<div class="mpseo-module-card <?php echo $mpseo_social_enabled ? 'module-active' : 'module-inactive'; ?>">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-share"></span></div>
							<?php if ( $mpseo_social_enabled ) : ?>
								<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
							<?php else : ?>
								<div class="mpseo-module-inactive-icon"><span class="dashicons dashicons-minus"></span></div>
							<?php endif; ?>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( 'Social Media', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Open Graph &amp; Twitter Card tags', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php esc_html_e( 'Status:', 'metapilot-smart-seo' ); ?>&nbsp;<strong><?php echo $mpseo_social_enabled ? esc_html__( 'Active', 'metapilot-smart-seo' ) : esc_html__( 'Inactive', 'metapilot-smart-seo' ); ?></strong>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=metapilot-smart-seo-settings&tab=social' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

					<!-- Redirects -->
					<div class="mpseo-module-card module-active">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-randomize"></span></div>
							<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( 'Redirects', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Manage URL redirects (301, 302, etc.)', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php
							/* translators: %d: number of active redirects */
							printf( esc_html__( 'Active redirects: %d', 'metapilot-smart-seo' ), (int) $mpseo_redirect_count );
							?>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=metapilot-smart-seo-redirects' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

					<!-- 404 Monitor -->
					<div class="mpseo-module-card module-active">
						<div class="mpseo-module-card-header">
							<div class="mpseo-module-icon"><span class="dashicons dashicons-warning"></span></div>
							<div class="mpseo-module-active-icon"><span class="dashicons dashicons-yes"></span></div>
						</div>
						<h3 class="mpseo-module-title"><?php esc_html_e( '404 Monitor', 'metapilot-smart-seo' ); ?></h3>
						<p class="mpseo-module-desc"><?php esc_html_e( 'Track and log 404 error pages', 'metapilot-smart-seo' ); ?></p>
						<div class="mpseo-module-status">
							<?php
							/* translators: %d: number of 404 errors in last 7 days */
							printf( esc_html__( 'Errors (7 days): %d', 'metapilot-smart-seo' ), (int) $mpseo_404_count );
							?>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=metapilot-smart-seo-404-monitor' ) ); ?>" class="mpseo-module-link"><?php esc_html_e( 'Settings', 'metapilot-smart-seo' ); ?> &rsaquo;</a>
					</div>

				</div>
			</div>

			<div class="recent-activity">
				<h2><?php esc_html_e( 'Recent Posts', 'metapilot-smart-seo' ); ?></h2>

				<?php
				$mpseo_recent_posts = get_posts(
					array(
						'numberposts' => 10,
						'post_status' => 'publish',
					)
				);

				if ( $mpseo_recent_posts ) :
					?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Title', 'metapilot-smart-seo' ); ?></th>
								<th><?php esc_html_e( 'SEO Score', 'metapilot-smart-seo' ); ?></th>
								<th><?php esc_html_e( 'Meta Title', 'metapilot-smart-seo' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'metapilot-smart-seo' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $mpseo_recent_posts as $mpseo_post ) :
								$mpseo_score      = get_post_meta( $mpseo_post->ID, '_mpseo_score', true );
								$mpseo_meta_title = get_post_meta( $mpseo_post->ID, '_mpseo_title', true );
								?>
								<tr>
									<td>
										<strong><?php echo esc_html( $mpseo_post->post_title ); ?></strong>
									</td>
									<td>
										<?php
										echo wp_kses(
											MPSEO_Helper::format_score( $mpseo_score ? $mpseo_score : 0 ),
											array( 'span' => array( 'style' => array() ) )
										);
										?>
									</td>
									<td>
										<?php echo $mpseo_meta_title ? esc_html( '✓' ) : esc_html( '✗' ); ?>
									</td>
									<td>
										<a href="<?php echo esc_url( get_edit_post_link( $mpseo_post->ID ) ); ?>" class="button button-small">
											<?php esc_html_e( 'Edit', 'metapilot-smart-seo' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p><?php esc_html_e( 'No posts found.', 'metapilot-smart-seo' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="dashboard-sidebar">
			<?php require_once MPSEO_PLUGIN_DIR . 'admin/partials/help-sidebar.php'; ?>
		</div>
	</div>
</div>
