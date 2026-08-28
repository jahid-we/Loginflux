/**
 * Loginflux - Admin JavaScript
 *
 * @package Loginflux
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Clean up reset query parameter from address bar so reloading doesn't re-trigger notification
        if (window.history && window.history.replaceState && window.location.search.indexOf('loginflux_settings_reset') !== -1) {
            var cleanUrl = window.location.href
                .replace(/([?&])loginflux_settings_reset=[^&]+(&|$)/, function(match, p1, p2) {
                    return p2 === '&' ? p1 : '';
                })
                .replace(/[?&]$/, '');
            window.history.replaceState(null, '', cleanUrl);
        }

        // Initialize WP Color Picker
        if ($.fn.wpColorPicker) {
            $('.loginflux-color-picker').wpColorPicker();
        }

        // Settings Tabs Navigation
        function activateTab(tab) {
            var targetTab = $(tab);
            var targetId = targetTab.data('tab');

            $('.loginflux-nav-tab')
                .removeClass('active')
                .attr({ 'aria-selected': 'false', tabindex: '-1' });
            targetTab
                .addClass('active')
                .attr({ 'aria-selected': 'true', tabindex: '0' });

            $('.loginflux-tab-content')
                .removeClass('active')
                .attr('aria-hidden', 'true');
            $('#loginflux-tab-' + targetId)
                .addClass('active')
                .attr('aria-hidden', 'false');

            if (window.localStorage) {
                localStorage.setItem('loginflux_active_tab', targetId);
            }
        }

        $(document).on('click', '.loginflux-nav-tab', function(e) {
            e.preventDefault();
            activateTab(this);
        }).on('keydown', '.loginflux-nav-tab', function(e) {
            var tabs = $('.loginflux-nav-tab');
            var currentIndex = tabs.index(this);
            var nextIndex;

            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                nextIndex = (currentIndex + 1) % tabs.length;
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            } else if (e.key === 'Home') {
                nextIndex = 0;
            } else if (e.key === 'End') {
                nextIndex = tabs.length - 1;
            } else {
                return;
            }

            e.preventDefault();
            tabs.eq(nextIndex).focus();
            activateTab(tabs.eq(nextIndex));
        });

        // Restore active tab
        if (window.localStorage) {
            var activeTab = localStorage.getItem('loginflux_active_tab');
            if (activeTab) {
                var matchingTab = $('.loginflux-nav-tab[data-tab="' + activeTab + '"]');
                if (matchingTab.length) {
                    activateTab(matchingTab.first());
                }
            }
        }

        // WP Media Uploader Handler
        $(document).on('click', '.loginflux-upload-btn', function(e) {
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
                    targetPreview.html('<img src="' + attachment.url + '" alt="Preview" /><button type="button" class="loginflux-remove-btn dashicons dashicons-no-alt" title="Remove image"></button>').show();
                }
            });

            // Open the modal
            customUploader.open();
        });

        // Remove image button
        $(document).on('click', '.loginflux-remove-btn', function(e) {
            e.preventDefault();
            var previewContainer = $(this).closest('.loginflux-image-preview');
            var targetInputId = previewContainer.data('input');
            $('#' + targetInputId).val('').trigger('change');
            previewContainer.html('').hide();
        });

        // Background Type Conditional Notice
        function handleBgImageNotice() {
            var bgImageVal = $('#loginflux_bg_image').val() || '';
            bgImageVal = bgImageVal.trim();
            if (bgImageVal !== '') {
                $('.loginflux-bg-image-notice').slideDown(200);
            } else {
                $('.loginflux-bg-image-notice').slideUp(200);
            }
        }

        $(document).on('input change', '#loginflux_bg_image', handleBgImageNotice);
        handleBgImageNotice();

        // Animation Style Selector & Panel Switcher
        function handleAnimSelector() {
            var selectedType = $('input[name="loginflux_settings[animation_type]"]:checked').val() || '3';

            // Highlight active card
            $('.loginflux-anim-card').each(function() {
                var radio = $(this).find('.loginflux-anim-radio');
                if (radio.is(':checked')) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });

            // Switch settings panels
            $('.loginflux-anim-panel').each(function() {
                var panelType = $(this).data('anim-panel');
                if (String(panelType) === String(selectedType)) {
                    $(this).stop(true, true).slideDown(250);
                } else {
                    $(this).stop(true, true).slideUp(200);
                }
            });
        }

        $(document).on('change', '.loginflux-anim-radio', handleAnimSelector);
        $(document).on('click', '.loginflux-anim-card', function(e) {
            if (!$(e.target).is('input[type="radio"]')) {
                $(this).find('.loginflux-anim-radio').prop('checked', true).trigger('change');
            }
        });

        // Animation Master Enable Toggle
        function handleAnimMasterToggle() {
            var isEnabled = $('#loginflux_animation_enable').is(':checked');
            if (isEnabled) {
                $('.loginflux-anim-selector-grid, .loginflux-anim-panel').css({
                    'opacity': '1',
                    'pointer-events': 'auto'
                });
            } else {
                $('.loginflux-anim-selector-grid, .loginflux-anim-panel').css({
                    'opacity': '0.55',
                    'pointer-events': 'none'
                });
            }
        }

        $(document).on('change', '#loginflux_animation_enable', handleAnimMasterToggle);
        handleAnimMasterToggle();
    });

})(jQuery);
