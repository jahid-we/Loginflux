<?php
/**
 * Frontend Login Page Customization
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter Login Body Classes
 *
 * @param array $classes Array of login body classes.
 * @return array
 */
function jzlf_login_body_class( $classes ) {
    $settings = jzlf_get_settings();

    if ( ! empty( $settings['bg_image'] ) ) {
        $classes[] = 'loginflux-has-bg-image';
    } elseif ( ! empty( $settings['animation_enable'] ) && '1' === (string) $settings['animation_enable'] ) {
        $anim_type = ! empty( $settings['animation_type'] ) ? (string) $settings['animation_type'] : '3';
        switch ( $anim_type ) {
            case '1':
                $classes[] = 'loginflux-anim-1-active';
                if ( empty( $settings['anim1_grid'] ) || '1' !== (string) $settings['anim1_grid'] ) {
                    $classes[] = 'loginflux-grid-disabled';
                }
                break;
            case '2':
                $classes[] = 'loginflux-anim-2-active';
                if ( empty( $settings['anim2_noise'] ) || '1' !== (string) $settings['anim2_noise'] ) {
                    $classes[] = 'loginflux-noise-disabled';
                }
                break;
            case '4':
                $classes[] = 'loginflux-anim-4-active';
                break;
            case '3':
            default:
                $classes[] = 'loginflux-anim-3-active';
                $classes[] = 'loginflux-aurora-active';
                break;
        }
    }

    return $classes;
}
add_filter( 'login_body_class', 'jzlf_login_body_class' );

/**
 * Enqueue Login Page CSS & Dynamic Inline Styles
 */
