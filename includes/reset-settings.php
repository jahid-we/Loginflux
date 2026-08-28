<?php
/**
 * Reset Settings
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Restore all plugin settings to their default values.
 */
function jzlf_reset_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to reset these settings.', 'loginflux' ) );
    }

    check_admin_referer( 'jzlf_reset_settings', 'jzlf_reset_settings_nonce' );

    update_option( 'loginflux_settings', jzlf_get_default_settings() );

    $redirect_url = add_query_arg(
        [
            'page'                     => 'loginflux',
            'loginflux_settings_reset' => '1',
        ],
        admin_url( 'admin.php' )
    );

    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_post_jzlf_reset_settings', 'jzlf_reset_settings' );

