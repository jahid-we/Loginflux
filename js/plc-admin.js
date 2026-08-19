/**
 * Pro Login Customizer - Admin JavaScript
 *
 * @package Pro_Login_Customizer
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize WP Color Picker
        if ($.fn.wpColorPicker) {
            $('.plc-color-picker').wpColorPicker();
        }

        // Settings Tabs Navigation
        $('.plc-nav-tab').on('click', function(e) {
            e.preventDefault();
            var targetTab = $(this).data('tab');

            $('.plc-nav-tab').removeClass('active');
            $(this).addClass('active');

            $('.plc-tab-content').removeClass('active');
            $('#plc-tab-' + targetTab).addClass('active');

            if (window.localStorage) {
                localStorage.setItem('plc_active_tab', targetTab);
            }
        });

        // Restore active tab
        if (window.localStorage) {
            var activeTab = localStorage.getItem('plc_active_tab');
            if (activeTab && $('#plc-tab-' + activeTab).length) {
                $('.plc-nav-tab[data-tab="' + activeTab + '"]').trigger('click');
            }
        }

        // WP Media Uploader Handler
        $('.plc-upload-btn').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var targetInput = $('#' + button.data('target'));
            var targetPreview = $('#' + button.data('preview'));

            // Create media frame
            var customUploader = wp.media({
                title: button.data('modal-title') || 'Select or Upload Image',
                button: {
                    text: button.data('modal-button') || 'Use This Image'
                },
                multiple: false
            });

            // When an image is selected in the media frame
            customUploader.on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url).trigger('change');

                if (targetPreview.length) {
                    targetPreview.html('<img src="' + attachment.url + '" alt="Preview" /><button type="button" class="plc-remove-btn dashicons dashicons-no-alt" title="Remove image"></button>').show();
                }
            });

            // Open the modal
            customUploader.open();
        });

        // Remove image button
        $(document).on('click', '.plc-remove-btn', function(e) {
            e.preventDefault();
            var previewContainer = $(this).closest('.plc-image-preview');
            var targetInputId = previewContainer.data('input');
            $('#' + targetInputId).val('').trigger('change');
            previewContainer.html('').hide();
        });

        // Background Type Conditional Notice
        function handleBgImageNotice() {
            var bgImageVal = $('#plc_bg_image').val().trim();
            if (bgImageVal !== '') {
                $('.plc-bg-image-notice').slideDown(200);
            } else {
                $('.plc-bg-image-notice').slideUp(200);
            }
        }

        $('#plc_bg_image').on('input change', handleBgImageNotice);
        handleBgImageNotice();
    });

})(jQuery);
