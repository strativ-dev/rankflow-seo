<?php
/**
 * API settings tab.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$mpseo_current_provider = get_option('mpseo_api_provider', 'gemini');
$mpseo_api_key = get_option('mpseo_api_key', '');
$mpseo_auto_generate = get_option('mpseo_auto_generate', false);
?>

<form method="post" action="options.php">
	<?php settings_fields('mpseo_api'); ?>
	<div class="mpseo-section-card">
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="api_provider"><?php esc_html_e('AI Provider', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<select name="mpseo_api_provider" id="api_provider" class="regular-text">
						<option value="anthropic" <?php selected($mpseo_current_provider, 'anthropic'); ?>>
							Anthropic (Claude)
						</option>
						<option value="gemini" <?php selected($mpseo_current_provider, 'gemini'); ?>>
							Google (Gemini)
						</option>
					</select>
					<p class="description">
							<?php esc_html_e('Choose which AI provider to use for generating meta tags', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="api_key"><?php esc_html_e('API Key', 'metapilot-smart-seo'); ?></label>
				</th>
				<td>
					<input type="password" id="api_key" name="mpseo_api_key"
						value="<?php echo esc_attr($mpseo_api_key); ?>" class="regular-text">
					<p class="description">
							<?php esc_html_e('Enter your API key from your chosen provider', 'metapilot-smart-seo'); ?>
					</p>

					<div class="api-key-instructions mpseo-mt-15">
						<p><strong><?php esc_html_e('How to get your API key:', 'metapilot-smart-seo'); ?></strong></p>
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
						<?php esc_html_e('Auto-generate', 'metapilot-smart-seo'); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="mpseo_auto_generate" value="1" <?php checked($mpseo_auto_generate, true); ?>>
							<?php esc_html_e('Automatically generate meta tags for new posts', 'metapilot-smart-seo'); ?>
					</label>
					<p class="description">
							<?php esc_html_e('When enabled, meta tags will be generated automatically when you publish a new post', 'metapilot-smart-seo'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<?php submit_button(); ?>
</form>