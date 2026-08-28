<?php
/**
 * Settings page 
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Plugin Settings merged with defaults (with safe backward-compatible migration)
 *
 * @return array
 */
function jzlf_get_settings() {
    $defaults = jzlf_get_default_settings();
    $settings = get_option( 'loginflux_settings', [] );

    if ( ! is_array( $settings ) ) {
        $settings = [];
    }

    return wp_parse_args( $settings, $defaults );
}

/**
 * Register Settings
 */
function jzlf_register_settings() {
    register_setting(
        'loginflux_settings_group',
        'loginflux_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'jzlf_sanitize_settings',
            'default'           => jzlf_get_default_settings(),
        ]
    );
}
add_action( 'admin_init', 'jzlf_register_settings' );