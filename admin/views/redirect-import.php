<?php
/**
 * Redirect Import View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="rankflow-seo-form-container">
	<h2><?php esc_html_e( 'Import Redirects from CSV', 'rankflow-seo' ); ?></h2>

	<div class="rankflow-seo-info-box rankflow-seo-info-box-blue">
		<h3><?php esc_html_e( 'CSV Format Requirements:', 'rankflow-seo' ); ?></h3>
		<p><?php esc_html_e( 'Your CSV file should have the following columns:', 'rankflow-seo' ); ?></p>
		<ol>
			<li><strong><?php esc_html_e( 'Source URL', 'rankflow-seo' ); ?></strong> - <?php esc_html_e( 'The URL to redirect from (required)', 'rankflow-seo' ); ?></li>
			<li><strong><?php esc_html_e( 'Target URL', 'rankflow-seo' ); ?></strong> - <?php esc_html_e( 'The URL to redirect to (required)', 'rankflow-seo' ); ?></li>
			<li><strong><?php esc_html_e( 'Type', 'rankflow-seo' ); ?></strong> - <?php esc_html_e( 'Redirect type: 301, 302, 307, 410, or 451 (optional, defaults to 301)', 'rankflow-seo' ); ?></li>
		</ol>
	</div>

	<div class="rankflow-seo-sample-box">
		<h4><?php esc_html_e( 'Sample CSV Format:', 'rankflow-seo' ); ?></h4>
		<pre>Source URL,Target URL,Type
/old-page,/new-page,301
/removed-page,/,410
/blog/old-post,/blog/new-post,301
/temporary,/temp-location,302</pre>

		<p>
			<a href="<?php echo esc_url( RANKFLOW_SEO_PLUGIN_URL . 'admin/sample-redirects.csv' ); ?>" class="button" download>
				<?php esc_html_e( 'Download Sample CSV', 'rankflow-seo' ); ?>
			</a>
		</p>
	</div>

	<form method="post" enctype="multipart/form-data" class="import-form rankflow-seo-form-box">
		<?php wp_nonce_field( 'import_redirects', 'rankflow_seo_redirect_nonce' ); ?>
		<input type="hidden" name="action" value="import_csv">

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="csv_file">
						<?php esc_html_e( 'Choose CSV File', 'rankflow-seo' ); ?>
						<span class="rankflow-seo-required">*</span>
					</label>
				</th>
				<td>
					<input type="file" id="csv_file" name="csv_file" accept=".csv" required>
					<p class="description">
						<?php esc_html_e( 'Select a CSV file to import. Maximum file size: 2MB', 'rankflow-seo' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<div class="rankflow-seo-info-box rankflow-seo-info-box-yellow">
			<h4>⚠️ <?php esc_html_e( 'Important Notes:', 'rankflow-seo' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'Duplicate source URLs will be skipped', 'rankflow-seo' ); ?></li>
				<li><?php esc_html_e( 'Invalid redirect types will default to 301', 'rankflow-seo' ); ?></li>
				<li><?php esc_html_e( 'The first row should contain column headers', 'rankflow-seo' ); ?></li>
				<li><?php esc_html_e( 'Existing redirects will NOT be overwritten', 'rankflow-seo' ); ?></li>
			</ul>
		</div>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php esc_html_e( 'Import Redirects', 'rankflow-seo' ); ?>
			</button>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>-redirects" class="button button-large">
				<?php esc_html_e( 'Cancel', 'rankflow-seo' ); ?>
			</a>
		</p>
	</form>

	<div class="rankflow-seo-info-box rankflow-seo-info-box-red">
		<h4>🔴 <?php esc_html_e( 'Before Importing:', 'rankflow-seo' ); ?></h4>
		<p><?php esc_html_e( 'It\'s recommended to export your existing redirects as a backup before importing new ones.', 'rankflow-seo' ); ?></p>
		<p>
			<a href="<?php echo esc_url( wp_nonce_url( '?page=' . $this->plugin_name . '-redirects&action=export', 'export_redirects' ) ); ?>" class="button">
				<?php esc_html_e( 'Export Current Redirects', 'rankflow-seo' ); ?>
			</a>
		</p>
	</div>
</div>
