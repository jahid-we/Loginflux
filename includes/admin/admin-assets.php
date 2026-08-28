<?php
/**
 * Admin Assets
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Enqueue Admin Assets
 *
 * @param string $hook_suffix Current admin page hook.
 */
function jzlf_admin_enqueue_scripts( $hook_suffix ) {
    if ( 'settings_page_loginflux' !== $hook_suffix ) {
        return;
    }

    // WP Media library
    wp_enqueue_media();

    // WP Color Picker
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    // Admin CSS & JS
    wp_enqueue_style(
        'loginflux-admin-style',
        LOGINFLUX_URL . 'css/loginflux-admin.css',
        [ 'wp-color-picker' ],
        LOGINFLUX_VERSION
    );

    wp_enqueue_script(
        'loginflux-admin-script',
        LOGINFLUX_URL . 'js/loginflux-admin.js',
        [ 'jquery', 'wp-color-picker' ],
        LOGINFLUX_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'jzlf_admin_enqueue_scripts' );