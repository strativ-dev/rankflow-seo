<?php
/**
 * General settings tab.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}

$mpseo_enabled_post_types = get_option('mpseo_post_types', array('post', 'page'));
$mpseo_title_separator = get_option('mpseo_title_separator', '-');
$mpseo_homepage_title = get_option('mpseo_homepage_title', get_bloginfo('name'));
$mpseo_homepage_description = get_option('mpseo_homepage_description', get_bloginfo('description'));
?>

<form method="post" action="options.php">
	<?php settings_fields('mpseo_general'); ?>

	<h2><?php esc_html_e('Post Type Settings', 'metapilot-smart-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label><?php esc_html_e('Enable for Post Types', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<div class="post-type-list">
					<?php
					$mpseo_post_types = get_post_types(array('public' => true), 'objects');

					foreach ($mpseo_post_types as $mpseo_post_type) {
						if ('attachment' === $mpseo_post_type->name) {
							continue;
						}
						?>
						<label>
							<input type="checkbox" name="mpseo_post_types[]"
								value="<?php echo esc_attr($mpseo_post_type->name); ?>" <?php checked(in_array($mpseo_post_type->name, $mpseo_enabled_post_types, true)); ?>>
							<span><?php echo esc_html($mpseo_post_type->label); ?></span>
						</label>
						<?php
					}
					?>
				</div>
				<p class="description">
					<?php esc_html_e('Select which post types should have AI SEO meta boxes', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Title Settings', 'metapilot-smart-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="title_separator"><?php esc_html_e('Title Separator', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<select name="mpseo_title_separator" id="title_separator" class="regular-text">
					<?php
					$mpseo_separators = array(
						'-' => '- (dash)',
						'–' => '– (ndash)',
						'—' => '— (mdash)',
						'|' => '| (pipe)',
						'/' => '/ (slash)',
						'::' => ':: (double colon)',
						'<' => '< (less than)',
						'>' => '> (greater than)',
					);

					foreach ($mpseo_separators as $mpseo_sep => $mpseo_label) {
						?>
						<option value="<?php echo esc_attr($mpseo_sep); ?>" <?php selected($mpseo_title_separator, $mpseo_sep); ?>>
							<?php echo esc_html($mpseo_label); ?>
						</option>
						<?php
					}
					?>
				</select>
				<p class="description">
					<?php esc_html_e('Choose the separator to use in page titles (e.g., "Post Title - Site Name")', 'metapilot-smart-seo'); ?>
				</p>

				<div class="mpseo-title-preview">
					<strong><?php esc_html_e('Preview:', 'metapilot-smart-seo'); ?></strong><br>
					<span id="title-preview-text" data-site-name="<?php echo esc_attr(get_bloginfo('name')); ?>">
						<?php echo esc_html('Your Post Title ' . $mpseo_title_separator . ' ' . get_bloginfo('name')); ?>
					</span>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="homepage_title"><?php esc_html_e('Homepage Title', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="homepage_title" name="mpseo_homepage_title"
					value="<?php echo esc_attr($mpseo_homepage_title); ?>" class="large-text"
					placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>">
				<p class="description">
					<?php esc_html_e('The title that will be used for your homepage', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="homepage_description"><?php esc_html_e('Homepage Description', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<textarea id="homepage_description" name="mpseo_homepage_description" rows="3" class="large-text"
					placeholder="<?php echo esc_attr(get_bloginfo('description')); ?>"><?php echo esc_textarea($mpseo_homepage_description); ?></textarea>
				<p class="description">
					<?php esc_html_e('The description that will be used for your homepage', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e('Knowledge Graph', 'metapilot-smart-seo'); ?></h2>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="site_represents"><?php esc_html_e('Site Represents', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<select name="mpseo_site_represents" id="site_represents" class="regular-text">
					<option value="organization" <?php selected(get_option('mpseo_site_represents', 'organization'), 'organization'); ?>>
						<?php esc_html_e('Organization', 'metapilot-smart-seo'); ?>
					</option>
					<option value="person" <?php selected(get_option('mpseo_site_represents'), 'person'); ?>>
						<?php esc_html_e('Person', 'metapilot-smart-seo'); ?>
					</option>
				</select>
				<p class="description">
					<?php esc_html_e('Does this site represent an organization or a person?', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>

		<tr id="organization_name_row">
			<th scope="row">
				<label for="organization_name"><?php esc_html_e('Organization Name', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="organization_name" name="mpseo_organization_name"
					value="<?php echo esc_attr(get_option('mpseo_organization_name', get_bloginfo('name'))); ?>"
					class="regular-text">
				<p class="description">
					<?php esc_html_e('The name of your organization', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>

		<tr id="person_name_row" class="mpseo-hidden">
			<th scope="row">
				<label for="person_name"><?php esc_html_e('Person Name', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<input type="text" id="person_name" name="mpseo_person_name"
					value="<?php echo esc_attr(get_option('mpseo_person_name')); ?>" class="regular-text">
				<p class="description">
					<?php esc_html_e('Your name', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site_logo"><?php esc_html_e('Site Logo URL', 'metapilot-smart-seo'); ?></label>
			</th>
			<td>
				<input type="url" id="site_logo" name="mpseo_site_logo"
					value="<?php echo esc_url(get_option('mpseo_site_logo', get_site_icon_url())); ?>"
					class="regular-text">
				<p class="description">
					<?php esc_html_e('URL of your site logo (recommended: 600x60px)', 'metapilot-smart-seo'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>
</form>

