/**
 * RankFlow SEO - Admin Views Consolidated Scripts
 * 
 * This file contains all JavaScript that was previously inline in PHP view files.
 * Properly enqueued via wp_enqueue_script() in class-admin.php
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /* From: admin/views/redirects-list.php */
        if ($('#select-all').length) {
            $('#select-all').on('change', function() {
                $('input[name="redirect_ids[]"]').prop('checked', this.checked);
            });
        }

        /* From: admin/views/redirect-form.php */
        if ($('#redirect_type').length) {
            $('#redirect_type').on('change', function() {
                var type = $(this).val();
                if (type === '410' || type === '451') {
                    $('#target_url').closest('tr').hide();
                } else {
                    $('#target_url').closest('tr').show();
                }
            }).trigger('change');
        }

        /* From: admin/views/404-monitor.php */
        if ($('#create-redirect-modal').length) {
            $('.create-redirect-btn').on('click', function() {
                var sourceUrl = $(this).data('source');
                var logId = $(this).data('log-id') || '';
                $('#redirect-source').val(sourceUrl);
                $('#redirect-log-id').val(logId);
                $('#redirect-target').val('').focus();
                $('#create-redirect-modal').fadeIn(200);
            });

            $('#cancel-redirect, #create-redirect-modal > div:first-child').on('click', function() {
                $('#create-redirect-modal').fadeOut(200);
            });

            $('#create-redirect-modal > div:last-child').on('click', function(e) {
                e.stopPropagation();
            });
        }

        /* From: admin/views/settings-general.php */
        if ($('#title_separator').length) {
            $('#title_separator').on('change', function() {
                var separator = $(this).val();
                var siteName = $('#title-preview-text').data('site-name') || '';
                var preview = 'Your Post Title ' + separator + ' ' + siteName;
                $('#title-preview-text').text(preview);
            });
        }

        if ($('#site_represents').length) {
            $('#site_represents').on('change', function() {
                if ($(this).val() === 'organization') {
                    $('#organization_name_row').show();
                    $('#person_name_row').hide();
                } else {
                    $('#organization_name_row').hide();
                    $('#person_name_row').show();
                }
            }).trigger('change');
        }

        /* From: admin/views/settings-social.php */
        var defaultOgFrame;
        if ($('#default_og_image_upload').length) {
            $('#default_og_image_upload').on('click', function(e) {
                e.preventDefault();
                if (defaultOgFrame) { defaultOgFrame.open(); return; }
                defaultOgFrame = wp.media({
                    title: rankflowSeoViewsData.strings.selectDefaultOgImage || 'Select Default OG Image',
                    button: { text: rankflowSeoViewsData.strings.useThisImage || 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                defaultOgFrame.on('select', function() {
                    var attachment = defaultOgFrame.state().get('selection').first().toJSON();
                    $('#rankflow_seo_default_og_image').val(attachment.url);
                    $('#default_og_image_preview').html('<img src="' + attachment.url + '" alt=""><button type="button" class="default-og-image-remove" id="default_og_image_remove"><span class="dashicons dashicons-no-alt"></span></button>');
                });
                defaultOgFrame.open();
            });
            $(document).on('click', '#default_og_image_remove', function(e) {
                e.preventDefault();
                $('#rankflow_seo_default_og_image').val('');
                $('#default_og_image_preview').empty();
            });
        }

        /* From: admin/views/metabox.php */
        var ogImageFrame;
        if ($('#rankflow_seo_og_image_upload').length) {
            $('#rankflow_seo_og_image_upload').on('click', function(e) {
                e.preventDefault();
                if (ogImageFrame) { ogImageFrame.open(); return; }
                ogImageFrame = wp.media({
                    title: rankflowSeoViewsData.strings.selectOgImage || 'Select Open Graph Image',
                    button: { text: rankflowSeoViewsData.strings.useThisImage || 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                ogImageFrame.on('select', function() {
                    var attachment = ogImageFrame.state().get('selection').first().toJSON();
                    $('#rankflow_seo_og_image').val(attachment.url);
                    $('#rankflow_seo_og_image_preview').html('<img src="' + attachment.url + '" alt=""><button type="button" class="og-image-remove" id="rankflow_seo_og_image_remove"><span class="dashicons dashicons-no-alt"></span></button>');
                    updateSearchPreviewImage(attachment.url);
                });
                ogImageFrame.open();
            });
            $(document).on('click', '#rankflow_seo_og_image_remove', function(e) {
                e.preventDefault();
                $('#rankflow_seo_og_image').val('');
                $('#rankflow_seo_og_image_preview').empty();
                updateSearchPreviewImage('');
            });
            $(document).on('click', '#remove-post-thumbnail', function() {
                setTimeout(function() {
                    if (!$('#rankflow_seo_og_image').val()) { updateSearchPreviewImage(''); }
                }, 500);
            });
        }

        /* Helper function for updating search preview image */
        function updateSearchPreviewImage(url) {
            var $wrapper = $('.search-preview .preview-image-wrapper');
            if (!$wrapper.length) return;
            if (url) {
                $wrapper.html('<div class="preview-image"><img src="' + url + '" alt=""></div>');
            } else {
                var $featuredImg = $('#set-post-thumbnail img, #postimagediv img');
                if ($featuredImg.length && $featuredImg.attr('src')) {
                    $wrapper.html('<div class="preview-image"><img src="' + $featuredImg.attr('src') + '" alt=""></div>');
                } else {
                    $wrapper.html('<div class="preview-image preview-no-image"><span class="dashicons dashicons-format-image"></span><span>' + (rankflowSeoViewsData.strings.noImage || 'No image') + '</span></div>');
                }
            }
        }

        /* From: admin/views/settings-robots-txt.php */
        if ($('#copy-robots-txt').length) {
            $('#copy-robots-txt').on('click', function() {
                var content = $('#robots-preview-content').text();
                var $btn = $(this);
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(content).then(function() {
                        $btn.text(rankflowSeoViewsData.strings.copied || 'Copied!');
                        setTimeout(function() { $btn.text(rankflowSeoViewsData.strings.copyToClipboard || 'Copy to Clipboard'); }, 2000);
                    });
                }
            });
        }

        /* From: admin/views/settings-sitemap.php */
        if ($('#ping-sitemap').length) {
            $('#ping-sitemap').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text(rankflowSeoViewsData.strings.pinging || 'Pinging...');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: { action: 'rankflow_seo_ping_sitemap', nonce: rankflowSeoViewsData.nonce },
                    success: function(response) {
                        $btn.prop('disabled', false).text(rankflowSeoViewsData.strings.pingSitemap || 'Ping Search Engines');
                        alert(response.success ? (rankflowSeoViewsData.strings.pingSuccess || 'Sitemap pinged successfully!') : (response.data || 'Error'));
                    }
                });
            });
        }

        /* From: admin/views/settings-schema.php */
        /* Handle display mode select change for schema display rules */
        function handleDisplayModeChange($select) {
            var value = $select.val();
            var $schemaItem = $select.closest('.schema-item');
            
            // Hide all conditional rows
            $schemaItem.find('.post-types-row, .include-row, .exclude-row').addClass('rankflow-seo-hidden');
            
            // Show the appropriate row based on selection
            if (value === 'post_types') {
                $schemaItem.find('.post-types-row').removeClass('rankflow-seo-hidden');
            } else if (value === 'include') {
                $schemaItem.find('.include-row').removeClass('rankflow-seo-hidden');
            } else if (value === 'exclude') {
                $schemaItem.find('.exclude-row').removeClass('rankflow-seo-hidden');
            }
        }

        // Initialize display mode handlers for existing schemas
        if ($('.display-mode-select').length) {
            // Handle change event
            $(document).on('change', '.display-mode-select', function() {
                handleDisplayModeChange($(this));
            });
            
            // Initialize on page load for existing schemas
            $('.display-mode-select').each(function() {
                handleDisplayModeChange($(this));
            });
        }

        /* Handle "No schemas" message visibility */
        function updateNoSchemasMessage() {
            var $schemasContainer = $('#schemas-container');
            var $noSchemasMessage = $('.no-schemas-message');
            
            if ($schemasContainer.find('.schema-item').length > 0) {
                $noSchemasMessage.addClass('rankflow-seo-hidden');
            } else {
                $noSchemasMessage.removeClass('rankflow-seo-hidden');
            }
        }

        // Update message when schema is added or deleted
        $(document).on('click', '#add-schema', function() {
            // Small delay to allow the new schema to be added to DOM
            setTimeout(updateNoSchemasMessage, 100);
        });

        $(document).on('click', '.schema-delete', function() {
            // Small delay to allow the schema to be removed from DOM
            setTimeout(updateNoSchemasMessage, 100);
        });

        // Initialize on page load
        updateNoSchemasMessage();

    });
})(jQuery);
