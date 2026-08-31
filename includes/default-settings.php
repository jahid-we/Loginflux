<?php
/**
 * Core Helper Functions
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Default Plugin Settings
 *
 * @return array
 */
function jzlf_get_default_settings() {
    return [
        'logo'             => LOGINFLUX_URL . 'images/logo.png',
        'logo_width'       => '160',
        'logo_height'      => '50',
        'form_subtitle'    => __( 'Sign in to your account', 'loginflux' ),
        'bg_image'         => '',

        // Animation Master Controls
        'animation_enable' => '1',
        'aurora_enable'    => '1',
        'animation_type'   => '3',

        // Animation 1: Pulse Orb & Cyber Grid
        'anim1_bg'         => '#090d16',
        'anim1_color_1'    => '#3b82f6',
        'anim1_color_2'    => '#ec4899',
        'anim1_speed'      => '12',
        'anim1_grid'       => '1',

        // Animation 2: Nebula Glow & Noise Overlay
        'anim2_bg'         => '#0b0f19',
        'anim2_color_1'    => '#06b6d4',
        'anim2_color_2'    => '#8b5cf6',
        'anim2_color_3'    => '#f43f5e',
        'anim2_speed'      => '10',
        'anim2_noise'      => '1',

        // Animation 3: Aurora Gradient Flow
        'bg_color'         => '#030712',
        'aurora_color_1'   => '#030712',
        'aurora_color_2'   => '#1e1b4b',
        'aurora_color_3'   => '#0284c7',
        'aurora_color_4'   => '#4f46e5',
        'aurora_speed'     => '15',

        // Animation 4: Ambient Mesh Spin
        'anim4_bg'         => '#0f172a',
        'anim4_color_1'    => '#818cf8',
        'anim4_color_2'    => '#c084fc',
        'anim4_color_3'    => '#38bdf8',
        'anim4_speed'      => '20',

        // Animation 5: Cosmic Starfield & Stardust Flow
        'anim5_bg'         => '#050814',
        'anim5_color_1'    => '#6366f1',
        'anim5_color_2'    => '#38bdf8',
        'anim5_speed'      => '18',
        'anim5_stars'      => '1',

        // Animation 6: Holographic Prism & Cyber Waves
        'anim6_bg'         => '#0a0618',
        'anim6_color_1'    => '#f43f5e',
        'anim6_color_2'    => '#8b5cf6',
        'anim6_color_3'    => '#06b6d4',
        'anim6_speed'      => '14',
        'anim6_lines'      => '1',

        // Animation 7: Retro Synthwave & Neon Horizon
        'anim7_bg'         => '#090514',
        'anim7_color_1'    => '#ff2a85',
        'anim7_color_2'    => '#00f2fe',
        'anim7_speed'      => '12',
        'anim7_sun'        => '1',

        // Animation 8: Liquid Morphing Blobs
        'anim8_bg'         => '#030712',
        'anim8_color_1'    => '#6366f1',
        'anim8_color_2'    => '#ec4899',
        'anim8_color_3'    => '#06b6d4',
        'anim8_speed'      => '16',
        'anim8_blur'       => '60',

        // Theme Colors & Styling
        'primary_color'    => '#6366f1',
        'hover_color'      => '#4f46e5',
        'text_color'       => '#0f172a',
        'card_bg_color'    => 'rgba(255, 255, 255, 0.45)',
        'card_blur'        => '28',
        'border_radius'    => '24',
    ];
}