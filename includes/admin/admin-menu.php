<?php
/**
 * Admin Menu
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Loginflux admin menu.
 */
function jzlf_add_admin_menu() {
	add_options_page(
		__( 'Loginflux', 'loginflux' ),
		__( 'Loginflux', 'loginflux' ),
		'manage_options',
		'loginflux',
		'jzlf_admin_page_content'
	);
}
add_action( 'admin_menu', 'jzlf_add_admin_menu' );

/**
 * Add plugin action links.
 *
 * @param array $links Array of plugin action links.
 * @return array
 */
function jzlf_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=loginflux' ) ),
		esc_html__( 'Settings', 'loginflux' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . LOGINFLUX_BASENAME, 'jzlf_action_links' );