<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Pro_Login_Customizer
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete the plugin settings from the database
delete_option( 'plc_settings' );

// For multisite, we may need to delete the option from all blogs if it was activated network-wide
if ( is_multisite() ) {
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        delete_option( 'plc_settings' );
    }

    switch_to_blog( $original_blog_id );
}
