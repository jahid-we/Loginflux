<?php
/**
 * Plugin Activation
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Activation
 */
function jzlf_activate() {
    $defaults = jzlf_get_default_settings();

    if ( false === get_option( 'loginflux_settings' ) ) {
        add_option( 'loginflux_settings', $defaults );
    }

    add_option( 'loginflux_activation_redirect', '1' );
}
register_activation_hook( LOGINFLUX_FILE, 'jzlf_activate' );

/**
 * Redirect the activating administrator to the plugin settings page.
 */
function jzlf_redirect_after_activation() {
    if ( ! get_option( 'loginflux_activation_redirect' ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    delete_option( 'loginflux_activation_redirect' );

    wp_safe_redirect( admin_url( 'options-general.php?page=loginflux' ) );
    exit;
}
add_action( 'admin_init', 'jzlf_redirect_after_activation' );