<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Loginflux
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options for the current site.
delete_option( 'loginflux_settings' );
delete_option( 'loginflux_activation_redirect' );

// For multisite, delete options from all sites.
if ( is_multisite() ) {
	$loginflux_sites = get_sites(
		array(
			'number' => 0,
		)
	);

	foreach ( $loginflux_sites as $loginflux_site ) {
		switch_to_blog( $loginflux_site->blog_id );

		delete_option( 'loginflux_settings' );
		delete_option( 'loginflux_activation_redirect' );

		restore_current_blog();
	}
}