function jzlf_login_enqueue_style() {
    $settings = jzlf_get_settings();

    wp_enqueue_style(
        'loginflux-login-style',
        LOGINFLUX_URL . 'css/loginflux-style.css',
        [],
        LOGINFLUX_VERSION
    );

    // Calculate dynamic branding & form values
    $logo_url      = ! empty( $settings['logo'] ) ? esc_url( $settings['logo'] ) : LOGINFLUX_URL . 'images/logo.png';
    $logo_width    = ! empty( $settings['logo_width'] ) ? absint( $settings['logo_width'] ) . 'px' : '160px';
    $logo_height   = ! empty( $settings['logo_height'] ) ? absint( $settings['logo_height'] ) . 'px' : '50px';
    $primary_color = ! empty( $settings['primary_color'] ) ? sanitize_hex_color( $settings['primary_color'] ) : '#6366f1';
    $hover_color   = ! empty( $settings['hover_color'] ) ? sanitize_hex_color( $settings['hover_color'] ) : '#4f46e5';
    $text_color    = ! empty( $settings['text_color'] ) ? sanitize_hex_color( $settings['text_color'] ) : '#0f172a';
    $card_bg       = ! empty( $settings['card_bg_color'] ) ? esc_attr( $settings['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $card_blur     = ! empty( $settings['card_blur'] ) ? absint( $settings['card_blur'] ) . 'px' : '28px';
    $border_radius = ! empty( $settings['border_radius'] ) ? absint( $settings['border_radius'] ) . 'px' : '24px';

    // Animation 1 (Pulse Orb & Cyber Grid)
    $anim1_bg      = ! empty( $settings['anim1_bg'] ) ? sanitize_hex_color( $settings['anim1_bg'] ) : '#090d16';
    $anim1_color1  = ! empty( $settings['anim1_color_1'] ) ? sanitize_hex_color( $settings['anim1_color_1'] ) : '#3b82f6';
    $anim1_color2  = ! empty( $settings['anim1_color_2'] ) ? sanitize_hex_color( $settings['anim1_color_2'] ) : '#ec4899';
    $anim1_speed   = ! empty( $settings['anim1_speed'] ) ? absint( $settings['anim1_speed'] ) . 's' : '12s';

    // Animation 2 (Nebula Glow & Noise)
    $anim2_bg      = ! empty( $settings['anim2_bg'] ) ? sanitize_hex_color( $settings['anim2_bg'] ) : '#0b0f19';
    $anim2_color1  = ! empty( $settings['anim2_color_1'] ) ? sanitize_hex_color( $settings['anim2_color_1'] ) : '#06b6d4';
    $anim2_color2  = ! empty( $settings['anim2_color_2'] ) ? sanitize_hex_color( $settings['anim2_color_2'] ) : '#8b5cf6';
    $anim2_color3  = ! empty( $settings['anim2_color_3'] ) ? sanitize_hex_color( $settings['anim2_color_3'] ) : '#f43f5e';
    $anim2_speed   = ! empty( $settings['anim2_speed'] ) ? absint( $settings['anim2_speed'] ) . 's' : '10s';

    // Animation 3 (Aurora Gradient Flow)
    $bg_color      = ! empty( $settings['bg_color'] ) ? sanitize_hex_color( $settings['bg_color'] ) : '#030712';
    $aurora_1      = ! empty( $settings['aurora_color_1'] ) ? sanitize_hex_color( $settings['aurora_color_1'] ) : $bg_color;
    $aurora_2      = ! empty( $settings['aurora_color_2'] ) ? sanitize_hex_color( $settings['aurora_color_2'] ) : '#1e1b4b';
    $aurora_3      = ! empty( $settings['aurora_color_3'] ) ? sanitize_hex_color( $settings['aurora_color_3'] ) : '#0284c7';
    $aurora_4      = ! empty( $settings['aurora_color_4'] ) ? sanitize_hex_color( $settings['aurora_color_4'] ) : '#4f46e5';
    $aurora_speed  = ! empty( $settings['aurora_speed'] ) ? absint( $settings['aurora_speed'] ) . 's' : '15s';

    // Animation 4 (Ambient Mesh Spin)
    $anim4_bg      = ! empty( $settings['anim4_bg'] ) ? sanitize_hex_color( $settings['anim4_bg'] ) : '#0f172a';
    $anim4_color1  = ! empty( $settings['anim4_color_1'] ) ? sanitize_hex_color( $settings['anim4_color_1'] ) : '#818cf8';
    $anim4_color2  = ! empty( $settings['anim4_color_2'] ) ? sanitize_hex_color( $settings['anim4_color_2'] ) : '#c084fc';
    $anim4_color3  = ! empty( $settings['anim4_color_3'] ) ? sanitize_hex_color( $settings['anim4_color_3'] ) : '#38bdf8';
    $anim4_speed   = ! empty( $settings['anim4_speed'] ) ? absint( $settings['anim4_speed'] ) . 's' : '20s';

    $dynamic_css = "
        :root {
            --loginflux-primary: {$primary_color};
            --loginflux-primary-hover: {$hover_color};
            --loginflux-text: {$text_color};
            --loginflux-card-bg: {$card_bg};
            --loginflux-card-blur: {$card_blur};
            --loginflux-radius: {$border_radius};
            --loginflux-logo: url('{$logo_url}');
            --loginflux-logo-width: {$logo_width};
            --loginflux-logo-height: {$logo_height};

            --loginflux-anim1-bg: {$anim1_bg};
            --loginflux-anim1-color1: {$anim1_color1};
            --loginflux-anim1-color2: {$anim1_color2};
            --loginflux-anim1-speed: {$anim1_speed};

            --loginflux-anim2-bg: {$anim2_bg};
            --loginflux-anim2-color1: {$anim2_color1};
            --loginflux-anim2-color2: {$anim2_color2};
            --loginflux-anim2-color3: {$anim2_color3};
            --loginflux-anim2-speed: {$anim2_speed};

            --loginflux-bg-color: {$bg_color};
            --loginflux-aurora-1: {$aurora_1};
            --loginflux-aurora-2: {$aurora_2};
            --loginflux-aurora-3: {$aurora_3};
            --loginflux-aurora-4: {$aurora_4};
            --loginflux-aurora-speed: {$aurora_speed};

            --loginflux-anim4-bg: {$anim4_bg};
            --loginflux-anim4-color1: {$anim4_color1};
            --loginflux-anim4-color2: {$anim4_color2};
            --loginflux-anim4-color3: {$anim4_color3};
            --loginflux-anim4-speed: {$anim4_speed};
        }
    ";

    // If background image is present, add variable
    if ( ! empty( $settings['bg_image'] ) ) {
        $bg_img_url = esc_url( $settings['bg_image'] );
        $dynamic_css .= "
            :root {
                --loginflux-bg-img: url('{$bg_img_url}');
            }
        ";
    }

    wp_add_inline_style( 'loginflux-login-style', $dynamic_css );
}
add_action( 'login_enqueue_scripts', 'jzlf_login_enqueue_style' );

/**
 * Change Login Logo URL to Site Home
 */
function jzlf_login_logo_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'jzlf_login_logo_url' );

/**
 * Change Login Logo Title / Header Text
 */
function jzlf_login_logo_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'jzlf_login_logo_title' );

/**
 * Render Custom Subtitle below logo
 *
 * @param string $message Existing login header message.
 * @return string
 */
function jzlf_login_custom_subtitle( $message ) {
    $settings = jzlf_get_settings();

    if ( ! empty( $settings['form_subtitle'] ) ) {
        $subtitle_html = sprintf(
            '<div class="loginflux-custom-subtitle">%s</div>',
            esc_html( $settings['form_subtitle'] )
        );
        $message = $subtitle_html . $message;
    }

    return $message;
}
add_filter( 'login_message', 'jzlf_login_custom_subtitle' );
