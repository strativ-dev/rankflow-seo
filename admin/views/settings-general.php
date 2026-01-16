<?php
/**
 * General settings tab.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$rankflow_seo_enabled_post_types = get_option('rankflow_seo_post_types', array('post', 'page'));
$rankflow_seo_title_separator = get_option('rankflow_seo_title_separator', '-');
$rankflow_seo_homepage_title = get_option('rankflow_seo_homepage_title', get_bloginfo('name'));
$rankflow_seo_homepage_description = get_option('rankflow_seo_homepage_description', get_bloginfo('description'));
?>

<form method="post" action="options.php">
	<?php settings_fields('rankflow_seo_general'); ?>

	<h2><?php esc_html_e('Post Type Settings', 'rankflow-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label><?php esc_html_e('Enable for Post Types', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<div class="post-type-list">
					<?php
					$rankflow_seo_post_types = get_post_types(array('public' => true), 'objects');

					foreach ($rankflow_seo_post_types as $rankflow_seo_post_type) {
						if ('attachment' === $rankflow_seo_post_type->name) {
							continue;
						}
						?>
						<label>
							<input type="checkbox" name="rankflow_seo_post_types[]"
								value="<?php echo esc_attr($rankflow_seo_post_type->name); ?>" <?php checked(in_array($rankflow_seo_post_type->name, $rankflow_seo_enabled_post_types, true)); ?>>
							<span><?php echo esc_html($rankflow_seo_post_type->label); ?></span>
						</label>
						<?php
					}
					?>
				</div>
				<p class="description">
					<?php esc_html_e('Select which post types should have AI SEO meta boxes', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Title Settings', 'rankflow-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="title_separator"><?php esc_html_e('Title Separator', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<select name="rankflow_seo_title_separator" id="title_separator" class="regular-text">
					<?php
					$rankflow_seo_separators = array(
						'-' => '- (dash)',
						'–' => '– (ndash)',
						'—' => '— (mdash)',
						'|' => '| (pipe)',
						'/' => '/ (slash)',
						'::' => ':: (double colon)',
						'<' => '< (less than)',
						'>' => '> (greater than)',
					);

					foreach ($rankflow_seo_separators as $rankflow_seo_sep => $rankflow_seo_label) {
						?>
						<option value="<?php echo esc_attr($rankflow_seo_sep); ?>" <?php selected($rankflow_seo_title_separator, $rankflow_seo_sep); ?>>
							<?php echo esc_html($rankflow_seo_label); ?>
						</option>
						<?php
					}
					?>
				</select>
				<p class="description">
					<?php esc_html_e('Choose the separator to use in page titles (e.g., "Post Title - Site Name")', 'rankflow-seo'); ?>
				</p>

				<div class="title-preview"
					style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-left: 4px solid #2271b1;">
					<strong><?php esc_html_e('Preview:', 'rankflow-seo'); ?></strong><br>
					<span id="title-preview-text">
						<?php echo esc_html('Your Post Title ' . $rankflow_seo_title_separator . ' ' . get_bloginfo('name')); ?>
					</span>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="homepage_title"><?php esc_html_e('Homepage Title', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="homepage_title" name="rankflow_seo_homepage_title"
					value="<?php echo esc_attr($rankflow_seo_homepage_title); ?>" class="large-text"
					placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>">
				<p class="description">
					<?php esc_html_e('The title that will be used for your homepage', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="homepage_description"><?php esc_html_e('Homepage Description', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<textarea id="homepage_description" name="rankflow_seo_homepage_description" rows="3" class="large-text"
					placeholder="<?php echo esc_attr(get_bloginfo('description')); ?>"><?php echo esc_textarea($rankflow_seo_homepage_description); ?></textarea>
				<p class="description">
					<?php esc_html_e('The description that will be used for your homepage', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Knowledge Graph', 'rankflow-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="site_represents"><?php esc_html_e('Site Represents', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<select name="rankflow_seo_site_represents" id="site_represents" class="regular-text">
					<option value="organization" <?php selected(get_option('rankflow_seo_site_represents', 'organization'), 'organization'); ?>>
						<?php esc_html_e('Organization', 'rankflow-seo'); ?>
					</option>
					<option value="person" <?php selected(get_option('rankflow_seo_site_represents'), 'person'); ?>>
						<?php esc_html_e('Person', 'rankflow-seo'); ?>
					</option>
				</select>
				<p class="description">
					<?php esc_html_e('Does this site represent an organization or a person?', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr id="organization_name_row">
			<th scope="row">
				<label for="organization_name"><?php esc_html_e('Organization Name', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="organization_name" name="rankflow_seo_organization_name"
					value="<?php echo esc_attr(get_option('rankflow_seo_organization_name', get_bloginfo('name'))); ?>"
					class="regular-text">
				<p class="description">
					<?php esc_html_e('The name of your organization', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr id="person_name_row" style="display: none;">
			<th scope="row">
				<label for="person_name"><?php esc_html_e('Person Name', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="person_name" name="rankflow_seo_person_name"
					value="<?php echo esc_attr(get_option('rankflow_seo_person_name')); ?>" class="regular-text">
				<p class="description">
					<?php esc_html_e('Your name', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site_logo"><?php esc_html_e('Site Logo URL', 'rankflow-seo'); ?></label>
			</th>
			<td>
				<input type="url" id="site_logo" name="rankflow_seo_site_logo"
					value="<?php echo esc_url(get_option('rankflow_seo_site_logo', get_site_icon_url())); ?>"
					class="regular-text">
				<p class="description">
					<?php esc_html_e('URL of your site logo (recommended: 600x60px)', 'rankflow-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>

<script>
	jQuery(document).ready(function ($) {
		// Update title preview.
		$('#title_separator').on('change', function () {
			var separator = $(this).val();
			var preview = 'Your Post Title ' + separator + ' <?php echo esc_js(get_bloginfo('name')); ?>';
			$('#title-preview-text').text(preview);
		});

		// Toggle organization/person fields.
		$('#site_represents').on('change', function () {
			if ($(this).val() === 'organization') {
				$('#organization_name_row').show();
				$('#person_name_row').hide();
			} else {
				$('#organization_name_row').hide();
				$('#person_name_row').show();
			}
		}).trigger('change');
	});
</script>