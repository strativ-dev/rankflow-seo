<?php
/**
 * Schema Settings View
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Get current settings.
$rankflow_seo_schema_enabled = get_option('rankflow_seo_schema_enabled', true);
$rankflow_seo_schemas = get_option('rankflow_seo_schemas', array());
$rankflow_seo_schema_admin = new RankFlow_SEO_Schema_Admin();
$rankflow_seo_schema_types = $rankflow_seo_schema_admin->get_schema_types();
?>

<div class="wrap rankflow-seo-schema">
	<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<p class="description rankflow-seo-description">
		<?php esc_html_e('Generate structured data (schema markup) for your website to help search engines understand your content better and display rich results.', 'rankflow-seo'); ?>
	</p>

	<?php settings_errors('rankflow_seo_schema'); ?>

	<form method="post" action="options.php" id="rankflow-seo-schema-form">
		<?php settings_fields('rankflow_seo_schema'); ?>

		<!-- Enable Toggle -->
		<div class="rankflow-seo-card">
			<h2><?php esc_html_e('Schema Settings', 'rankflow-seo'); ?></h2>
			<label class="rankflow-seo-toggle">
				<input type="checkbox" name="rankflow_seo_schema_enabled" value="1" <?php checked($rankflow_seo_schema_enabled); ?>>
				<span class="toggle-slider"></span>
				<span class="toggle-label"><?php esc_html_e('Enable Schema Markup Output', 'rankflow-seo'); ?></span>
			</label>
			<p class="description">
				<?php esc_html_e('When enabled, schema markup will be added to your website\'s head section.', 'rankflow-seo'); ?>
			</p>
		</div>

		<!-- Schema Repeater -->
		<div class="rankflow-seo-card">
			<div class="card-header">
				<h2><?php esc_html_e('Schema Markup', 'rankflow-seo'); ?></h2>
				<button type="button" id="add-schema" class="button button-primary">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e('Add Schema', 'rankflow-seo'); ?>
				</button>
			</div>

			<div id="schema-repeater" class="schema-repeater">
				<?php if (!empty($rankflow_seo_schemas)): ?>
					<?php foreach ($rankflow_seo_schemas as $rankflow_seo_index => $rankflow_seo_schema): ?>
						<div class="schema-item" data-index="<?php echo esc_attr($rankflow_seo_index); ?>">
							<div class="schema-header">
								<span class="schema-drag-handle dashicons dashicons-menu"></span>
								<label class="schema-enable">
									<input type="checkbox"
										name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][enabled]"
										value="1" <?php checked(!empty($rankflow_seo_schema['enabled'])); ?>>
								</label>
								<select class="schema-type-select"
									name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][type]">
									<option value=""><?php esc_html_e('— Select Schema Type —', 'rankflow-seo'); ?></option>
									<?php foreach ($rankflow_seo_schema_types as $rankflow_seo_type => $rankflow_seo_config): ?>
										<option value="<?php echo esc_attr($rankflow_seo_type); ?>" <?php selected($rankflow_seo_schema['type'], $rankflow_seo_type); ?>>
											<?php echo esc_html($rankflow_seo_config['label']); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span
									class="schema-title"><?php echo !empty($rankflow_seo_schema['data']['name']) ? esc_html($rankflow_seo_schema['data']['name']) : ''; ?></span>
								<button type="button" class="schema-toggle"
									title="<?php esc_attr_e('Toggle', 'rankflow-seo'); ?>">
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</button>
								<button type="button" class="schema-delete"
									title="<?php esc_attr_e('Delete', 'rankflow-seo'); ?>">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
							<div class="schema-content rankflow-seo-hidden">
								<!-- Display Rules Section -->
								<div class="schema-display-rules">
									<h4><?php esc_html_e('Display Rules', 'rankflow-seo'); ?></h4>

									<div class="display-rule-row">
										<label><?php esc_html_e('Show on:', 'rankflow-seo'); ?></label>
										<select class="display-mode-select"
											name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][display_mode]">
											<option value="all" <?php selected($rankflow_seo_schema['display_mode'] ?? 'all', 'all'); ?>><?php esc_html_e('All Pages', 'rankflow-seo'); ?></option>
											<option value="homepage" <?php selected($rankflow_seo_schema['display_mode'] ?? '', 'homepage'); ?>><?php esc_html_e('Homepage Only', 'rankflow-seo'); ?></option>
											<option value="post_types" <?php selected($rankflow_seo_schema['display_mode'] ?? '', 'post_types'); ?>>
												<?php esc_html_e('Specific Post Types', 'rankflow-seo'); ?>
											</option>
											<option value="include" <?php selected($rankflow_seo_schema['display_mode'] ?? '', 'include'); ?>>
												<?php esc_html_e('Only Specific Pages/Posts', 'rankflow-seo'); ?>
											</option>
											<option value="exclude" <?php selected($rankflow_seo_schema['display_mode'] ?? '', 'exclude'); ?>>
												<?php esc_html_e('All Except Specific Pages/Posts', 'rankflow-seo'); ?>
											</option>
										</select>
									</div>

									<!-- Post Types Selection -->
									<div class="display-rule-row post-types-row <?php echo esc_attr(($rankflow_seo_schema['display_mode'] ?? '') === 'post_types' ? '' : 'rankflow-seo-hidden'); ?>">
										<label><?php esc_html_e('Post Types:', 'rankflow-seo'); ?></label>
										<select class="post-types-select"
											name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][post_types][]"
											multiple>
											<?php
											$rankflow_seo_post_types = get_post_types(array('public' => true), 'objects');
											$rankflow_seo_selected_types = $rankflow_seo_schema['post_types'] ?? array();
											foreach ($rankflow_seo_post_types as $rankflow_seo_pt):
												if ('attachment' === $rankflow_seo_pt->name) {
													continue;
												}
												?>
												<option value="<?php echo esc_attr($rankflow_seo_pt->name); ?>" <?php selected(in_array($rankflow_seo_pt->name, $rankflow_seo_selected_types, true), true); ?>>
													<?php echo esc_html($rankflow_seo_pt->label); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Include Pages/Posts -->
									<div class="display-rule-row include-row <?php echo esc_attr(($rankflow_seo_schema['display_mode'] ?? '') === 'include' ? '' : 'rankflow-seo-hidden'); ?>">
										<label><?php esc_html_e('Include:', 'rankflow-seo'); ?></label>
										<select class="include-select"
											name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][include_ids][]"
											multiple>
											<?php
											$rankflow_seo_include_ids = $rankflow_seo_schema['include_ids'] ?? array();
											$rankflow_seo_all_content = get_posts(array(
												'post_type' => array('post', 'page'),
												'posts_per_page' => -1,
												'post_status' => 'publish',
												'orderby' => 'title',
												'order' => 'ASC',
											));
											foreach ($rankflow_seo_all_content as $rankflow_seo_content):
												?>
												<option value="<?php echo esc_attr($rankflow_seo_content->ID); ?>" <?php selected(in_array($rankflow_seo_content->ID, $rankflow_seo_include_ids, true), true); ?>>
													<?php echo esc_html($rankflow_seo_content->post_title); ?>
													(<?php echo esc_html(ucfirst($rankflow_seo_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Exclude Pages/Posts -->
									<div class="display-rule-row exclude-row <?php echo esc_attr(($rankflow_seo_schema['display_mode'] ?? '') === 'exclude' ? '' : 'rankflow-seo-hidden'); ?>">
										<label><?php esc_html_e('Exclude:', 'rankflow-seo'); ?></label>
										<select class="exclude-select"
											name="rankflow_seo_schemas[<?php echo esc_attr($rankflow_seo_index); ?>][exclude_ids][]"
											multiple>
											<?php
											$rankflow_seo_exclude_ids = $rankflow_seo_schema['exclude_ids'] ?? array();
											foreach ($rankflow_seo_all_content as $rankflow_seo_content):
												?>
												<option value="<?php echo esc_attr($rankflow_seo_content->ID); ?>" <?php selected(in_array($rankflow_seo_content->ID, $rankflow_seo_exclude_ids, true), true); ?>>
													<?php echo esc_html($rankflow_seo_content->post_title); ?>
													(<?php echo esc_html(ucfirst($rankflow_seo_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="schema-fields">
									<?php
									if (!empty($rankflow_seo_schema['type']) && isset($rankflow_seo_schema_types[$rankflow_seo_schema['type']])) {
										$rankflow_seo_fields = $rankflow_seo_schema_types[$rankflow_seo_schema['type']]['fields'];
										foreach ($rankflow_seo_fields as $rankflow_seo_field_key => $rankflow_seo_field_config) {
											$rankflow_seo_field_value = isset($rankflow_seo_schema['data'][$rankflow_seo_field_key]) ? $rankflow_seo_schema['data'][$rankflow_seo_field_key] : '';
											$rankflow_seo_field_name = 'rankflow_seo_schemas[' . esc_attr($rankflow_seo_index) . '][data][' . esc_attr($rankflow_seo_field_key) . ']';
											$rankflow_seo_field_id = 'schema_' . esc_attr($rankflow_seo_index) . '_' . esc_attr($rankflow_seo_field_key);

											echo '<div class="schema-field">';
											echo '<label for="' . esc_attr($rankflow_seo_field_id) . '">';
											echo esc_html($rankflow_seo_field_config['label']);
											if (!empty($rankflow_seo_field_config['required'])) {
												echo ' <span class="required">*</span>';
											}
											echo '</label>';

											switch ($rankflow_seo_field_config['type']) {
												case 'textarea':
													echo '<textarea id="' . esc_attr($rankflow_seo_field_id) . '" ';
													echo 'name="' . esc_attr($rankflow_seo_field_name) . '" ';
													echo 'rows="4" class="widefat" ';
													if (!empty($rankflow_seo_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($rankflow_seo_field_config['placeholder']) . '"';
													}
													echo '>' . esc_textarea($rankflow_seo_field_value) . '</textarea>';
													break;

												case 'select':
													echo '<select id="' . esc_attr($rankflow_seo_field_id) . '" ';
													echo 'name="' . esc_attr($rankflow_seo_field_name) . '" class="widefat">';
													echo '<option value="">' . esc_html__('— Select —', 'rankflow-seo') . '</option>';
													foreach ($rankflow_seo_field_config['options'] as $rankflow_seo_opt_value => $rankflow_seo_opt_label) {
														echo '<option value="' . esc_attr($rankflow_seo_opt_value) . '" ' . selected($rankflow_seo_field_value, $rankflow_seo_opt_value, false) . '>';
														echo esc_html($rankflow_seo_opt_label) . '</option>';
													}
													echo '</select>';
													break;

												case 'image':
													echo '<div class="image-field-wrapper">';
													echo '<input type="url" id="' . esc_attr($rankflow_seo_field_id) . '" ';
													echo 'name="' . esc_attr($rankflow_seo_field_name) . '" ';
													echo 'value="' . esc_attr($rankflow_seo_field_value) . '" class="widefat image-url-input">';
													echo '<button type="button" class="button select-image">' . esc_html__('Select Image', 'rankflow-seo') . '</button>';
													echo '</div>';
													break;

												case 'checkbox':
													echo '<label class="checkbox-label">';
													echo '<input type="checkbox" id="' . esc_attr($rankflow_seo_field_id) . '" ';
													echo 'name="' . esc_attr($rankflow_seo_field_name) . '" value="1" ';
													checked($rankflow_seo_field_value, '1');
													echo '>';
													if (!empty($rankflow_seo_field_config['description'])) {
														echo ' ' . esc_html($rankflow_seo_field_config['description']);
													}
													echo '</label>';
													break;

												case 'hours':
													echo '<div class="hours-repeater" data-name="' . esc_attr($rankflow_seo_field_name) . '">';
													if (!empty($rankflow_seo_field_value) && is_array($rankflow_seo_field_value)) {
														foreach ($rankflow_seo_field_value as $rankflow_seo_h_index => $rankflow_seo_hour) {
															$rankflow_seo_h_index_safe = intval($rankflow_seo_h_index);
															echo '<div class="hour-row">';
															echo '<select name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_h_index_safe) . '][days][]" multiple class="days-select">';
															$rankflow_seo_days = array(
																'Mo' => 'Monday',
																'Tu' => 'Tuesday',
																'We' => 'Wednesday',
																'Th' => 'Thursday',
																'Fr' => 'Friday',
																'Sa' => 'Saturday',
																'Su' => 'Sunday',
															);
															foreach ($rankflow_seo_days as $rankflow_seo_d_val => $rankflow_seo_d_label) {
																$rankflow_seo_is_selected = isset($rankflow_seo_hour['days']) && in_array($rankflow_seo_d_val, $rankflow_seo_hour['days'], true);
																echo '<option value="' . esc_attr($rankflow_seo_d_val) . '" ' . selected($rankflow_seo_is_selected, true, false) . '>' . esc_html($rankflow_seo_d_label) . '</option>';
															}
															echo '</select>';
															echo '<input type="time" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_h_index_safe) . '][open]" value="' . esc_attr($rankflow_seo_hour['open'] ?? '') . '" placeholder="Open">';
															echo '<input type="time" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_h_index_safe) . '][close]" value="' . esc_attr($rankflow_seo_hour['close'] ?? '') . '" placeholder="Close">';
															echo '<button type="button" class="button remove-hour">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-hour">' . esc_html__('Add Hours', 'rankflow-seo') . '</button>';
													echo '</div>';
													break;

												case 'faq_repeater':
													echo '<div class="faq-repeater" data-name="' . esc_attr($rankflow_seo_field_name) . '">';
													if (!empty($rankflow_seo_field_value) && is_array($rankflow_seo_field_value)) {
														foreach ($rankflow_seo_field_value as $rankflow_seo_f_index => $rankflow_seo_faq) {
															$rankflow_seo_f_index_safe = intval($rankflow_seo_f_index);
															echo '<div class="faq-row">';
															echo '<input type="text" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_f_index_safe) . '][question]" value="' . esc_attr($rankflow_seo_faq['question'] ?? '') . '" placeholder="' . esc_attr__('Question', 'rankflow-seo') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_f_index_safe) . '][answer]" placeholder="' . esc_attr__('Answer', 'rankflow-seo') . '" class="widefat" rows="2">' . esc_textarea($rankflow_seo_faq['answer'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-faq">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-faq">' . esc_html__('Add FAQ', 'rankflow-seo') . '</button>';
													echo '</div>';
													break;

												case 'steps_repeater':
													echo '<div class="steps-repeater" data-name="' . esc_attr($rankflow_seo_field_name) . '">';
													if (!empty($rankflow_seo_field_value) && is_array($rankflow_seo_field_value)) {
														foreach ($rankflow_seo_field_value as $rankflow_seo_s_index => $rankflow_seo_step) {
															$rankflow_seo_s_index_safe = intval($rankflow_seo_s_index);
															echo '<div class="step-row">';
															echo '<span class="step-number">' . esc_html($rankflow_seo_s_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_s_index_safe) . '][name]" value="' . esc_attr($rankflow_seo_step['name'] ?? '') . '" placeholder="' . esc_attr__('Step Name (optional)', 'rankflow-seo') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_s_index_safe) . '][text]" placeholder="' . esc_attr__('Step Instructions', 'rankflow-seo') . '" class="widefat" rows="2">' . esc_textarea($rankflow_seo_step['text'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-step">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-step">' . esc_html__('Add Step', 'rankflow-seo') . '</button>';
													echo '</div>';
													break;

												case 'breadcrumb_repeater':
													echo '<div class="breadcrumb-repeater" data-name="' . esc_attr($rankflow_seo_field_name) . '">';
													if (!empty($rankflow_seo_field_value) && is_array($rankflow_seo_field_value)) {
														foreach ($rankflow_seo_field_value as $rankflow_seo_b_index => $rankflow_seo_item) {
															$rankflow_seo_b_index_safe = intval($rankflow_seo_b_index);
															echo '<div class="breadcrumb-row">';
															echo '<span class="breadcrumb-number">' . esc_html($rankflow_seo_b_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_b_index_safe) . '][name]" value="' . esc_attr($rankflow_seo_item['name'] ?? '') . '" placeholder="' . esc_attr__('Name', 'rankflow-seo') . '">';
															echo '<input type="url" name="' . esc_attr($rankflow_seo_field_name) . '[' . esc_attr($rankflow_seo_b_index_safe) . '][url]" value="' . esc_attr($rankflow_seo_item['url'] ?? '') . '" placeholder="' . esc_attr__('URL', 'rankflow-seo') . '">';
															echo '<button type="button" class="button remove-breadcrumb">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-breadcrumb">' . esc_html__('Add Item', 'rankflow-seo') . '</button>';
													echo '</div>';
													break;

												default:
													echo '<input type="' . esc_attr($rankflow_seo_field_config['type']) . '" ';
													echo 'id="' . esc_attr($rankflow_seo_field_id) . '" ';
													echo 'name="' . esc_attr($rankflow_seo_field_name) . '" ';
													echo 'value="' . esc_attr($rankflow_seo_field_value) . '" ';
													echo 'class="widefat" ';
													if (!empty($rankflow_seo_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($rankflow_seo_field_config['placeholder']) . '"';
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

			<p class="no-schemas-message <?php echo esc_attr(!empty($rankflow_seo_schemas) ? 'rankflow-seo-hidden' : ''); ?>">
				<?php esc_html_e('No schemas added yet. Click "Add Schema" to create your first schema markup.', 'rankflow-seo'); ?>
			</p>
		</div>

		<!-- Submit Button -->
		<div class="rankflow-seo-card submit-card">
			<?php submit_button(esc_html__('Save Schemas', 'rankflow-seo'), 'primary', 'submit', false); ?>
		</div>
	</form>

	<!-- Schema Type Template (Hidden) -->
	<template id="schema-item-template">
		<div class="schema-item" data-index="{{INDEX}}">
			<div class="schema-header">
				<span class="schema-drag-handle dashicons dashicons-menu"></span>
				<label class="schema-enable">
					<input type="checkbox" name="rankflow_seo_schemas[{{INDEX}}][enabled]" value="1" checked>
				</label>
				<select class="schema-type-select" name="rankflow_seo_schemas[{{INDEX}}][type]">
					<option value=""><?php esc_html_e('— Select Schema Type —', 'rankflow-seo'); ?></option>
					<?php foreach ($rankflow_seo_schema_types as $rankflow_seo_type => $rankflow_seo_config): ?>
												<option value="<?php echo esc_attr($rankflow_seo_type); ?>"><?php echo esc_html($rankflow_seo_config['label']); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="schema-title"></span>
				<button type="button" class="schema-toggle" title="<?php esc_attr_e('Toggle', 'rankflow-seo'); ?>">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
				<button type="button" class="schema-delete" title="<?php esc_attr_e('Delete', 'rankflow-seo'); ?>">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
			<div class="schema-content">
				<!-- Display Rules Section -->
				<div class="schema-display-rules">
					<h4><?php esc_html_e('Display Rules', 'rankflow-seo'); ?></h4>
					
					<div class="display-rule-row">
						<label><?php esc_html_e('Show on:', 'rankflow-seo'); ?></label>
						<select class="display-mode-select" name="rankflow_seo_schemas[{{INDEX}}][display_mode]">
							<option value="all"><?php esc_html_e('All Pages', 'rankflow-seo'); ?></option>
							<option value="homepage"><?php esc_html_e('Homepage Only', 'rankflow-seo'); ?></option>
							<option value="post_types"><?php esc_html_e('Specific Post Types', 'rankflow-seo'); ?></option>
							<option value="include"><?php esc_html_e('Only Specific Pages/Posts', 'rankflow-seo'); ?></option>
							<option value="exclude"><?php esc_html_e('All Except Specific Pages/Posts', 'rankflow-seo'); ?></option>
						</select>
					</div>

					<!-- Post Types Selection -->
					<div class="display-rule-row post-types-row rankflow-seo-hidden">
						<label><?php esc_html_e('Post Types:', 'rankflow-seo'); ?></label>
						<select class="post-types-select" name="rankflow_seo_schemas[{{INDEX}}][post_types][]" multiple>
							<?php
							$rankflow_seo_post_types = get_post_types(array('public' => true), 'objects');
							foreach ($rankflow_seo_post_types as $rankflow_seo_pt):
								if ('attachment' === $rankflow_seo_pt->name) {
									continue;
								}
								?>
														<option value="<?php echo esc_attr($rankflow_seo_pt->name); ?>"><?php echo esc_html($rankflow_seo_pt->label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Include Pages/Posts -->
					<div class="display-rule-row include-row rankflow-seo-hidden">
						<label><?php esc_html_e('Include:', 'rankflow-seo'); ?></label>
						<select class="include-select" name="rankflow_seo_schemas[{{INDEX}}][include_ids][]" multiple>
							<?php
							$rankflow_seo_all_content = get_posts(array(
								'post_type' => array('post', 'page'),
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'orderby' => 'title',
								'order' => 'ASC',
							));
							foreach ($rankflow_seo_all_content as $rankflow_seo_content):
								?>
														<option value="<?php echo esc_attr($rankflow_seo_content->ID); ?>">
															<?php echo esc_html($rankflow_seo_content->post_title); ?> (<?php echo esc_html(ucfirst($rankflow_seo_content->post_type)); ?>)
														</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Exclude Pages/Posts -->
					<div class="display-rule-row exclude-row rankflow-seo-hidden">
						<label><?php esc_html_e('Exclude:', 'rankflow-seo'); ?></label>
						<select class="exclude-select" name="rankflow_seo_schemas[{{INDEX}}][exclude_ids][]" multiple>
							<?php foreach ($rankflow_seo_all_content as $rankflow_seo_content): ?>
														<option value="<?php echo esc_attr($rankflow_seo_content->ID); ?>">
															<?php echo esc_html($rankflow_seo_content->post_title); ?> (<?php echo esc_html(ucfirst($rankflow_seo_content->post_type)); ?>)
														</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="schema-fields">
					<p class="select-type-message"><?php esc_html_e('Please select a schema type to see available fields.', 'rankflow-seo'); ?></p>
				</div>
			</div>
		</div>
	</template>
</div>

