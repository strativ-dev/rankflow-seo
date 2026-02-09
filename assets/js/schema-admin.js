/**
 * RankFlow SEO Schema Admin JavaScript
 *
 * Handles dynamic schema form functionality
 */

(function ($) {
    'use strict';

    var SchemaAdmin = {
        schemaIndex: 0,
        schemaTypes: {},

        init: function () {
            this.schemaTypes = rankflowSeoSchema.schemaTypes || {};
            this.schemaIndex = $('.schema-item').length;

            this.bindEvents();
            this.initSortable();
            this.initSelect2();
        },

        bindEvents: function () {
            var self = this;

            // Add new schema
            $('#add-schema').on('click', function () {
                self.addSchema();
            });

            // Delete schema
            $(document).on('click', '.schema-delete', function (e) {
                e.stopPropagation();
                if (confirm(rankflowSeoSchema.confirmDelete)) {
                    self.deleteSchema($(this).closest('.schema-item'));
                }
            });

            // Toggle schema content
            $(document).on('click', '.schema-toggle', function (e) {
                e.stopPropagation();
                self.toggleSchema($(this).closest('.schema-item'));
            });

            // Schema header click (expand/collapse)
            $(document).on('click', '.schema-header', function (e) {
                if (!$(e.target).is('select, input, button, .dashicons')) {
                    self.toggleSchema($(this).closest('.schema-item'));
                }
            });

            // Schema type change
            $(document).on('change', '.schema-type-select', function () {
                self.onTypeChange($(this));
            });

            // Display mode change
            $(document).on('change', '.display-mode-select', function () {
                self.onDisplayModeChange($(this));
            });

            // Image selection
            $(document).on('click', '.select-image', function (e) {
                e.preventDefault();
                self.selectImage($(this));
            });

            // Add hours
            $(document).on('click', '.add-hour', function () {
                self.addHourRow($(this).closest('.hours-repeater'));
            });

            // Remove hours
            $(document).on('click', '.remove-hour', function () {
                $(this).closest('.hour-row').remove();
            });

            // Add FAQ
            $(document).on('click', '.add-faq', function () {
                self.addFaqRow($(this).closest('.faq-repeater'));
            });

            // Remove FAQ
            $(document).on('click', '.remove-faq', function () {
                $(this).closest('.faq-row').remove();
            });

            // Add step
            $(document).on('click', '.add-step', function () {
                self.addStepRow($(this).closest('.steps-repeater'));
            });

            // Remove step
            $(document).on('click', '.remove-step', function () {
                $(this).closest('.step-row').remove();
                self.updateStepNumbers($(this).closest('.steps-repeater'));
            });

            // Add breadcrumb
            $(document).on('click', '.add-breadcrumb', function () {
                self.addBreadcrumbRow($(this).closest('.breadcrumb-repeater'));
            });

            // Remove breadcrumb
            $(document).on('click', '.remove-breadcrumb', function () {
                $(this).closest('.breadcrumb-row').remove();
                self.updateBreadcrumbNumbers($(this).closest('.breadcrumb-repeater'));
            });

            // Add offer catalog category
            $(document).on('click', '.add-category-btn', function () {
                self.addCategoryRow($(this).closest('.offer-catalog-repeater'));
            });

            // Remove offer catalog category
            $(document).on('click', '.remove-category-btn', function () {
                $(this).closest('.category-item').remove();
            });

            // Add service to category
            $(document).on('click', '.add-service-btn', function () {
                self.addServiceRow($(this).closest('.category-item'));
            });

            // Remove service from category
            $(document).on('click', '.remove-service-btn', function () {
                $(this).closest('.service-item').remove();
            });

            // Update title when name field changes
            $(document).on('input', '.schema-fields input[name*="[name]"]:first, .schema-fields input[name*="[headline]"]:first', function () {
                var $item = $(this).closest('.schema-item');
                $item.find('.schema-title').text($(this).val());
            });
        },

        initSortable: function () {
            $('#schema-repeater').sortable({
                handle: '.schema-drag-handle',
                placeholder: 'schema-item ui-sortable-placeholder',
                update: function () {
                    // Re-index after sorting
                    SchemaAdmin.reindexSchemas();
                }
            });
        },

        initSelect2: function () {
            // Initialize Select2 on existing selects
            $('.include-select, .exclude-select, .post-types-select').each(function () {
                $(this).select2({
                    placeholder: 'Select...',
                    allowClear: true,
                    width: '100%'
                });
            });
        },

        addSchema: function () {
            var template = $('#schema-item-template').html();
            template = template.replace(/\{\{INDEX\}\}/g, this.schemaIndex);

            var $newItem = $(template);
            $('#schema-repeater').append($newItem);

            // Initialize Select2 on new item
            $newItem.find('.include-select, .exclude-select, .post-types-select').select2({
                placeholder: 'Select...',
                allowClear: true,
                width: '100%'
            });

            this.schemaIndex++;
            $('.no-schemas-message').hide();

            // Expand the new item
            $newItem.find('.schema-content').show();
        },

        deleteSchema: function ($item) {
            // Destroy Select2 before removing
            $item.find('.include-select, .exclude-select, .post-types-select').select2('destroy');

            $item.slideUp(300, function () {
                $(this).remove();
                if ($('.schema-item').length === 0) {
                    $('.no-schemas-message').show();
                }
            });
        },

        toggleSchema: function ($item) {
            var $content = $item.find('.schema-content');
            var $toggle = $item.find('.schema-toggle .dashicons');

            if ($content.is(':visible')) {
                $content.slideUp(200);
                $toggle.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                $item.addClass('collapsed');
            } else {
                $content.slideDown(200);
                $toggle.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                $item.removeClass('collapsed');
            }
        },

        onDisplayModeChange: function ($select) {
            var mode = $select.val();
            var $item = $select.closest('.schema-item');

            // Hide all conditional rows
            $item.find('.post-types-row, .include-row, .exclude-row').hide();

            // Show relevant row based on mode
            switch (mode) {
                case 'post_types':
                    $item.find('.post-types-row').show();
                    break;
                case 'include':
                    $item.find('.include-row').show();
                    break;
                case 'exclude':
                    $item.find('.exclude-row').show();
                    break;
            }
        },

        onTypeChange: function ($select) {
            var type = $select.val();
            var $item = $select.closest('.schema-item');
            var index = $item.data('index');
            var $fieldsContainer = $item.find('.schema-fields');

            if (!type) {
                $fieldsContainer.html('<p class="select-type-message">Please select a schema type to see available fields.</p>');
                return;
            }

            var typeConfig = this.schemaTypes[type];
            if (!typeConfig) {
                return;
            }

            var fieldsHtml = this.generateFieldsHtml(type, typeConfig.fields, index);
            $fieldsContainer.html(fieldsHtml);

            // Update title
            $item.find('.schema-title').text(typeConfig.label);
        },

        generateFieldsHtml: function (type, fields, index) {
            var html = '';

            for (var fieldKey in fields) {
                if (!fields.hasOwnProperty(fieldKey)) continue;

                var field = fields[fieldKey];
                var fieldName = 'rankflow_seo_schemas[' + index + '][data][' + fieldKey + ']';
                var fieldId = 'schema_' + index + '_' + fieldKey;

                html += '<div class="schema-field">';
                html += '<label for="' + fieldId + '">' + field.label;
                if (field.required) {
                    html += ' <span class="required">*</span>';
                }
                html += '</label>';

                html += this.generateFieldInput(field, fieldName, fieldId);
                html += '</div>';
            }

            return html;
        },

        generateFieldInput: function (field, name, id) {
            var html = '';
            var placeholder = field.placeholder || '';

            switch (field.type) {
                case 'textarea':
                    html = '<textarea id="' + id + '" name="' + name + '" rows="4" class="widefat" placeholder="' + placeholder + '"></textarea>';
                    break;

                case 'select':
                    html = '<select id="' + id + '" name="' + name + '" class="widefat">';
                    html += '<option value="">— Select —</option>';
                    for (var optValue in field.options) {
                        html += '<option value="' + optValue + '">' + field.options[optValue] + '</option>';
                    }
                    html += '</select>';
                    break;

                case 'image':
                    html = '<div class="image-field-wrapper">';
                    html += '<input type="url" id="' + id + '" name="' + name + '" class="widefat image-url-input" placeholder="' + placeholder + '">';
                    html += '<button type="button" class="button select-image">' + rankflowSeoSchema.selectImage + '</button>';
                    html += '</div>';
                    break;

                case 'checkbox':
                    html = '<label class="checkbox-label">';
                    html += '<input type="checkbox" id="' + id + '" name="' + name + '" value="1">';
                    if (field.description) {
                        html += ' ' + field.description;
                    }
                    html += '</label>';
                    break;

                case 'hours':
                    html = '<div class="hours-repeater" data-name="' + name + '">';
                    html += '<button type="button" class="button add-hour">Add Hours</button>';
                    html += '</div>';
                    break;

                case 'faq_repeater':
                    html = '<div class="faq-repeater" data-name="' + name + '">';
                    html += '<button type="button" class="button add-faq">Add FAQ</button>';
                    html += '</div>';
                    break;

                case 'steps_repeater':
                    html = '<div class="steps-repeater" data-name="' + name + '">';
                    html += '<button type="button" class="button add-step">Add Step</button>';
                    html += '</div>';
                    break;

                case 'breadcrumb_repeater':
                    html = '<div class="breadcrumb-repeater" data-name="' + name + '">';
                    html += '<button type="button" class="button add-breadcrumb">Add Item</button>';
                    html += '</div>';
                    break;

                case 'offer_catalog_repeater':
                    html = this.generateOfferCatalogRepeater(name, id);
                    break;

                default:
                    html = '<input type="' + field.type + '" id="' + id + '" name="' + name + '" class="widefat" placeholder="' + placeholder + '">';
            }

            return html;
        },

        selectImage: function ($button) {
            var $input = $button.siblings('.image-url-input');

            var mediaUploader = wp.media({
                title: rankflowSeoSchema.selectImage,
                button: {
                    text: rankflowSeoSchema.useImage
                },
                multiple: false
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $input.val(attachment.url);
            });

            mediaUploader.open();
        },

        addHourRow: function ($container) {
            var name = $container.data('name');
            var index = $container.find('.hour-row').length;

            var html = '<div class="hour-row">';
            html += '<select name="' + name + '[' + index + '][days][]" multiple class="days-select">';
            var days = { 'Mo': 'Monday', 'Tu': 'Tuesday', 'We': 'Wednesday', 'Th': 'Thursday', 'Fr': 'Friday', 'Sa': 'Saturday', 'Su': 'Sunday' };
            for (var d in days) {
                html += '<option value="' + d + '">' + days[d] + '</option>';
            }
            html += '</select>';
            html += '<input type="time" name="' + name + '[' + index + '][open]" placeholder="Open">';
            html += '<input type="time" name="' + name + '[' + index + '][close]" placeholder="Close">';
            html += '<button type="button" class="button remove-hour">×</button>';
            html += '</div>';

            $container.find('.add-hour').before(html);
        },

        addFaqRow: function ($container) {
            var name = $container.data('name');
            var index = $container.find('.faq-row').length;

            var html = '<div class="faq-row">';
            html += '<input type="text" name="' + name + '[' + index + '][question]" placeholder="Question" class="widefat">';
            html += '<textarea name="' + name + '[' + index + '][answer]" placeholder="Answer" class="widefat" rows="2"></textarea>';
            html += '<button type="button" class="button remove-faq">×</button>';
            html += '</div>';

            $container.find('.add-faq').before(html);
        },

        addStepRow: function ($container) {
            var name = $container.data('name');
            var index = $container.find('.step-row').length;

            var html = '<div class="step-row">';
            html += '<span class="step-number">' + (index + 1) + '</span>';
            html += '<input type="text" name="' + name + '[' + index + '][name]" placeholder="Step Name (optional)" class="widefat">';
            html += '<textarea name="' + name + '[' + index + '][text]" placeholder="Step Instructions" class="widefat" rows="2"></textarea>';
            html += '<button type="button" class="button remove-step">×</button>';
            html += '</div>';

            $container.find('.add-step').before(html);
        },

        updateStepNumbers: function ($container) {
            $container.find('.step-row').each(function (index) {
                $(this).find('.step-number').text(index + 1);
            });
        },

        addBreadcrumbRow: function ($container) {
            var name = $container.data('name');
            var index = $container.find('.breadcrumb-row').length;

            var html = '<div class="breadcrumb-row">';
            html += '<span class="breadcrumb-number">' + (index + 1) + '</span>';
            html += '<input type="text" name="' + name + '[' + index + '][name]" placeholder="Name">';
            html += '<input type="url" name="' + name + '[' + index + '][url]" placeholder="URL">';
            html += '<button type="button" class="button remove-breadcrumb">×</button>';
            html += '</div>';

            $container.find('.add-breadcrumb').before(html);
        },

        updateBreadcrumbNumbers: function ($container) {
            $container.find('.breadcrumb-row').each(function (index) {
                $(this).find('.breadcrumb-number').text(index + 1);
            });
        },

        generateOfferCatalogRepeater: function (name, id) {
            var html = '<div class="offer-catalog-repeater" data-name="' + name + '">';
            html += '<div class="catalog-name-field">';
            html += '<label>Catalog Name</label>';
            html += '<input type="text" name="' + name + '[name]" class="widefat catalog-name-input" placeholder="e.g., Our Services">';
            html += '</div>';
            html += '<div class="catalog-categories">';
            html += '<h4>Service Categories</h4>';
            html += '<div class="categories-list"></div>';
            html += '<button type="button" class="button add-category-btn"><span class="dashicons dashicons-plus-alt2"></span> Add Category</button>';
            html += '</div>';
            html += '</div>';
            return html;
        },

        addCategoryRow: function ($container) {
            var name = $container.data('name');
            var $categoriesList = $container.find('.categories-list');
            var catIndex = $categoriesList.find('.category-item').length;

            var html = '<div class="category-item" data-category-index="' + catIndex + '">';
            html += '<div class="category-header">';
            html += '<input type="text" name="' + name + '[categories][' + catIndex + '][name]" class="widefat category-name" placeholder="Category Name (e.g., House Cleaning)">';
            html += '<button type="button" class="button remove-category-btn" title="Remove Category"><span class="dashicons dashicons-trash"></span></button>';
            html += '</div>';
            html += '<div class="category-services">';
            html += '<div class="services-list"></div>';
            html += '<button type="button" class="button button-small add-service-btn"><span class="dashicons dashicons-plus"></span> Add Service</button>';
            html += '</div>';
            html += '</div>';

            $categoriesList.append(html);
        },

        addServiceRow: function ($categoryItem) {
            var $container = $categoryItem.closest('.offer-catalog-repeater');
            var name = $container.data('name');
            var catIndex = $categoryItem.data('category-index');
            var $servicesList = $categoryItem.find('.services-list');
            var svcIndex = $servicesList.find('.service-item').length;

            var baseName = name + '[categories][' + catIndex + '][services][' + svcIndex + ']';

            var html = '<div class="service-item" data-service-index="' + svcIndex + '">';
            html += '<div class="service-fields">';
            html += '<input type="text" name="' + baseName + '[name]" class="widefat service-name" placeholder="Service Name">';
            html += '<input type="text" name="' + baseName + '[description]" class="widefat service-description" placeholder="Description (optional)">';
            html += '<div class="service-price-row">';
            html += '<input type="text" name="' + baseName + '[price]" class="service-price" placeholder="Price">';
            html += '<input type="text" name="' + baseName + '[priceCurrency]" class="service-currency" placeholder="USD" value="USD">';
            html += '<input type="url" name="' + baseName + '[url]" class="service-url" placeholder="Service URL (optional)">';
            html += '</div>';
            html += '</div>';
            html += '<button type="button" class="button button-small remove-service-btn" title="Remove Service"><span class="dashicons dashicons-no-alt"></span></button>';
            html += '</div>';

            $servicesList.append(html);
        },

        reindexSchemas: function () {
            $('.schema-item').each(function (newIndex) {
                var $item = $(this);
                var oldIndex = $item.data('index');

                // Update data attribute
                $item.attr('data-index', newIndex);
                $item.data('index', newIndex);

                // Update all input names
                $item.find('[name]').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        name = name.replace(/rankflow_seo_schemas\[\d+\]/, 'rankflow_seo_schemas[' + newIndex + ']');
                        $(this).attr('name', name);
                    }
                });

                // Update IDs
                $item.find('[id^="schema_"]').each(function () {
                    var id = $(this).attr('id');
                    if (id) {
                        id = id.replace(/schema_\d+_/, 'schema_' + newIndex + '_');
                        $(this).attr('id', id);
                    }
                });
            });
        }
    };

    $(document).ready(function () {
        SchemaAdmin.init();
    });

})(jQuery);
