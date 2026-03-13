<?php
/**
 * Redirect Form View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 *
 * @var object $mpseo_redirect Redirect object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mpseo_is_edit    = isset( $mpseo_redirect->id );
$mpseo_page_title = $mpseo_is_edit ? __( 'Edit Redirect', 'metapilot-smart-seo' ) : __( 'Add New Redirect', 'metapilot-smart-seo' );
?>

<div class="redirect-form-container mpseo-form-container">
	<h2><?php echo esc_html( $mpseo_page_title ); ?></h2>

	<form method="post" action="" class="redirect-form">
		<?php wp_nonce_field( 'mpseo_redirect_save', 'mpseo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="save_redirect">
		<?php if ( $mpseo_is_edit ) : ?>
			<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $mpseo_redirect->id ); ?>">
		<?php endif; ?>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="source_url">
						<?php esc_html_e( 'Source URL', 'metapilot-smart-seo' ); ?>
						<span class="mpseo-required">*</span>
					</label>
				</th>
				<td>
					<input type="text" id="source_url" name="source_url" value="<?php echo esc_attr( $mpseo_redirect->source_url ); ?>" class="large-text" required placeholder="/old-page">
					<p class="description">
						<?php esc_html_e( 'The URL to redirect FROM. Use relative URLs starting with / (e.g., /old-page)', 'metapilot-smart-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="target_url">
						<?php esc_html_e( 'Target URL', 'metapilot-smart-seo' ); ?>
						<span class="mpseo-required">*</span>
					</label>
				</th>
				<td>
					<input type="text" id="target_url" name="target_url" value="<?php echo esc_attr( $mpseo_redirect->target_url ); ?>" class="large-text" required placeholder="/new-page">
					<p class="description">
						<?php esc_html_e( 'The URL to redirect TO. Can be relative (/new-page) or absolute (https://example.com)', 'metapilot-smart-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="redirect_type"><?php esc_html_e( 'Redirect Type', 'metapilot-smart-seo' ); ?></label>
				</th>
				<td>
					<select id="redirect_type" name="redirect_type" class="regular-text">
						<option value="301" <?php selected( $mpseo_redirect->redirect_type, '301' ); ?>>301 - <?php esc_html_e( 'Permanent', 'metapilot-smart-seo' ); ?></option>
						<option value="302" <?php selected( $mpseo_redirect->redirect_type, '302' ); ?>>302 - <?php esc_html_e( 'Temporary', 'metapilot-smart-seo' ); ?></option>
						<option value="307" <?php selected( $mpseo_redirect->redirect_type, '307' ); ?>>307 - <?php esc_html_e( 'Temporary (Preserve Method)', 'metapilot-smart-seo' ); ?></option>
						<option value="410" <?php selected( $mpseo_redirect->redirect_type, '410' ); ?>>410 - <?php esc_html_e( 'Gone', 'metapilot-smart-seo' ); ?></option>
						<option value="451" <?php selected( $mpseo_redirect->redirect_type, '451' ); ?>>451 - <?php esc_html_e( 'Unavailable For Legal Reasons', 'metapilot-smart-seo' ); ?></option>
					</select>
					<p class="description">
						<?php esc_html_e( '301 is recommended for SEO. Use 302/307 for temporary redirects.', 'metapilot-smart-seo' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Options', 'metapilot-smart-seo' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="checkbox" name="is_regex" value="1" <?php checked( $mpseo_redirect->is_regex, 1 ); ?>>
							<?php esc_html_e( 'Enable Regular Expression (Regex)', 'metapilot-smart-seo' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Use regex patterns for advanced URL matching. Example: /category/(.*) → /new-category/$1', 'metapilot-smart-seo' ); ?>
						</p>
					</fieldset>

					<fieldset class="mpseo-mt-10">
						<label>
							<input type="checkbox" name="is_active" value="1" <?php checked( $mpseo_redirect->is_active, 1 ); ?>>
							<?php esc_html_e( 'Active', 'metapilot-smart-seo' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Uncheck to temporarily disable this redirect without deleting it.', 'metapilot-smart-seo' ); ?>
						</p>
					</fieldset>
				</td>
			</tr>
		</table>

		<div class="mpseo-info-box mpseo-info-box-gray">
			<h4><?php esc_html_e( 'Examples:', 'metapilot-smart-seo' ); ?></h4>
			<ul>
				<li><strong><?php esc_html_e( 'Simple:', 'metapilot-smart-seo' ); ?></strong> /old-page → /new-page</li>
				<li><strong><?php esc_html_e( 'To Homepage:', 'metapilot-smart-seo' ); ?></strong> /removed-page → /</li>
				<li><strong><?php esc_html_e( 'External:', 'metapilot-smart-seo' ); ?></strong> /blog → https://blog.example.com</li>
				<li><strong><?php esc_html_e( 'Regex:', 'metapilot-smart-seo' ); ?></strong> /category/(.*) → /new-category/$1</li>
			</ul>
		</div>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php echo $mpseo_is_edit ? esc_html__( 'Update Redirect', 'metapilot-smart-seo' ) : esc_html__( 'Add Redirect', 'metapilot-smart-seo' ); ?>
			</button>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects" class="button button-large">
				<?php esc_html_e( 'Cancel', 'metapilot-smart-seo' ); ?>
			</a>
		</p>
	</form>
</div>
