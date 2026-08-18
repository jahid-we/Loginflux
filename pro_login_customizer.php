<?php
/*
 * Plugin Name:       Pro Login Customizer
 * Plugin URI:        https://example.com/pro-login-customizer/
 * Description:       Customize the WordPress login page with custom logo, colors, background, and styling.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Jahid Hasan
 * Author URI:        https://github.com/jahid-we
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pl-customizer
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Plugin Constants
 */
define( 'PLC_VERSION', '1.0.0' );
define( 'PLC_URL', plugin_dir_url( __FILE__ ) );
define( 'PLC_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Plugin Activation
 */
function plc_activate() {

    $default_settings = [
    'logo'             => PLC_URL . 'images/logo.png',
    'bg_image'         => PLC_URL . 'images/BG.jpg',
    'primary_color'    => '#2563eb',
    'hover_color'      => '#1d4ed8',
    'text_color'       => '#0f172a',
    'background_color' => '#f5f7fb',
    'border_radius'    => '10',
    ];

    if ( false === get_option( 'plc_settings' ) ) {
        add_option( 'plc_settings', $default_settings );
    }

    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'plc_activate' );


/**
 * Plugin Deactivation
 */
function plc_deactivate() {

    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'plc_deactivate' );


/**
 * Get Plugin Settings
 */
function plc_get_settings() {

    $defaults = [
    'logo'             => PLC_URL . 'images/logo.png',
    'bg_image'         => PLC_URL . 'images/BG.jpg',
    'primary_color'    => '#2563eb',
    'hover_color'      => '#1d4ed8',
    'text_color'       => '#0f172a',
    'background_color' => '#f5f7fb',
    'border_radius'    => '10',
];

    $settings = get_option( 'plc_settings', [] );

    if ( ! is_array( $settings ) ) {
        $settings = [];
    }

    return wp_parse_args( $settings, $defaults );
}


/**
 * Add Admin Menu
 */
function plc_add_admin_menu() {

    add_menu_page(
        __( 'Login Page', 'pl-customizer' ),
        __( 'Login Page', 'pl-customizer' ),
        'manage_options',
        'pro-login-customizer',
        'plc_login_page_content',
        'dashicons-lock',
        25
    );
}

add_action( 'admin_menu', 'plc_add_admin_menu' );


/**
 * Register Settings
 */
function plc_register_settings() {

    register_setting(
        'plc_settings_group',
        'plc_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'plc_sanitize_settings',
            'default'           => [],
        ]
    );
}

add_action( 'admin_init', 'plc_register_settings' );


/**
 * Sanitize Settings
 */
function plc_sanitize_settings( $input ) {

    $sanitized = [];

    if ( ! is_array( $input ) ) {
        return $sanitized;
    }


    /*
     * Logo
     */
    if ( isset( $input['logo'] ) ) {
        $sanitized['logo'] = esc_url_raw( $input['logo'] );
    }

    /*
     * Background Image
     */
    if ( isset( $input['bg_image'] ) ) {
        $sanitized['bg_image'] = esc_url_raw( $input['bg_image'] );
    }


    /*
     * Primary Color
     */
    if ( isset( $input['primary_color'] ) ) {
        $sanitized['primary_color'] = sanitize_hex_color(
            $input['primary_color']
        );
    }


    /*
     * Hover Color
     */
    if ( isset( $input['hover_color'] ) ) {
        $sanitized['hover_color'] = sanitize_hex_color(
            $input['hover_color']
        );
    }


    /*
     * Text Color
     */
    if ( isset( $input['text_color'] ) ) {
        $sanitized['text_color'] = sanitize_hex_color(
            $input['text_color']
        );
    }


    /*
     * Background Color
     */
    if ( isset( $input['background_color'] ) ) {
        $sanitized['background_color'] = sanitize_hex_color(
            $input['background_color']
        );
    }


    /*
     * Border Radius
     */
    if ( isset( $input['border_radius'] ) ) {

        $sanitized['border_radius'] = absint(
            $input['border_radius']
        );
    }


    return $sanitized;
}


/**
 * Admin Page
 */
