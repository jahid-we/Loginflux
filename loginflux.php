<?php
/**
 * Plugin Name:       Loginflux
 * Plugin URI:        https://github.com/jahid-we/Loginflux
 * Description:       Transform your login page with animated visual effects, dynamic backgrounds, glassmorphism, custom branding, and modern color controls.
 * Version:           1.3.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Jahid Hasan
 * Author URI:        https://github.com/jahid-we
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       loginflux
 * Domain Path:       /languages
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin Constants
 */
define( 'LOGINFLUX_VERSION', '1.3.0' );
define( 'LOGINFLUX_FILE', __FILE__ );
define( 'LOGINFLUX_URL', plugin_dir_url( __FILE__ ) );
define( 'LOGINFLUX_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOGINFLUX_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Core Settings & Configuration
 */
require_once LOGINFLUX_PATH . 'includes/default-settings.php';
require_once LOGINFLUX_PATH . 'includes/settings.php';
require_once LOGINFLUX_PATH . 'includes/sanitization.php';
require_once LOGINFLUX_PATH . 'includes/reset-settings.php';
require_once LOGINFLUX_PATH . 'includes/activation.php';

/**
 * Frontend Login Page Customization
 */
require_once LOGINFLUX_PATH . 'includes/frontend-login.php';

/**
 * Admin Panel & Dashboard
 */
require_once LOGINFLUX_PATH . 'includes/admin/admin-menu.php';
require_once LOGINFLUX_PATH . 'includes/admin/admin-assets.php';
require_once LOGINFLUX_PATH . 'includes/admin/settings-page.php';
