<?php
/**
 * Sanitization
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize Settings
 *
 * @param array $input Raw form input.
 * @return array Sanitized settings.
 */
function jzlf_sanitize_settings( $input ) {
    $sanitized = [];

    if ( ! is_array( $input ) ) {
        return $sanitized;
    }

    // Logo & Branding
    $sanitized['logo']          = isset( $input['logo'] ) ? esc_url_raw( $input['logo'] ) : '';
    $sanitized['logo_width']    = isset( $input['logo_width'] ) ? absint( $input['logo_width'] ) : 160;
    $sanitized['logo_height']   = isset( $input['logo_height'] ) ? absint( $input['logo_height'] ) : 50;
    $sanitized['form_subtitle'] = isset( $input['form_subtitle'] ) ? sanitize_text_field( $input['form_subtitle'] ) : '';

    // Background & Visual Effects
    $sanitized['bg_image']         = isset( $input['bg_image'] ) ? esc_url_raw( $input['bg_image'] ) : '';

    // Animation Master Enable
    $anim_enabled                  = ! empty( $input['animation_enable'] ) || ! empty( $input['aurora_enable'] );
    $sanitized['animation_enable'] = $anim_enabled ? '1' : '0';
    $sanitized['aurora_enable']    = $sanitized['animation_enable']; // backward compatibility

    // Animation Style Selection
    $valid_anim_types              = [ '1', '2', '3', '4', '5', '6', '7', '8' ];
    $sanitized['animation_type']   = ( isset( $input['animation_type'] ) && in_array( (string) $input['animation_type'], $valid_anim_types, true ) ) ? (string) $input['animation_type'] : '3';

    // Animation 1 (Pulse Orb & Cyber Grid)
    $sanitized['anim1_bg']         = isset( $input['anim1_bg'] ) ? sanitize_hex_color( $input['anim1_bg'] ) : '#090d16';
    $sanitized['anim1_color_1']    = isset( $input['anim1_color_1'] ) ? sanitize_hex_color( $input['anim1_color_1'] ) : '#3b82f6';
    $sanitized['anim1_color_2']    = isset( $input['anim1_color_2'] ) ? sanitize_hex_color( $input['anim1_color_2'] ) : '#ec4899';
    $sanitized['anim1_speed']      = isset( $input['anim1_speed'] ) ? absint( $input['anim1_speed'] ) : 12;
    $sanitized['anim1_grid']       = ! empty( $input['anim1_grid'] ) ? '1' : '0';

    // Animation 2 (Nebula Glow & Noise)
    $sanitized['anim2_bg']         = isset( $input['anim2_bg'] ) ? sanitize_hex_color( $input['anim2_bg'] ) : '#0b0f19';
    $sanitized['anim2_color_1']    = isset( $input['anim2_color_1'] ) ? sanitize_hex_color( $input['anim2_color_1'] ) : '#06b6d4';
    $sanitized['anim2_color_2']    = isset( $input['anim2_color_2'] ) ? sanitize_hex_color( $input['anim2_color_2'] ) : '#8b5cf6';
    $sanitized['anim2_color_3']    = isset( $input['anim2_color_3'] ) ? sanitize_hex_color( $input['anim2_color_3'] ) : '#f43f5e';
    $sanitized['anim2_speed']      = isset( $input['anim2_speed'] ) ? absint( $input['anim2_speed'] ) : 10;
    $sanitized['anim2_noise']      = ! empty( $input['anim2_noise'] ) ? '1' : '0';

    // Animation 3 (Aurora Gradient Flow)
    $sanitized['bg_color']         = isset( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : '#030712';
    $sanitized['aurora_color_1']   = isset( $input['aurora_color_1'] ) ? sanitize_hex_color( $input['aurora_color_1'] ) : '#030712';
    $sanitized['aurora_color_2']   = isset( $input['aurora_color_2'] ) ? sanitize_hex_color( $input['aurora_color_2'] ) : '#1e1b4b';
    $sanitized['aurora_color_3']   = isset( $input['aurora_color_3'] ) ? sanitize_hex_color( $input['aurora_color_3'] ) : '#0284c7';
    $sanitized['aurora_color_4']   = isset( $input['aurora_color_4'] ) ? sanitize_hex_color( $input['aurora_color_4'] ) : '#4f46e5';
    $sanitized['aurora_speed']     = isset( $input['aurora_speed'] ) ? absint( $input['aurora_speed'] ) : 15;

    // Animation 4 (Ambient Mesh Spin)
    $sanitized['anim4_bg']         = isset( $input['anim4_bg'] ) ? sanitize_hex_color( $input['anim4_bg'] ) : '#0f172a';
    $sanitized['anim4_color_1']    = isset( $input['anim4_color_1'] ) ? sanitize_hex_color( $input['anim4_color_1'] ) : '#818cf8';
    $sanitized['anim4_color_2']    = isset( $input['anim4_color_2'] ) ? sanitize_hex_color( $input['anim4_color_2'] ) : '#c084fc';
    $sanitized['anim4_color_3']    = isset( $input['anim4_color_3'] ) ? sanitize_hex_color( $input['anim4_color_3'] ) : '#38bdf8';
    $sanitized['anim4_speed']      = isset( $input['anim4_speed'] ) ? absint( $input['anim4_speed'] ) : 20;

    // Animation 5 (Cosmic Starfield & Stardust)
    $sanitized['anim5_bg']         = isset( $input['anim5_bg'] ) ? sanitize_hex_color( $input['anim5_bg'] ) : '#050814';
    $sanitized['anim5_color_1']    = isset( $input['anim5_color_1'] ) ? sanitize_hex_color( $input['anim5_color_1'] ) : '#6366f1';
    $sanitized['anim5_color_2']    = isset( $input['anim5_color_2'] ) ? sanitize_hex_color( $input['anim5_color_2'] ) : '#38bdf8';
    $sanitized['anim5_speed']      = isset( $input['anim5_speed'] ) ? absint( $input['anim5_speed'] ) : 18;
    $sanitized['anim5_stars']      = ! empty( $input['anim5_stars'] ) ? '1' : '0';

    // Animation 6 (Holographic Prism & Cyber Waves)
    $sanitized['anim6_bg']         = isset( $input['anim6_bg'] ) ? sanitize_hex_color( $input['anim6_bg'] ) : '#0a0618';
    $sanitized['anim6_color_1']    = isset( $input['anim6_color_1'] ) ? sanitize_hex_color( $input['anim6_color_1'] ) : '#f43f5e';
    $sanitized['anim6_color_2']    = isset( $input['anim6_color_2'] ) ? sanitize_hex_color( $input['anim6_color_2'] ) : '#8b5cf6';
    $sanitized['anim6_color_3']    = isset( $input['anim6_color_3'] ) ? sanitize_hex_color( $input['anim6_color_3'] ) : '#06b6d4';
    $sanitized['anim6_speed']      = isset( $input['anim6_speed'] ) ? absint( $input['anim6_speed'] ) : 14;
    $sanitized['anim6_lines']      = ! empty( $input['anim6_lines'] ) ? '1' : '0';

    // Animation 7 (Retro Synthwave & Neon Horizon)
    $sanitized['anim7_bg']         = isset( $input['anim7_bg'] ) ? sanitize_hex_color( $input['anim7_bg'] ) : '#090514';
    $sanitized['anim7_color_1']    = isset( $input['anim7_color_1'] ) ? sanitize_hex_color( $input['anim7_color_1'] ) : '#ff2a85';
    $sanitized['anim7_color_2']    = isset( $input['anim7_color_2'] ) ? sanitize_hex_color( $input['anim7_color_2'] ) : '#00f2fe';
    $sanitized['anim7_speed']      = isset( $input['anim7_speed'] ) ? absint( $input['anim7_speed'] ) : 12;
    $sanitized['anim7_sun']        = ! empty( $input['anim7_sun'] ) ? '1' : '0';

    // Animation 8 (Liquid Morphing Blobs)
    $sanitized['anim8_bg']         = isset( $input['anim8_bg'] ) ? sanitize_hex_color( $input['anim8_bg'] ) : '#030712';
    $sanitized['anim8_color_1']    = isset( $input['anim8_color_1'] ) ? sanitize_hex_color( $input['anim8_color_1'] ) : '#6366f1';
    $sanitized['anim8_color_2']    = isset( $input['anim8_color_2'] ) ? sanitize_hex_color( $input['anim8_color_2'] ) : '#ec4899';
    $sanitized['anim8_color_3']    = isset( $input['anim8_color_3'] ) ? sanitize_hex_color( $input['anim8_color_3'] ) : '#06b6d4';
    $sanitized['anim8_speed']      = isset( $input['anim8_speed'] ) ? absint( $input['anim8_speed'] ) : 16;
    $sanitized['anim8_blur']       = isset( $input['anim8_blur'] ) ? absint( $input['anim8_blur'] ) : 60;

    // Theme Colors & Styling
    $sanitized['primary_color']    = isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#6366f1';
    $sanitized['hover_color']      = isset( $input['hover_color'] ) ? sanitize_hex_color( $input['hover_color'] ) : '#4f46e5';
    $sanitized['text_color']       = isset( $input['text_color'] ) ? sanitize_hex_color( $input['text_color'] ) : '#0f172a';
    $sanitized['card_bg_color']    = isset( $input['card_bg_color'] ) ? sanitize_text_field( $input['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $sanitized['card_blur']        = isset( $input['card_blur'] ) ? absint( $input['card_blur'] ) : 28;
    $sanitized['border_radius']    = isset( $input['border_radius'] ) ? absint( $input['border_radius'] ) : 24;

    return $sanitized;
}