function plc_login_page_content() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings = plc_get_settings();

    ?>

    <div class="wrap plc-admin-wrap">

        <h1>
            <?php esc_html_e( 'Pro Login Customizer', 'pl-customizer' ); ?>
        </h1>

        <p>
            <?php
            esc_html_e(
                'Customize your WordPress login page appearance.',
                'pl-customizer'
            );
            ?>
        </p>


        <form action="options.php" method="post">

            <?php
            settings_fields( 'plc_settings_group' );
            ?>


            <table class="form-table" role="presentation">

                <tbody>


                    <!-- Primary Color -->
                    <tr>

                        <th scope="row">

                            <label for="plc_primary_color">

                                <?php
                                esc_html_e(
                                    'Primary Color',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="color"
                                id="plc_primary_color"
                                name="plc_settings[primary_color]"
                                value="<?php echo esc_attr( $settings['primary_color'] ); ?>"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Main color used on the login page.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                    <!-- Hover Color -->
                    <tr>

                        <th scope="row">

                            <label for="plc_hover_color">

                                <?php
                                esc_html_e(
                                    'Primary Hover Color',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="color"
                                id="plc_hover_color"
                                name="plc_settings[hover_color]"
                                value="<?php echo esc_attr( $settings['hover_color'] ); ?>"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Color used when hovering over buttons.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                    <!-- Text Color -->
                    <tr>

                        <th scope="row">

                            <label for="plc_text_color">

                                <?php
                                esc_html_e(
                                    'Text Color',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="color"
                                id="plc_text_color"
                                name="plc_settings[text_color]"
                                value="<?php echo esc_attr( $settings['text_color'] ); ?>"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Main text color on the login page.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                    <!-- Background Color -->
                    <tr>

                        <th scope="row">

                            <label for="plc_background_color">

                                <?php
                                esc_html_e(
                                    'Background Color',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="color"
                                id="plc_background_color"
                                name="plc_settings[background_color]"
                                value="<?php echo esc_attr( $settings['background_color'] ); ?>"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Login page background color.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                    <!-- Border Radius -->
                    <tr>

                        <th scope="row">

                            <label for="plc_border_radius">

                                <?php
                                esc_html_e(
                                    'Border Radius',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="number"
                                id="plc_border_radius"
                                name="plc_settings[border_radius]"
                                value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
                                min="0"
                                max="50"
                            >

                            <span>px</span>

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Border radius of the login form.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                    <!-- Logo -->
                    <tr>

                        <th scope="row">

                            <label for="plc_logo">

                                <?php
                                esc_html_e(
                                    'Logo URL',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="url"
                                id="plc_logo"
                                name="plc_settings[logo]"
                                value="<?php echo esc_attr( $settings['logo'] ); ?>"
                                class="regular-text"
                                placeholder="https://example.com/logo.png"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Enter the URL of your login logo.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>

                    <!-- Background Image -->
                    <tr>

                        <th scope="row">

                            <label for="plc_bg_image">

                                <?php
                                esc_html_e(
                                    'Background Image URL',
                                    'pl-customizer'
                                );
                                ?>

                            </label>

                        </th>

                        <td>

                            <input
                                type="url"
                                id="plc_bg_image"
                                name="plc_settings[bg_image]"
                                value="<?php echo esc_attr( $settings['bg_image'] ); ?>"
                                class="regular-text"
                                placeholder="https://example.com/bg-image.jpg"
                            >

                            <p class="description">
                                <?php
                                esc_html_e(
                                    'Enter the URL of your login background image.',
                                    'pl-customizer'
                                );
                                ?>
                            </p>

                        </td>

                    </tr>


                </tbody>

            </table>


            <?php submit_button( __( 'Save Changes', 'pl-customizer' ) ); ?>


        </form>

    </div>

    <?php
}


/**
 * Enqueue Login Page CSS
 */
function plc_login_enqueue_style() {

    $settings = plc_get_settings();

    wp_enqueue_style(
        'plc-login-style',
        PLC_URL . 'css/plc-style.css',
        [],
        PLC_VERSION
    );


    /*
     * Dynamic CSS Variables
     */
    $custom_css = "
        :root {
            --plc-primary: {$settings['primary_color']};
            --plc-primary-hover: {$settings['hover_color']};
            --plc-text: {$settings['text_color']};
            --plc-radius: {$settings['border_radius']}px;
            --plc-logo: url('{$settings['logo']}');
        }
        body.login {
            background-color: {$settings['background_color']};
        } 
    ";
    /*
     * Add Background Image Only If Available
     */
    if ( ! empty( $settings['bg_image'] ) ) {

        $custom_css .= "
            body.login {
                background-image: url('{$settings['bg_image']}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
        ";
    }


    wp_add_inline_style(
        'plc-login-style',
        $custom_css
    );
}

add_action( 'login_enqueue_scripts', 'plc_login_enqueue_style' );


/**
 * Change Login Logo URL
 */
function plc_login_logo_url() {

    return home_url( '/' );
}

add_filter( 'login_headerurl', 'plc_login_logo_url' );


/**
 * Change Login Logo Title
 */
function plc_login_logo_title() {

    return get_bloginfo( 'name' );
}

add_filter( 'login_headertext', 'plc_login_logo_title' );