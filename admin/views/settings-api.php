<?php
/**
 * API settings tab.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$rankflow_seo_current_provider = get_option('rankflow_seo_api_provider', 'gemini');
$rankflow_seo_api_key = get_option('rankflow_seo_api_key', '');
$rankflow_seo_auto_generate = get_option('rankflow_seo_auto_generate', false);
?>

<form method="post" action="options.php">
	<?php settings_fields('rankflow_seo_api'); ?>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="api_provider"><?php esc_html_e('AI Provider', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<select name="rankflow_seo_api_provider" id="api_provider" class="regular-text">
					<option value="anthropic" <?php selected($rankflow_seo_current_provider, 'anthropic'); ?>>
						Anthropic (Claude)
					</option>
					<option value="gemini" <?php selected($rankflow_seo_current_provider, 'gemini'); ?>>
						Google (Gemini)
					</option>
				</select>
				<p class="description">
					<?php esc_html_e('Choose which AI provider to use for generating meta tags', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="api_key"><?php esc_html_e('API Key', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="password" id="api_key" name="rankflow_seo_api_key"
					value="<?php echo esc_attr($rankflow_seo_api_key); ?>" class="regular-text">
				<p class="description">
					<?php esc_html_e('Enter your API key from your chosen provider', 'rankflow-seo'); ?>
				</p>

				<div class="api-key-instructions" style="margin-top: 15px;">
					<p><strong><?php esc_html_e('How to get your API key:', 'rankflow-seo'); ?></strong></p>
					<ul class="provider-instructions">
						<li data-provider="anthropic">
							<strong>Anthropic:</strong>
							<a href="https://console.anthropic.com/" target="_blank">
								https://console.anthropic.com/
							</a>
						</li>
						<li data-provider="gemini">
							<strong>Google Gemini:</strong>
							<a href="https://makersuite.google.com/app/apikey" target="_blank">
								https://makersuite.google.com/app/apikey
							</a>
						</li>
					</ul>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e('Auto-generate', 'rankflow-seo'); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" name="rankflow_seo_auto_generate" value="1" <?php checked($rankflow_seo_auto_generate, true); ?>>
					<?php esc_html_e('Automatically generate meta tags for new posts', 'rankflow-seo'); ?>
				</label>
				<p class="description">
					<?php esc_html_e('When enabled, meta tags will be generated automatically when you publish a new post', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>