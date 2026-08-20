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

// Delete settings for the current site.
delete_option( 'plc_settings' );

// For multisite, delete settings from all sites.
if ( is_multisite() ) {

	$sites = get_sites(
		array(
			'number' => 0,
		)
	);

	foreach ( $sites as $site ) {

		switch_to_blog( $site->blog_id );

		delete_option( 'plc_settings' );

		restore_current_blog();
	}
}