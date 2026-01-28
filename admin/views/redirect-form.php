<?php
/**
 * Redirect Form View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 *
 * @var object $rankflow_seo_redirect Redirect object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rankflow_seo_is_edit    = isset( $rankflow_seo_redirect->id );
$rankflow_seo_page_title = $rankflow_seo_is_edit ? __( 'Edit Redirect', 'rankflow-seo' ) : __( 'Add New Redirect', 'rankflow-seo' );
?>

<div class="redirect-form-container rankflow-seo-form-container">
	<h2><?php echo esc_html( $rankflow_seo_page_title ); ?></h2>

	<form method="post" action="" class="redirect-form">
		<?php wp_nonce_field( 'rankflow_seo_redirect_save', 'rankflow_seo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="save_redirect">
		<?php if ( $rankflow_seo_is_edit ) : ?>
			<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $rankflow_seo_redirect->id ); ?>">
		<?php endif; ?>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="source_url">
						<?php esc_html_e( 'Source URL', 'rankflow-seo' ); ?>
						<span class="rankflow-seo-required">*</span>
					</label>
				</th>
				<td>
					<input type="text" id="source_url" name="source_url" value="<?php echo esc_attr( $rankflow_seo_redirect->source_url ); ?>" class="large-text" required placeholder="/old-page">
					<p class="description">
						<?php esc_html_e( 'The URL to redirect FROM. Use relative URLs starting with / (e.g., /old-page)', 'rankflow-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="target_url">
						<?php esc_html_e( 'Target URL', 'rankflow-seo' ); ?>
						<span class="rankflow-seo-required">*</span>
					</label>
				</th>
				<td>
					<input type="text" id="target_url" name="target_url" value="<?php echo esc_attr( $rankflow_seo_redirect->target_url ); ?>" class="large-text" required placeholder="/new-page">
					<p class="description">
						<?php esc_html_e( 'The URL to redirect TO. Can be relative (/new-page) or absolute (https://example.com)', 'rankflow-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="redirect_type"><?php esc_html_e( 'Redirect Type', 'rankflow-seo' ); ?></label>
				</th>
				<td>
					<select id="redirect_type" name="redirect_type" class="regular-text">
						<option value="301" <?php selected( $rankflow_seo_redirect->redirect_type, '301' ); ?>>301 - <?php esc_html_e( 'Permanent', 'rankflow-seo' ); ?></option>
						<option value="302" <?php selected( $rankflow_seo_redirect->redirect_type, '302' ); ?>>302 - <?php esc_html_e( 'Temporary', 'rankflow-seo' ); ?></option>
						<option value="307" <?php selected( $rankflow_seo_redirect->redirect_type, '307' ); ?>>307 - <?php esc_html_e( 'Temporary (Preserve Method)', 'rankflow-seo' ); ?></option>
						<option value="410" <?php selected( $rankflow_seo_redirect->redirect_type, '410' ); ?>>410 - <?php esc_html_e( 'Gone', 'rankflow-seo' ); ?></option>
						<option value="451" <?php selected( $rankflow_seo_redirect->redirect_type, '451' ); ?>>451 - <?php esc_html_e( 'Unavailable For Legal Reasons', 'rankflow-seo' ); ?></option>
					</select>
					<p class="description">
						<?php esc_html_e( '301 is recommended for SEO. Use 302/307 for temporary redirects.', 'rankflow-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Options', 'rankflow-seo' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="checkbox" name="is_regex" value="1" <?php checked( $rankflow_seo_redirect->is_regex, 1 ); ?>>
							<?php esc_html_e( 'Enable Regular Expression (Regex)', 'rankflow-seo' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Use regex patterns for advanced URL matching. Example: /category/(.*) → /new-category/$1', 'rankflow-seo' ); ?>
						</p>
					</fieldset>

					<fieldset class="rankflow-seo-mt-10">
						<label>
							<input type="checkbox" name="is_active" value="1" <?php checked( $rankflow_seo_redirect->is_active, 1 ); ?>>
							<?php esc_html_e( 'Active', 'rankflow-seo' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Uncheck to temporarily disable this redirect without deleting it.', 'rankflow-seo' ); ?>
						</p>
					</fieldset>
				</td>
			</tr>
		</table>

		<div class="rankflow-seo-info-box rankflow-seo-info-box-gray">
			<h4><?php esc_html_e( 'Examples:', 'rankflow-seo' ); ?></h4>
			<ul>
				<li><strong><?php esc_html_e( 'Simple:', 'rankflow-seo' ); ?></strong> /old-page → /new-page</li>
				<li><strong><?php esc_html_e( 'To Homepage:', 'rankflow-seo' ); ?></strong> /removed-page → /</li>
				<li><strong><?php esc_html_e( 'External:', 'rankflow-seo' ); ?></strong> /blog → https://blog.example.com</li>
				<li><strong><?php esc_html_e( 'Regex:', 'rankflow-seo' ); ?></strong> /category/(.*) → /new-category/$1</li>
			</ul>
		</div>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php echo $rankflow_seo_is_edit ? esc_html__( 'Update Redirect', 'rankflow-seo' ) : esc_html__( 'Add Redirect', 'rankflow-seo' ); ?>
			</button>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects" class="button button-large">
				<?php esc_html_e( 'Cancel', 'rankflow-seo' ); ?>
			</a>
		</p>
	</form>
</div>
