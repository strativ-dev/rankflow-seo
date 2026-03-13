<?php
/**
 * Schema Settings View
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Get current settings.
$mpseo_schema_enabled = get_option('mpseo_schema_enabled', true);
$mpseo_schemas = get_option('mpseo_schemas', array());
$mpseo_schema_admin = new MPSEO_Schema_Admin();
$mpseo_schema_types = $mpseo_schema_admin->get_schema_types();
?>

<div class="wrap mpseo-schema">
	<?php require_once MPSEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<p class="description mpseo-description">
		<?php esc_html_e('Generate structured data (schema markup) for your website to help search engines understand your content better and display rich results.', 'metapilot-smart-seo'); ?>
	</p>

	<form method="post" action="options.php" id="mpseo-schema-form">
		<?php settings_fields('mpseo_schema'); ?>

		<!-- Enable Toggle -->
		<div class="mpseo-card">
			<h2><?php esc_html_e('Schema Settings', 'metapilot-smart-seo'); ?></h2>
			<label class="mpseo-toggle">
				<input type="checkbox" name="mpseo_schema_enabled" value="1" <?php checked($mpseo_schema_enabled); ?>>
				<span class="toggle-slider"></span>
				<span class="toggle-label"><?php esc_html_e('Enable Schema Markup Output', 'metapilot-smart-seo'); ?></span>
			</label>
			<p class="description">
				<?php esc_html_e('When enabled, schema markup will be added to your website\'s head section.', 'metapilot-smart-seo'); ?>
			</p>
		</div>

		<!-- Schema Repeater -->
		<div class="mpseo-card">
			<div class="card-header">
				<h2><?php esc_html_e('Schema Markup', 'metapilot-smart-seo'); ?></h2>
				<button type="button" id="add-schema" class="button button-primary">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e('Add Schema', 'metapilot-smart-seo'); ?>
				</button>
			</div>

			<div id="schema-repeater" class="schema-repeater">
				<?php if (!empty($mpseo_schemas)): ?>
					<?php foreach ($mpseo_schemas as $mpseo_index => $mpseo_schema): ?>
						<div class="schema-item" data-index="<?php echo esc_attr($mpseo_index); ?>">
							<div class="schema-header">
								<span class="schema-drag-handle dashicons dashicons-menu"></span>
								<label class="schema-enable">
									<input type="checkbox"
										name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][enabled]"
										value="1" <?php checked(!empty($mpseo_schema['enabled'])); ?>>
								</label>
								<select class="schema-type-select"
									name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][type]">
									<option value=""><?php esc_html_e('— Select Schema Type —', 'metapilot-smart-seo'); ?></option>
									<?php foreach ($mpseo_schema_types as $mpseo_type => $mpseo_config): ?>
										<option value="<?php echo esc_attr($mpseo_type); ?>" <?php selected($mpseo_schema['type'], $mpseo_type); ?>>
											<?php echo esc_html($mpseo_config['label']); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span
									class="schema-title"><?php echo !empty($mpseo_schema['data']['name']) ? esc_html($mpseo_schema['data']['name']) : ''; ?></span>
								<button type="button" class="schema-toggle"
									title="<?php esc_attr_e('Toggle', 'metapilot-smart-seo'); ?>">
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</button>
								<button type="button" class="schema-delete"
									title="<?php esc_attr_e('Delete', 'metapilot-smart-seo'); ?>">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
							<div class="schema-content mpseo-hidden">
								<!-- Display Rules Section -->
								<div class="schema-display-rules">
									<h4><?php esc_html_e('Display Rules', 'metapilot-smart-seo'); ?></h4>

									<div class="display-rule-row">
										<label><?php esc_html_e('Show on:', 'metapilot-smart-seo'); ?></label>
										<select class="display-mode-select"
											name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][display_mode]">
											<option value="all" <?php selected($mpseo_schema['display_mode'] ?? 'all', 'all'); ?>><?php esc_html_e('All Pages', 'metapilot-smart-seo'); ?></option>
											<option value="homepage" <?php selected($mpseo_schema['display_mode'] ?? '', 'homepage'); ?>><?php esc_html_e('Homepage Only', 'metapilot-smart-seo'); ?></option>
											<option value="post_types" <?php selected($mpseo_schema['display_mode'] ?? '', 'post_types'); ?>>
												<?php esc_html_e('Specific Post Types', 'metapilot-smart-seo'); ?>
											</option>
											<option value="include" <?php selected($mpseo_schema['display_mode'] ?? '', 'include'); ?>>
												<?php esc_html_e('Only Specific Pages/Posts', 'metapilot-smart-seo'); ?>
											</option>
											<option value="exclude" <?php selected($mpseo_schema['display_mode'] ?? '', 'exclude'); ?>>
												<?php esc_html_e('All Except Specific Pages/Posts', 'metapilot-smart-seo'); ?>
											</option>
										</select>
									</div>

									<!-- Post Types Selection -->
									<div class="display-rule-row post-types-row <?php echo esc_attr(($mpseo_schema['display_mode'] ?? '') === 'post_types' ? '' : 'mpseo-hidden'); ?>">
										<label><?php esc_html_e('Post Types:', 'metapilot-smart-seo'); ?></label>
										<select class="post-types-select"
											name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][post_types][]"
											multiple>
											<?php
											$mpseo_post_types = get_post_types(array('public' => true), 'objects');
											$mpseo_selected_types = $mpseo_schema['post_types'] ?? array();
											foreach ($mpseo_post_types as $mpseo_pt):
												if ('attachment' === $mpseo_pt->name) {
													continue;
												}
												?>
												<option value="<?php echo esc_attr($mpseo_pt->name); ?>" <?php selected(in_array($mpseo_pt->name, $mpseo_selected_types, true), true); ?>>
													<?php echo esc_html($mpseo_pt->label); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Include Pages/Posts -->
									<div class="display-rule-row include-row <?php echo esc_attr(($mpseo_schema['display_mode'] ?? '') === 'include' ? '' : 'mpseo-hidden'); ?>">
										<label><?php esc_html_e('Include:', 'metapilot-smart-seo'); ?></label>
										<select class="include-select"
											name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][include_ids][]"
											multiple>
											<?php
											$mpseo_include_ids = $mpseo_schema['include_ids'] ?? array();
											$mpseo_all_content = get_posts(array(
												'post_type' => array('post', 'page'),
												'posts_per_page' => -1,
												'post_status' => 'publish',
												'orderby' => 'title',
												'order' => 'ASC',
											));
											foreach ($mpseo_all_content as $mpseo_content):
												?>
												<option value="<?php echo esc_attr($mpseo_content->ID); ?>" <?php selected(in_array($mpseo_content->ID, $mpseo_include_ids, true), true); ?>>
													<?php echo esc_html($mpseo_content->post_title); ?>
													(<?php echo esc_html(ucfirst($mpseo_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Exclude Pages/Posts -->
									<div class="display-rule-row exclude-row <?php echo esc_attr(($mpseo_schema['display_mode'] ?? '') === 'exclude' ? '' : 'mpseo-hidden'); ?>">
										<label><?php esc_html_e('Exclude:', 'metapilot-smart-seo'); ?></label>
										<select class="exclude-select"
											name="mpseo_schemas[<?php echo esc_attr($mpseo_index); ?>][exclude_ids][]"
											multiple>
											<?php
											$mpseo_exclude_ids = $mpseo_schema['exclude_ids'] ?? array();
											foreach ($mpseo_all_content as $mpseo_content):
												?>
												<option value="<?php echo esc_attr($mpseo_content->ID); ?>" <?php selected(in_array($mpseo_content->ID, $mpseo_exclude_ids, true), true); ?>>
													<?php echo esc_html($mpseo_content->post_title); ?>
													(<?php echo esc_html(ucfirst($mpseo_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="schema-fields">
									<?php
									if (!empty($mpseo_schema['type']) && isset($mpseo_schema_types[$mpseo_schema['type']])) {
										$mpseo_fields = $mpseo_schema_types[$mpseo_schema['type']]['fields'];
										foreach ($mpseo_fields as $mpseo_field_key => $mpseo_field_config) {
											$mpseo_field_value = isset($mpseo_schema['data'][$mpseo_field_key]) ? $mpseo_schema['data'][$mpseo_field_key] : '';
											$mpseo_field_name = 'mpseo_schemas[' . esc_attr($mpseo_index) . '][data][' . esc_attr($mpseo_field_key) . ']';
											$mpseo_field_id = 'schema_' . esc_attr($mpseo_index) . '_' . esc_attr($mpseo_field_key);

											echo '<div class="schema-field">';
											echo '<label for="' . esc_attr($mpseo_field_id) . '">';
											echo esc_html($mpseo_field_config['label']);
											if (!empty($mpseo_field_config['required'])) {
												echo ' <span class="required">*</span>';
											}
											echo '</label>';

											switch ($mpseo_field_config['type']) {
												case 'textarea':
													echo '<textarea id="' . esc_attr($mpseo_field_id) . '" ';
													echo 'name="' . esc_attr($mpseo_field_name) . '" ';
													echo 'rows="4" class="widefat" ';
													if (!empty($mpseo_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($mpseo_field_config['placeholder']) . '"';
													}
													echo '>' . esc_textarea($mpseo_field_value) . '</textarea>';
													break;

												case 'select':
													echo '<select id="' . esc_attr($mpseo_field_id) . '" ';
													echo 'name="' . esc_attr($mpseo_field_name) . '" class="widefat">';
													echo '<option value="">' . esc_html__('— Select —', 'metapilot-smart-seo') . '</option>';
													foreach ($mpseo_field_config['options'] as $mpseo_opt_value => $mpseo_opt_label) {
														echo '<option value="' . esc_attr($mpseo_opt_value) . '" ' . selected($mpseo_field_value, $mpseo_opt_value, false) . '>';
														echo esc_html($mpseo_opt_label) . '</option>';
													}
													echo '</select>';
													break;

												case 'image':
													echo '<div class="image-field-wrapper">';
													echo '<input type="url" id="' . esc_attr($mpseo_field_id) . '" ';
													echo 'name="' . esc_attr($mpseo_field_name) . '" ';
													echo 'value="' . esc_attr($mpseo_field_value) . '" class="widefat image-url-input">';
													echo '<button type="button" class="button select-image">' . esc_html__('Select Image', 'metapilot-smart-seo') . '</button>';
													echo '</div>';
													break;

												case 'checkbox':
													echo '<label class="checkbox-label">';
													echo '<input type="checkbox" id="' . esc_attr($mpseo_field_id) . '" ';
													echo 'name="' . esc_attr($mpseo_field_name) . '" value="1" ';
													checked($mpseo_field_value, '1');
													echo '>';
													if (!empty($mpseo_field_config['description'])) {
														echo ' ' . esc_html($mpseo_field_config['description']);
													}
													echo '</label>';
													break;

												case 'hours':
													echo '<div class="hours-repeater" data-name="' . esc_attr($mpseo_field_name) . '">';
													if (!empty($mpseo_field_value) && is_array($mpseo_field_value)) {
														foreach ($mpseo_field_value as $mpseo_h_index => $mpseo_hour) {
															$mpseo_h_index_safe = intval($mpseo_h_index);
															echo '<div class="hour-row">';
															echo '<select name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_h_index_safe) . '][days][]" multiple class="days-select">';
															$mpseo_days = array(
																'Mo' => 'Monday',
																'Tu' => 'Tuesday',
																'We' => 'Wednesday',
																'Th' => 'Thursday',
																'Fr' => 'Friday',
																'Sa' => 'Saturday',
																'Su' => 'Sunday',
															);
															foreach ($mpseo_days as $mpseo_d_val => $mpseo_d_label) {
																$mpseo_is_selected = isset($mpseo_hour['days']) && in_array($mpseo_d_val, $mpseo_hour['days'], true);
																echo '<option value="' . esc_attr($mpseo_d_val) . '" ' . selected($mpseo_is_selected, true, false) . '>' . esc_html($mpseo_d_label) . '</option>';
															}
															echo '</select>';
															echo '<input type="time" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_h_index_safe) . '][open]" value="' . esc_attr($mpseo_hour['open'] ?? '') . '" placeholder="Open">';
															echo '<input type="time" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_h_index_safe) . '][close]" value="' . esc_attr($mpseo_hour['close'] ?? '') . '" placeholder="Close">';
															echo '<button type="button" class="button remove-hour">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-hour">' . esc_html__('Add Hours', 'metapilot-smart-seo') . '</button>';
													echo '</div>';
													break;

												case 'faq_repeater':
													echo '<div class="faq-repeater" data-name="' . esc_attr($mpseo_field_name) . '">';
													if (!empty($mpseo_field_value) && is_array($mpseo_field_value)) {
														foreach ($mpseo_field_value as $mpseo_f_index => $mpseo_faq) {
															$mpseo_f_index_safe = intval($mpseo_f_index);
															echo '<div class="faq-row">';
															echo '<input type="text" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_f_index_safe) . '][question]" value="' . esc_attr($mpseo_faq['question'] ?? '') . '" placeholder="' . esc_attr__('Question', 'metapilot-smart-seo') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_f_index_safe) . '][answer]" placeholder="' . esc_attr__('Answer', 'metapilot-smart-seo') . '" class="widefat" rows="2">' . esc_textarea($mpseo_faq['answer'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-faq">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-faq">' . esc_html__('Add FAQ', 'metapilot-smart-seo') . '</button>';
													echo '</div>';
													break;

												case 'steps_repeater':
													echo '<div class="steps-repeater" data-name="' . esc_attr($mpseo_field_name) . '">';
													if (!empty($mpseo_field_value) && is_array($mpseo_field_value)) {
														foreach ($mpseo_field_value as $mpseo_s_index => $mpseo_step) {
															$mpseo_s_index_safe = intval($mpseo_s_index);
															echo '<div class="step-row">';
															echo '<span class="step-number">' . esc_html($mpseo_s_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_s_index_safe) . '][name]" value="' . esc_attr($mpseo_step['name'] ?? '') . '" placeholder="' . esc_attr__('Step Name (optional)', 'metapilot-smart-seo') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_s_index_safe) . '][text]" placeholder="' . esc_attr__('Step Instructions', 'metapilot-smart-seo') . '" class="widefat" rows="2">' . esc_textarea($mpseo_step['text'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-step">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-step">' . esc_html__('Add Step', 'metapilot-smart-seo') . '</button>';
													echo '</div>';
													break;

												case 'breadcrumb_repeater':
													echo '<div class="breadcrumb-repeater" data-name="' . esc_attr($mpseo_field_name) . '">';
													if (!empty($mpseo_field_value) && is_array($mpseo_field_value)) {
														foreach ($mpseo_field_value as $mpseo_b_index => $mpseo_item) {
															$mpseo_b_index_safe = intval($mpseo_b_index);
															echo '<div class="breadcrumb-row">';
															echo '<span class="breadcrumb-number">' . esc_html($mpseo_b_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_b_index_safe) . '][name]" value="' . esc_attr($mpseo_item['name'] ?? '') . '" placeholder="' . esc_attr__('Name', 'metapilot-smart-seo') . '">';
															echo '<input type="url" name="' . esc_attr($mpseo_field_name) . '[' . esc_attr($mpseo_b_index_safe) . '][url]" value="' . esc_attr($mpseo_item['url'] ?? '') . '" placeholder="' . esc_attr__('URL', 'metapilot-smart-seo') . '">';
															echo '<button type="button" class="button remove-breadcrumb">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-breadcrumb">' . esc_html__('Add Item', 'metapilot-smart-seo') . '</button>';
													echo '</div>';
													break;

												default:
													echo '<input type="' . esc_attr($mpseo_field_config['type']) . '" ';
													echo 'id="' . esc_attr($mpseo_field_id) . '" ';
													echo 'name="' . esc_attr($mpseo_field_name) . '" ';
													echo 'value="' . esc_attr($mpseo_field_value) . '" ';
													echo 'class="widefat" ';
													if (!empty($mpseo_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($mpseo_field_config['placeholder']) . '"';
													}
													echo '>';
											}

											echo '</div>';
										}
									}
									?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<p class="no-schemas-message <?php echo esc_attr(!empty($mpseo_schemas) ? 'mpseo-hidden' : ''); ?>">
				<?php esc_html_e('No schemas added yet. Click "Add Schema" to create your first schema markup.', 'metapilot-smart-seo'); ?>
			</p>
		</div>

		<!-- Submit Button -->
		<div class="mpseo-card submit-card">
			<?php submit_button(esc_html__('Save Schemas', 'metapilot-smart-seo'), 'primary', 'submit', false); ?>
		</div>
	</form>

	<!-- Schema Type Template (Hidden) -->
	<template id="schema-item-template">
		<div class="schema-item" data-index="{{INDEX}}">
			<div class="schema-header">
				<span class="schema-drag-handle dashicons dashicons-menu"></span>
				<label class="schema-enable">
					<input type="checkbox" name="mpseo_schemas[{{INDEX}}][enabled]" value="1" checked>
				</label>
				<select class="schema-type-select" name="mpseo_schemas[{{INDEX}}][type]">
					<option value=""><?php esc_html_e('— Select Schema Type —', 'metapilot-smart-seo'); ?></option>
					<?php foreach ($mpseo_schema_types as $mpseo_type => $mpseo_config): ?>
												<option value="<?php echo esc_attr($mpseo_type); ?>"><?php echo esc_html($mpseo_config['label']); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="schema-title"></span>
				<button type="button" class="schema-toggle" title="<?php esc_attr_e('Toggle', 'metapilot-smart-seo'); ?>">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
				<button type="button" class="schema-delete" title="<?php esc_attr_e('Delete', 'metapilot-smart-seo'); ?>">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
			<div class="schema-content">
				<!-- Display Rules Section -->
				<div class="schema-display-rules">
					<h4><?php esc_html_e('Display Rules', 'metapilot-smart-seo'); ?></h4>
					
					<div class="display-rule-row">
						<label><?php esc_html_e('Show on:', 'metapilot-smart-seo'); ?></label>
						<select class="display-mode-select" name="mpseo_schemas[{{INDEX}}][display_mode]">
							<option value="all"><?php esc_html_e('All Pages', 'metapilot-smart-seo'); ?></option>
							<option value="homepage"><?php esc_html_e('Homepage Only', 'metapilot-smart-seo'); ?></option>
							<option value="post_types"><?php esc_html_e('Specific Post Types', 'metapilot-smart-seo'); ?></option>
							<option value="include"><?php esc_html_e('Only Specific Pages/Posts', 'metapilot-smart-seo'); ?></option>
							<option value="exclude"><?php esc_html_e('All Except Specific Pages/Posts', 'metapilot-smart-seo'); ?></option>
						</select>
					</div>

					<!-- Post Types Selection -->
					<div class="display-rule-row post-types-row mpseo-hidden">
						<label><?php esc_html_e('Post Types:', 'metapilot-smart-seo'); ?></label>
						<select class="post-types-select" name="mpseo_schemas[{{INDEX}}][post_types][]" multiple>
							<?php
							$mpseo_post_types = get_post_types(array('public' => true), 'objects');
							foreach ($mpseo_post_types as $mpseo_pt):
								if ('attachment' === $mpseo_pt->name) {
									continue;
								}
								?>
														<option value="<?php echo esc_attr($mpseo_pt->name); ?>"><?php echo esc_html($mpseo_pt->label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Include Pages/Posts -->
					<div class="display-rule-row include-row mpseo-hidden">
						<label><?php esc_html_e('Include:', 'metapilot-smart-seo'); ?></label>
						<select class="include-select" name="mpseo_schemas[{{INDEX}}][include_ids][]" multiple>
							<?php
							$mpseo_all_content = get_posts(array(
								'post_type' => array('post', 'page'),
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'orderby' => 'title',
								'order' => 'ASC',
							));
							foreach ($mpseo_all_content as $mpseo_content):
								?>
														<option value="<?php echo esc_attr($mpseo_content->ID); ?>">
															<?php echo esc_html($mpseo_content->post_title); ?> (<?php echo esc_html(ucfirst($mpseo_content->post_type)); ?>)
														</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Exclude Pages/Posts -->
					<div class="display-rule-row exclude-row mpseo-hidden">
						<label><?php esc_html_e('Exclude:', 'metapilot-smart-seo'); ?></label>
						<select class="exclude-select" name="mpseo_schemas[{{INDEX}}][exclude_ids][]" multiple>
							<?php foreach ($mpseo_all_content as $mpseo_content): ?>
														<option value="<?php echo esc_attr($mpseo_content->ID); ?>">
															<?php echo esc_html($mpseo_content->post_title); ?> (<?php echo esc_html(ucfirst($mpseo_content->post_type)); ?>)
														</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="schema-fields">
					<p class="select-type-message"><?php esc_html_e('Please select a schema type to see available fields.', 'metapilot-smart-seo'); ?></p>
				</div>
			</div>
		</div>
	</template>
</div>

