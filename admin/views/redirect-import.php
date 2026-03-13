<?php
/**
 * Redirect Import View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mpseo-form-container">
	<h2><?php esc_html_e( 'Import Redirects from CSV', 'metapilot-smart-seo' ); ?></h2>

	<div class="mpseo-info-box mpseo-info-box-blue">
		<h3><?php esc_html_e( 'CSV Format Requirements:', 'metapilot-smart-seo' ); ?></h3>
		<p><?php esc_html_e( 'Your CSV file should have the following columns:', 'metapilot-smart-seo' ); ?></p>
		<ol>
			<li><strong><?php esc_html_e( 'Source URL', 'metapilot-smart-seo' ); ?></strong> - <?php esc_html_e( 'The URL to redirect from (required)', 'metapilot-smart-seo' ); ?></li>
			<li><strong><?php esc_html_e( 'Target URL', 'metapilot-smart-seo' ); ?></strong> - <?php esc_html_e( 'The URL to redirect to (required)', 'metapilot-smart-seo' ); ?></li>
			<li><strong><?php esc_html_e( 'Type', 'metapilot-smart-seo' ); ?></strong> - <?php esc_html_e( 'Redirect type: 301, 302, 307, 410, or 451 (optional, defaults to 301)', 'metapilot-smart-seo' ); ?></li>
		</ol>
	</div>

	<div class="mpseo-sample-box">
		<h4><?php esc_html_e( 'Sample CSV Format:', 'metapilot-smart-seo' ); ?></h4>
		<pre>Source URL,Target URL,Type
/old-page,/new-page,301
/removed-page,/,410
/blog/old-post,/blog/new-post,301
/temporary,/temp-location,302</pre>

		<p>
			<a href="<?php echo esc_url( MPSEO_PLUGIN_URL . 'admin/sample-redirects.csv' ); ?>" class="button" download>
				<?php esc_html_e( 'Download Sample CSV', 'metapilot-smart-seo' ); ?>
			</a>
		</p>
	</div>

	<form method="post" enctype="multipart/form-data" class="import-form mpseo-form-box">
		<?php wp_nonce_field( 'import_redirects', 'mpseo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="import_csv">

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="csv_file">
						<?php esc_html_e( 'Choose CSV File', 'metapilot-smart-seo' ); ?>
						<span class="mpseo-required">*</span>
					</label>
				</th>
				<td>
					<input type="file" id="csv_file" name="csv_file" accept=".csv" required>
					<p class="description">
						<?php esc_html_e( 'Select a CSV file to import. Maximum file size: 2MB', 'metapilot-smart-seo' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<div class="mpseo-info-box mpseo-info-box-yellow">
			<h4>⚠️ <?php esc_html_e( 'Important Notes:', 'metapilot-smart-seo' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'Duplicate source URLs will be skipped', 'metapilot-smart-seo' ); ?></li>
				<li><?php esc_html_e( 'Invalid redirect types will default to 301', 'metapilot-smart-seo' ); ?></li>
				<li><?php esc_html_e( 'The first row should contain column headers', 'metapilot-smart-seo' ); ?></li>
				<li><?php esc_html_e( 'Existing redirects will NOT be overwritten', 'metapilot-smart-seo' ); ?></li>
			</ul>
		</div>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php esc_html_e( 'Import Redirects', 'metapilot-smart-seo' ); ?>
			</button>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects" class="button button-large">
				<?php esc_html_e( 'Cancel', 'metapilot-smart-seo' ); ?>
			</a>
		</p>
	</form>

	<div class="mpseo-info-box mpseo-info-box-red">
		<h4>🔴 <?php esc_html_e( 'Before Importing:', 'metapilot-smart-seo' ); ?></h4>
		<p><?php esc_html_e( 'It\'s recommended to export your existing redirects as a backup before importing new ones.', 'metapilot-smart-seo' ); ?></p>
		<p>
			<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=export', 'export_redirects' ) ); ?>" class="button">
				<?php esc_html_e( 'Export Current Redirects', 'metapilot-smart-seo' ); ?>
			</a>
		</p>
	</div>
</div>
