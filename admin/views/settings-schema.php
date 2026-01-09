<?php
/**
 * Schema Settings View
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 * @author     Strativ AB
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Get current settings.
$ai_seo_pro_schema_enabled = get_option('ai_seo_pro_schema_enabled', true);
$ai_seo_pro_schemas = get_option('ai_seo_pro_schemas', array());
$ai_seo_pro_schema_admin = new AI_SEO_Pro_Schema_Admin();
$ai_seo_pro_schema_types = $ai_seo_pro_schema_admin->get_schema_types();
?>

<div class="wrap ai-seo-pro-schema">
	<h1>
		<span class="dashicons dashicons-editor-code" style="margin-right: 10px;"></span>
		<?php esc_html_e('Schema Generator', 'ai-seo-pro'); ?>
	</h1>

	<p class="description" style="font-size: 14px; margin-bottom: 20px;">
		<?php esc_html_e('Generate structured data (schema markup) for your website to help search engines understand your content better and display rich results.', 'ai-seo-pro'); ?>
	</p>

	<?php settings_errors('ai_seo_pro_schema'); ?>

	<form method="post" action="options.php" id="ai-seo-pro-schema-form">
		<?php settings_fields('ai_seo_pro_schema'); ?>

		<!-- Enable Toggle -->
		<div class="ai-seo-pro-card">
			<h2><?php esc_html_e('Schema Settings', 'ai-seo-pro'); ?></h2>
			<label class="ai-seo-pro-toggle">
				<input type="checkbox" name="ai_seo_pro_schema_enabled" value="1" <?php checked($ai_seo_pro_schema_enabled); ?>>
				<span class="toggle-slider"></span>
				<span class="toggle-label"><?php esc_html_e('Enable Schema Markup Output', 'ai-seo-pro'); ?></span>
			</label>
			<p class="description">
				<?php esc_html_e('When enabled, schema markup will be added to your website\'s head section.', 'ai-seo-pro'); ?>
			</p>
		</div>

		<!-- Schema Repeater -->
		<div class="ai-seo-pro-card">
			<div class="card-header">
				<h2><?php esc_html_e('Schema Markup', 'ai-seo-pro'); ?></h2>
				<button type="button" id="add-schema" class="button button-primary">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e('Add Schema', 'ai-seo-pro'); ?>
				</button>
			</div>

			<div id="schema-repeater" class="schema-repeater">
				<?php if (!empty($ai_seo_pro_schemas)): ?>
					<?php foreach ($ai_seo_pro_schemas as $ai_seo_pro_index => $ai_seo_pro_schema): ?>
						<div class="schema-item" data-index="<?php echo esc_attr($ai_seo_pro_index); ?>">
							<div class="schema-header">
								<span class="schema-drag-handle dashicons dashicons-menu"></span>
								<label class="schema-enable">
									<input type="checkbox"
										name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][enabled]" value="1"
										<?php checked(!empty($ai_seo_pro_schema['enabled'])); ?>>
								</label>
								<select class="schema-type-select"
									name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][type]">
									<option value=""><?php esc_html_e('— Select Schema Type —', 'ai-seo-pro'); ?></option>
									<?php foreach ($ai_seo_pro_schema_types as $ai_seo_pro_type => $ai_seo_pro_config): ?>
										<option value="<?php echo esc_attr($ai_seo_pro_type); ?>" <?php selected($ai_seo_pro_schema['type'], $ai_seo_pro_type); ?>>
											<?php echo esc_html($ai_seo_pro_config['label']); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<span
									class="schema-title"><?php echo !empty($ai_seo_pro_schema['data']['name']) ? esc_html($ai_seo_pro_schema['data']['name']) : ''; ?></span>
								<button type="button" class="schema-toggle"
									title="<?php esc_attr_e('Toggle', 'ai-seo-pro'); ?>">
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</button>
								<button type="button" class="schema-delete"
									title="<?php esc_attr_e('Delete', 'ai-seo-pro'); ?>">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
							<div class="schema-content" style="display: none;">
								<!-- Display Rules Section -->
								<div class="schema-display-rules">
									<h4><?php esc_html_e('Display Rules', 'ai-seo-pro'); ?></h4>

									<div class="display-rule-row">
										<label><?php esc_html_e('Show on:', 'ai-seo-pro'); ?></label>
										<select class="display-mode-select"
											name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][display_mode]">
											<option value="all" <?php selected($ai_seo_pro_schema['display_mode'] ?? 'all', 'all'); ?>><?php esc_html_e('All Pages', 'ai-seo-pro'); ?></option>
											<option value="homepage" <?php selected($ai_seo_pro_schema['display_mode'] ?? '', 'homepage'); ?>><?php esc_html_e('Homepage Only', 'ai-seo-pro'); ?></option>
											<option value="post_types" <?php selected($ai_seo_pro_schema['display_mode'] ?? '', 'post_types'); ?>><?php esc_html_e('Specific Post Types', 'ai-seo-pro'); ?>
											</option>
											<option value="include" <?php selected($ai_seo_pro_schema['display_mode'] ?? '', 'include'); ?>>
												<?php esc_html_e('Only Specific Pages/Posts', 'ai-seo-pro'); ?>
											</option>
											<option value="exclude" <?php selected($ai_seo_pro_schema['display_mode'] ?? '', 'exclude'); ?>>
												<?php esc_html_e('All Except Specific Pages/Posts', 'ai-seo-pro'); ?>
											</option>
										</select>
									</div>

									<!-- Post Types Selection -->
									<div class="display-rule-row post-types-row"
										style="<?php echo ($ai_seo_pro_schema['display_mode'] ?? '') === 'post_types' ? '' : 'display:none;'; ?>">
										<label><?php esc_html_e('Post Types:', 'ai-seo-pro'); ?></label>
										<select class="post-types-select"
											name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][post_types][]"
											multiple>
											<?php
											$ai_seo_pro_post_types = get_post_types(array('public' => true), 'objects');
											$ai_seo_pro_selected_types = $ai_seo_pro_schema['post_types'] ?? array();
											foreach ($ai_seo_pro_post_types as $ai_seo_pro_pt):
												if ('attachment' === $ai_seo_pro_pt->name) {
													continue;
												}
												?>
												<option value="<?php echo esc_attr($ai_seo_pro_pt->name); ?>" <?php selected(in_array($ai_seo_pro_pt->name, $ai_seo_pro_selected_types, true), true); ?>>
													<?php echo esc_html($ai_seo_pro_pt->label); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Include Pages/Posts -->
									<div class="display-rule-row include-row"
										style="<?php echo ($ai_seo_pro_schema['display_mode'] ?? '') === 'include' ? '' : 'display:none;'; ?>">
										<label><?php esc_html_e('Include:', 'ai-seo-pro'); ?></label>
										<select class="include-select"
											name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][include_ids][]"
											multiple>
											<?php
											$ai_seo_pro_include_ids = $ai_seo_pro_schema['include_ids'] ?? array();
											$ai_seo_pro_all_content = get_posts(array(
												'post_type' => array('post', 'page'),
												'posts_per_page' => -1,
												'post_status' => 'publish',
												'orderby' => 'title',
												'order' => 'ASC',
											));
											foreach ($ai_seo_pro_all_content as $ai_seo_pro_content):
												?>
												<option value="<?php echo esc_attr($ai_seo_pro_content->ID); ?>" <?php selected(in_array($ai_seo_pro_content->ID, $ai_seo_pro_include_ids, true), true); ?>>
													<?php echo esc_html($ai_seo_pro_content->post_title); ?>
													(<?php echo esc_html(ucfirst($ai_seo_pro_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>

									<!-- Exclude Pages/Posts -->
									<div class="display-rule-row exclude-row"
										style="<?php echo ($ai_seo_pro_schema['display_mode'] ?? '') === 'exclude' ? '' : 'display:none;'; ?>">
										<label><?php esc_html_e('Exclude:', 'ai-seo-pro'); ?></label>
										<select class="exclude-select"
											name="ai_seo_pro_schemas[<?php echo esc_attr($ai_seo_pro_index); ?>][exclude_ids][]"
											multiple>
											<?php
											$ai_seo_pro_exclude_ids = $ai_seo_pro_schema['exclude_ids'] ?? array();
											foreach ($ai_seo_pro_all_content as $ai_seo_pro_content):
												?>
												<option value="<?php echo esc_attr($ai_seo_pro_content->ID); ?>" <?php selected(in_array($ai_seo_pro_content->ID, $ai_seo_pro_exclude_ids, true), true); ?>>
													<?php echo esc_html($ai_seo_pro_content->post_title); ?>
													(<?php echo esc_html(ucfirst($ai_seo_pro_content->post_type)); ?>)
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="schema-fields">
									<?php
									if (!empty($ai_seo_pro_schema['type']) && isset($ai_seo_pro_schema_types[$ai_seo_pro_schema['type']])) {
										$ai_seo_pro_fields = $ai_seo_pro_schema_types[$ai_seo_pro_schema['type']]['fields'];
										foreach ($ai_seo_pro_fields as $ai_seo_pro_field_key => $ai_seo_pro_field_config) {
											$ai_seo_pro_field_value = isset($ai_seo_pro_schema['data'][$ai_seo_pro_field_key]) ? $ai_seo_pro_schema['data'][$ai_seo_pro_field_key] : '';
											$ai_seo_pro_field_name = 'ai_seo_pro_schemas[' . esc_attr($ai_seo_pro_index) . '][data][' . esc_attr($ai_seo_pro_field_key) . ']';
											$ai_seo_pro_field_id = 'schema_' . esc_attr($ai_seo_pro_index) . '_' . esc_attr($ai_seo_pro_field_key);

											echo '<div class="schema-field">';
											echo '<label for="' . esc_attr($ai_seo_pro_field_id) . '">';
											echo esc_html($ai_seo_pro_field_config['label']);
											if (!empty($ai_seo_pro_field_config['required'])) {
												echo ' <span class="required">*</span>';
											}
											echo '</label>';

											switch ($ai_seo_pro_field_config['type']) {
												case 'textarea':
													echo '<textarea id="' . esc_attr($ai_seo_pro_field_id) . '" ';
													echo 'name="' . esc_attr($ai_seo_pro_field_name) . '" ';
													echo 'rows="4" class="widefat" ';
													if (!empty($ai_seo_pro_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($ai_seo_pro_field_config['placeholder']) . '"';
													}
													echo '>' . esc_textarea($ai_seo_pro_field_value) . '</textarea>';
													break;

												case 'select':
													echo '<select id="' . esc_attr($ai_seo_pro_field_id) . '" ';
													echo 'name="' . esc_attr($ai_seo_pro_field_name) . '" class="widefat">';
													echo '<option value="">' . esc_html__('— Select —', 'ai-seo-pro') . '</option>';
													foreach ($ai_seo_pro_field_config['options'] as $ai_seo_pro_opt_value => $ai_seo_pro_opt_label) {
														echo '<option value="' . esc_attr($ai_seo_pro_opt_value) . '" ' . selected($ai_seo_pro_field_value, $ai_seo_pro_opt_value, false) . '>';
														echo esc_html($ai_seo_pro_opt_label) . '</option>';
													}
													echo '</select>';
													break;

												case 'image':
													echo '<div class="image-field-wrapper">';
													echo '<input type="url" id="' . esc_attr($ai_seo_pro_field_id) . '" ';
													echo 'name="' . esc_attr($ai_seo_pro_field_name) . '" ';
													echo 'value="' . esc_attr($ai_seo_pro_field_value) . '" class="widefat image-url-input">';
													echo '<button type="button" class="button select-image">' . esc_html__('Select Image', 'ai-seo-pro') . '</button>';
													echo '</div>';
													break;

												case 'checkbox':
													echo '<label class="checkbox-label">';
													echo '<input type="checkbox" id="' . esc_attr($ai_seo_pro_field_id) . '" ';
													echo 'name="' . esc_attr($ai_seo_pro_field_name) . '" value="1" ';
													checked($ai_seo_pro_field_value, '1');
													echo '>';
													if (!empty($ai_seo_pro_field_config['description'])) {
														echo ' ' . esc_html($ai_seo_pro_field_config['description']);
													}
													echo '</label>';
													break;

												case 'hours':
													echo '<div class="hours-repeater" data-name="' . esc_attr($ai_seo_pro_field_name) . '">';
													if (!empty($ai_seo_pro_field_value) && is_array($ai_seo_pro_field_value)) {
														foreach ($ai_seo_pro_field_value as $ai_seo_pro_h_index => $ai_seo_pro_hour) {
															$ai_seo_pro_h_index_safe = intval($ai_seo_pro_h_index);
															echo '<div class="hour-row">';
															echo '<select name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_h_index_safe) . '][days][]" multiple class="days-select">';
															$ai_seo_pro_days = array(
																'Mo' => 'Monday',
																'Tu' => 'Tuesday',
																'We' => 'Wednesday',
																'Th' => 'Thursday',
																'Fr' => 'Friday',
																'Sa' => 'Saturday',
																'Su' => 'Sunday',
															);
															foreach ($ai_seo_pro_days as $ai_seo_pro_d_val => $ai_seo_pro_d_label) {
																$ai_seo_pro_is_selected = isset($ai_seo_pro_hour['days']) && in_array($ai_seo_pro_d_val, $ai_seo_pro_hour['days'], true);
																echo '<option value="' . esc_attr($ai_seo_pro_d_val) . '" ' . selected($ai_seo_pro_is_selected, true, false) . '>' . esc_html($ai_seo_pro_d_label) . '</option>';
															}
															echo '</select>';
															echo '<input type="time" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_h_index_safe) . '][open]" value="' . esc_attr($ai_seo_pro_hour['open'] ?? '') . '" placeholder="Open">';
															echo '<input type="time" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_h_index_safe) . '][close]" value="' . esc_attr($ai_seo_pro_hour['close'] ?? '') . '" placeholder="Close">';
															echo '<button type="button" class="button remove-hour">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-hour">' . esc_html__('Add Hours', 'ai-seo-pro') . '</button>';
													echo '</div>';
													break;

												case 'faq_repeater':
													echo '<div class="faq-repeater" data-name="' . esc_attr($ai_seo_pro_field_name) . '">';
													if (!empty($ai_seo_pro_field_value) && is_array($ai_seo_pro_field_value)) {
														foreach ($ai_seo_pro_field_value as $ai_seo_pro_f_index => $ai_seo_pro_faq) {
															$ai_seo_pro_f_index_safe = intval($ai_seo_pro_f_index);
															echo '<div class="faq-row">';
															echo '<input type="text" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_f_index_safe) . '][question]" value="' . esc_attr($ai_seo_pro_faq['question'] ?? '') . '" placeholder="' . esc_attr__('Question', 'ai-seo-pro') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_f_index_safe) . '][answer]" placeholder="' . esc_attr__('Answer', 'ai-seo-pro') . '" class="widefat" rows="2">' . esc_textarea($ai_seo_pro_faq['answer'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-faq">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-faq">' . esc_html__('Add FAQ', 'ai-seo-pro') . '</button>';
													echo '</div>';
													break;

												case 'steps_repeater':
													echo '<div class="steps-repeater" data-name="' . esc_attr($ai_seo_pro_field_name) . '">';
													if (!empty($ai_seo_pro_field_value) && is_array($ai_seo_pro_field_value)) {
														foreach ($ai_seo_pro_field_value as $ai_seo_pro_s_index => $ai_seo_pro_step) {
															$ai_seo_pro_s_index_safe = intval($ai_seo_pro_s_index);
															echo '<div class="step-row">';
															echo '<span class="step-number">' . esc_html($ai_seo_pro_s_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_s_index_safe) . '][name]" value="' . esc_attr($ai_seo_pro_step['name'] ?? '') . '" placeholder="' . esc_attr__('Step Name (optional)', 'ai-seo-pro') . '" class="widefat">';
															echo '<textarea name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_s_index_safe) . '][text]" placeholder="' . esc_attr__('Step Instructions', 'ai-seo-pro') . '" class="widefat" rows="2">' . esc_textarea($ai_seo_pro_step['text'] ?? '') . '</textarea>';
															echo '<button type="button" class="button remove-step">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-step">' . esc_html__('Add Step', 'ai-seo-pro') . '</button>';
													echo '</div>';
													break;

												case 'breadcrumb_repeater':
													echo '<div class="breadcrumb-repeater" data-name="' . esc_attr($ai_seo_pro_field_name) . '">';
													if (!empty($ai_seo_pro_field_value) && is_array($ai_seo_pro_field_value)) {
														foreach ($ai_seo_pro_field_value as $ai_seo_pro_b_index => $ai_seo_pro_item) {
															$ai_seo_pro_b_index_safe = intval($ai_seo_pro_b_index);
															echo '<div class="breadcrumb-row">';
															echo '<span class="breadcrumb-number">' . esc_html($ai_seo_pro_b_index_safe + 1) . '</span>';
															echo '<input type="text" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_b_index_safe) . '][name]" value="' . esc_attr($ai_seo_pro_item['name'] ?? '') . '" placeholder="' . esc_attr__('Name', 'ai-seo-pro') . '">';
															echo '<input type="url" name="' . esc_attr($ai_seo_pro_field_name) . '[' . esc_attr($ai_seo_pro_b_index_safe) . '][url]" value="' . esc_attr($ai_seo_pro_item['url'] ?? '') . '" placeholder="' . esc_attr__('URL', 'ai-seo-pro') . '">';
															echo '<button type="button" class="button remove-breadcrumb">×</button>';
															echo '</div>';
														}
													}
													echo '<button type="button" class="button add-breadcrumb">' . esc_html__('Add Item', 'ai-seo-pro') . '</button>';
													echo '</div>';
													break;

												default:
													echo '<input type="' . esc_attr($ai_seo_pro_field_config['type']) . '" ';
													echo 'id="' . esc_attr($ai_seo_pro_field_id) . '" ';
													echo 'name="' . esc_attr($ai_seo_pro_field_name) . '" ';
													echo 'value="' . esc_attr($ai_seo_pro_field_value) . '" ';
													echo 'class="widefat" ';
													if (!empty($ai_seo_pro_field_config['placeholder'])) {
														echo 'placeholder="' . esc_attr($ai_seo_pro_field_config['placeholder']) . '"';
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

			<p class="no-schemas-message" <?php echo !empty($ai_seo_pro_schemas) ? 'style="display:none;"' : ''; ?>>
				<?php esc_html_e('No schemas added yet. Click "Add Schema" to create your first schema markup.', 'ai-seo-pro'); ?>
			</p>
		</div>

		<!-- Submit Button -->
		<div class="ai-seo-pro-card submit-card">
			<?php submit_button(esc_html__('Save Schemas', 'ai-seo-pro'), 'primary', 'submit', false); ?>
		</div>
	</form>

	<!-- Schema Type Template (Hidden) -->
	<script type="text/template" id="schema-item-template">
		<div class="schema-item" data-index="{{INDEX}}">
			<div class="schema-header">
				<span class="schema-drag-handle dashicons dashicons-menu"></span>
				<label class="schema-enable">
					<input type="checkbox" name="ai_seo_pro_schemas[{{INDEX}}][enabled]" value="1" checked>
				</label>
				<select class="schema-type-select" name="ai_seo_pro_schemas[{{INDEX}}][type]">
					<option value=""><?php esc_html_e('— Select Schema Type —', 'ai-seo-pro'); ?></option>
					<?php foreach ($ai_seo_pro_schema_types as $ai_seo_pro_type => $ai_seo_pro_config): ?>
									<option value="<?php echo esc_attr($ai_seo_pro_type); ?>"><?php echo esc_html($ai_seo_pro_config['label']); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="schema-title"></span>
				<button type="button" class="schema-toggle" title="<?php esc_attr_e('Toggle', 'ai-seo-pro'); ?>">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
				<button type="button" class="schema-delete" title="<?php esc_attr_e('Delete', 'ai-seo-pro'); ?>">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
			<div class="schema-content">
				<!-- Display Rules Section -->
				<div class="schema-display-rules">
					<h4><?php esc_html_e('Display Rules', 'ai-seo-pro'); ?></h4>
					
					<div class="display-rule-row">
						<label><?php esc_html_e('Show on:', 'ai-seo-pro'); ?></label>
						<select class="display-mode-select" name="ai_seo_pro_schemas[{{INDEX}}][display_mode]">
							<option value="all"><?php esc_html_e('All Pages', 'ai-seo-pro'); ?></option>
							<option value="homepage"><?php esc_html_e('Homepage Only', 'ai-seo-pro'); ?></option>
							<option value="post_types"><?php esc_html_e('Specific Post Types', 'ai-seo-pro'); ?></option>
							<option value="include"><?php esc_html_e('Only Specific Pages/Posts', 'ai-seo-pro'); ?></option>
							<option value="exclude"><?php esc_html_e('All Except Specific Pages/Posts', 'ai-seo-pro'); ?></option>
						</select>
					</div>

					<!-- Post Types Selection -->
					<div class="display-rule-row post-types-row" style="display:none;">
						<label><?php esc_html_e('Post Types:', 'ai-seo-pro'); ?></label>
						<select class="post-types-select" name="ai_seo_pro_schemas[{{INDEX}}][post_types][]" multiple>
							<?php
							$ai_seo_pro_post_types = get_post_types(array('public' => true), 'objects');
							foreach ($ai_seo_pro_post_types as $ai_seo_pro_pt):
								if ('attachment' === $ai_seo_pro_pt->name) {
									continue;
								}
								?>
											<option value="<?php echo esc_attr($ai_seo_pro_pt->name); ?>"><?php echo esc_html($ai_seo_pro_pt->label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Include Pages/Posts -->
					<div class="display-rule-row include-row" style="display:none;">
						<label><?php esc_html_e('Include:', 'ai-seo-pro'); ?></label>
						<select class="include-select" name="ai_seo_pro_schemas[{{INDEX}}][include_ids][]" multiple>
							<?php
							$ai_seo_pro_all_content = get_posts(array(
								'post_type' => array('post', 'page'),
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'orderby' => 'title',
								'order' => 'ASC',
							));
							foreach ($ai_seo_pro_all_content as $ai_seo_pro_content):
								?>
											<option value="<?php echo esc_attr($ai_seo_pro_content->ID); ?>">
												<?php echo esc_html($ai_seo_pro_content->post_title); ?> (<?php echo esc_html(ucfirst($ai_seo_pro_content->post_type)); ?>)
											</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Exclude Pages/Posts -->
					<div class="display-rule-row exclude-row" style="display:none;">
						<label><?php esc_html_e('Exclude:', 'ai-seo-pro'); ?></label>
						<select class="exclude-select" name="ai_seo_pro_schemas[{{INDEX}}][exclude_ids][]" multiple>
							<?php foreach ($ai_seo_pro_all_content as $ai_seo_pro_content): ?>
											<option value="<?php echo esc_attr($ai_seo_pro_content->ID); ?>">
												<?php echo esc_html($ai_seo_pro_content->post_title); ?> (<?php echo esc_html(ucfirst($ai_seo_pro_content->post_type)); ?>)
											</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="schema-fields">
					<p class="select-type-message"><?php esc_html_e('Please select a schema type to see available fields.', 'ai-seo-pro'); ?></p>
				</div>
			</div>
		</div>
	</script>
</div>

<style>
	.ai-seo-pro-schema {
		max-width: 1200px;
	}

	.ai-seo-pro-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 8px;
		padding: 20px;
		margin-bottom: 20px;
	}

	.ai-seo-pro-card h2 {
		margin: 0 0 15px 0;
		padding: 0 0 10px 0;
		border-bottom: 1px solid #eee;
		font-size: 16px;
	}

	.card-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 15px;
		padding-bottom: 10px;
		border-bottom: 1px solid #eee;
	}

	.card-header h2 {
		margin: 0 !important;
		padding: 0 !important;
		border: none !important;
	}

	/* Toggle Switch */
	.ai-seo-pro-toggle {
		display: flex;
		align-items: center;
		gap: 12px;
		cursor: pointer;
		margin-bottom: 10px;
	}

	.ai-seo-pro-toggle input {
		display: none;
	}

	.toggle-slider {
		width: 50px;
		height: 26px;
		background: #ccc;
		border-radius: 13px;
		position: relative;
		transition: background 0.3s;
	}

	.toggle-slider::after {
		content: '';
		position: absolute;
		width: 22px;
		height: 22px;
		background: #fff;
		border-radius: 50%;
		top: 2px;
		left: 2px;
		transition: transform 0.3s;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
	}

	.ai-seo-pro-toggle input:checked+.toggle-slider {
		background: #2271b1;
	}

	.ai-seo-pro-toggle input:checked+.toggle-slider::after {
		transform: translateX(24px);
	}

	.toggle-label {
		font-weight: 500;
	}

	/* Schema Repeater */
	.schema-repeater {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	.schema-item {
		border: 1px solid #ddd;
		border-radius: 6px;
		background: #fafafa;
	}

	.schema-header {
		display: flex;
		align-items: center;
		gap: 10px;
		padding: 12px 15px;
		background: #f5f5f5;
		border-radius: 6px 6px 0 0;
		cursor: pointer;
	}

	.schema-item.collapsed .schema-header {
		border-radius: 6px;
	}

	.schema-drag-handle {
		cursor: grab;
		color: #999;
	}

	.schema-enable input {
		width: 18px;
		height: 18px;
	}

	.schema-type-select {
		min-width: 200px;
	}

	.schema-title {
		flex: 1;
		font-weight: 500;
		color: #666;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.schema-toggle,
	.schema-delete {
		background: none;
		border: none;
		cursor: pointer;
		padding: 5px;
		color: #666;
		transition: color 0.2s;
	}

	.schema-toggle:hover {
		color: #2271b1;
	}

	.schema-delete:hover {
		color: #dc3232;
	}

	.schema-content {
		padding: 20px;
		background: #fff;
		border-radius: 0 0 6px 6px;
	}

	.schema-fields {
		display: grid;
		gap: 15px;
	}

	.schema-field {
		display: flex;
		flex-direction: column;
		gap: 5px;
	}

	.schema-field label {
		font-weight: 500;
		font-size: 13px;
		color: #1d2327;
	}

	.schema-field .required {
		color: #dc3232;
	}

	.schema-field input[type="text"],
	.schema-field input[type="url"],
	.schema-field input[type="email"],
	.schema-field input[type="tel"],
	.schema-field input[type="number"],
	.schema-field input[type="date"],
	.schema-field input[type="datetime-local"],
	.schema-field textarea,
	.schema-field select {
		padding: 8px 12px;
		border: 1px solid #ddd;
		border-radius: 4px;
		font-size: 14px;
	}

	.schema-field textarea {
		resize: vertical;
	}

	.select-type-message {
		color: #666;
		font-style: italic;
		padding: 20px;
		text-align: center;
		background: #f9f9f9;
		border-radius: 4px;
	}

	/* Image field */
	.image-field-wrapper {
		display: flex;
		gap: 10px;
	}

	.image-field-wrapper input {
		flex: 1;
	}

	/* Hours, FAQ, Steps repeaters */
	.hours-repeater,
	.faq-repeater,
	.steps-repeater,
	.breadcrumb-repeater {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.hour-row,
	.faq-row,
	.step-row,
	.breadcrumb-row {
		display: flex;
		gap: 10px;
		align-items: flex-start;
		padding: 10px;
		background: #f9f9f9;
		border-radius: 4px;
	}

	.hour-row select,
	.hour-row input {
		flex: 1;
	}

	.faq-row input,
	.faq-row textarea {
		flex: 1;
	}

	.step-row .step-number,
	.breadcrumb-row .breadcrumb-number {
		width: 30px;
		height: 30px;
		background: #2271b1;
		color: #fff;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: bold;
		flex-shrink: 0;
	}

	.step-row input,
	.step-row textarea,
	.breadcrumb-row input {
		flex: 1;
	}

	.days-select {
		min-height: 100px;
	}

	/* Checkbox label */
	.checkbox-label {
		display: flex;
		align-items: center;
		gap: 8px;
		font-weight: normal !important;
	}

	/* No schemas message */
	.no-schemas-message {
		padding: 30px;
		text-align: center;
		color: #666;
		background: #f9f9f9;
		border-radius: 4px;
		font-style: italic;
	}

	/* Submit card */
	.submit-card {
		display: flex;
		gap: 10px;
	}

	/* Sortable placeholder */
	.schema-item.ui-sortable-placeholder {
		visibility: visible !important;
		background: #f0f6fc;
		border: 2px dashed #2271b1;
	}

	/* Display Rules Section */
	.schema-display-rules {
		background: #f8f9fa;
		border: 1px solid #e2e4e7;
		border-radius: 6px;
		padding: 15px;
		margin-bottom: 20px;
	}

	.schema-display-rules h4 {
		margin: 0 0 15px 0;
		padding: 0 0 10px 0;
		border-bottom: 1px solid #e2e4e7;
		font-size: 14px;
		color: #1d2327;
	}

	.display-rule-row {
		display: flex;
		align-items: center;
		gap: 15px;
		margin-bottom: 12px;
	}

	.display-rule-row:last-child {
		margin-bottom: 0;
	}

	.display-rule-row>label {
		min-width: 100px;
		font-weight: 500;
		font-size: 13px;
	}

	.display-rule-row select {
		flex: 1;
		max-width: 400px;
	}

	.display-rule-row .select2-container {
		flex: 1;
		max-width: 400px;
	}

	/* Select2 Styling */
	.select2-container--default .select2-selection--multiple {
		border: 1px solid #ddd;
		border-radius: 4px;
		min-height: 36px;
	}

	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background: #2271b1;
		border: none;
		color: #fff;
		border-radius: 3px;
		padding: 2px 8px;
	}

	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		color: #fff;
		margin-right: 5px;
	}

	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
		color: #fff;
		background: transparent;
	}

	/* Responsive */
	@media (max-width: 782px) {
		.schema-header {
			flex-wrap: wrap;
		}

		.schema-type-select {
			min-width: 150px;
		}

		.hour-row,
		.step-row,
		.breadcrumb-row {
			flex-wrap: wrap;
		}
	}
</style>