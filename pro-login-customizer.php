<?php
/**
 * Plugin Name:       Pro Login Customizer
 * Plugin URI:        https://github.com/jahid-we/pro_login_customizer
 * Description:       Customize your WordPress login page with stunning animated Aurora backgrounds, glassmorphism cards, custom logo, and modern color controls.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Jahid Hasan
 * Author URI:        https://github.com/jahid-we
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pro-login-customizer
 * Domain Path:       /languages
 *
 * @package Pro_Login_Customizer
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
define( 'PLC_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Get Default Plugin Settings
 *
 * @return array
 */
function plc_get_default_settings() {
    return [
        'logo'             => PLC_URL . 'images/logo.png',
        'logo_width'       => '160',
        'logo_height'      => '50',
        'form_subtitle'    => __( 'Sign in to your account', 'pro-login-customizer' ),
        'bg_image'         => '',
        'bg_color'         => '#030712',
        'aurora_enable'    => '1',
        'aurora_color_1'   => '#030712',
        'aurora_color_2'   => '#1e1b4b',
        'aurora_color_3'   => '#0284c7',
        'aurora_color_4'   => '#4f46e5',
        'aurora_speed'     => '15',
        'primary_color'    => '#6366f1',
        'hover_color'      => '#4f46e5',
        'text_color'       => '#0f172a',
        'card_bg_color'    => 'rgba(255, 255, 255, 0.45)',
        'card_blur'        => '28',
        'border_radius'    => '24',
        'custom_css'       => '',
    ];
}

/**
 * Plugin Activation
 */
function plc_activate() {
    $defaults = plc_get_default_settings();

    if ( false === get_option( 'plc_settings' ) ) {
        add_option( 'plc_settings', $defaults );
    }

}
register_activation_hook( __FILE__, 'plc_activate' );

/**
 * Get Plugin Settings merged with defaults
 *
 * @return array
 */
function plc_get_settings() {
    $defaults = plc_get_default_settings();
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
        __( 'Pro Login Customizer', 'pro-login-customizer' ),
        __( 'Login Page', 'pro-login-customizer' ),
        'manage_options',
        'pro-login-customizer',
        'plc_admin_page_content',
        'dashicons-lock',
        25
    );
}
add_action( 'admin_menu', 'plc_add_admin_menu' );

/**
 * Add Plugin Action Links
 */
function plc_action_links( $links ) {
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        admin_url( 'admin.php?page=pro-login-customizer' ),
        __( 'Settings', 'pro-login-customizer' )
    );
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . PLC_BASENAME, 'plc_action_links' );

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
            'default'           => plc_get_default_settings(),
        ]
    );
}
add_action( 'admin_init', 'plc_register_settings' );

/**
 * Sanitize Settings
 *
 * @param array $input Raw form input.
 * @return array Sanitized settings.
 */
function plc_sanitize_settings( $input ) {
    $sanitized = [];

    if ( ! is_array( $input ) ) {
        return $sanitized;
    }

    // Logo & Branding
    $sanitized['logo']          = isset( $input['logo'] ) ? esc_url_raw( $input['logo'] ) : '';
    $sanitized['logo_width']    = isset( $input['logo_width'] ) ? absint( $input['logo_width'] ) : 160;
    $sanitized['logo_height']   = isset( $input['logo_height'] ) ? absint( $input['logo_height'] ) : 50;
    $sanitized['form_subtitle'] = isset( $input['form_subtitle'] ) ? sanitize_text_field( $input['form_subtitle'] ) : '';

    // Background & Aurora
    $sanitized['bg_image']       = isset( $input['bg_image'] ) ? esc_url_raw( $input['bg_image'] ) : '';
    $sanitized['bg_color']       = isset( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : '#030712';
    $sanitized['aurora_enable']  = ! empty( $input['aurora_enable'] ) ? '1' : '0';
    $sanitized['aurora_color_1'] = isset( $input['aurora_color_1'] ) ? sanitize_hex_color( $input['aurora_color_1'] ) : '#030712';
    $sanitized['aurora_color_2'] = isset( $input['aurora_color_2'] ) ? sanitize_hex_color( $input['aurora_color_2'] ) : '#1e1b4b';
    $sanitized['aurora_color_3'] = isset( $input['aurora_color_3'] ) ? sanitize_hex_color( $input['aurora_color_3'] ) : '#0284c7';
    $sanitized['aurora_color_4'] = isset( $input['aurora_color_4'] ) ? sanitize_hex_color( $input['aurora_color_4'] ) : '#4f46e5';
    $sanitized['aurora_speed']   = isset( $input['aurora_speed'] ) ? absint( $input['aurora_speed'] ) : 15;

    // Theme Colors & Styling
    $sanitized['primary_color'] = isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#6366f1';
    $sanitized['hover_color']   = isset( $input['hover_color'] ) ? sanitize_hex_color( $input['hover_color'] ) : '#4f46e5';
    $sanitized['text_color']    = isset( $input['text_color'] ) ? sanitize_hex_color( $input['text_color'] ) : '#0f172a';
    $sanitized['card_bg_color'] = isset( $input['card_bg_color'] ) ? sanitize_text_field( $input['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $sanitized['card_blur']     = isset( $input['card_blur'] ) ? absint( $input['card_blur'] ) : 28;
    $sanitized['border_radius'] = isset( $input['border_radius'] ) ? absint( $input['border_radius'] ) : 24;

    // Custom CSS
    $sanitized['custom_css'] = isset( $input['custom_css'] ) ? plc_sanitize_custom_css( $input['custom_css'] ) : '';

    return $sanitized;
}

/**
 * Determine whether CSS is safe to place inside an inline style element.
 *
 * @param string $css CSS to validate.
 * @return bool Whether the CSS can be output safely.
 */
function plc_is_safe_custom_css( $css ) {
    return is_string( $css ) && ! preg_match( '#</style\b#i', $css );
}

/**
 * Sanitize custom CSS submitted through the settings form.
 *
 * Custom CSS is code rather than HTML, so HTML tag stripping would corrupt
 * valid CSS. Only users with the unfiltered_html capability may save it.
 *
 * @param string $css Raw CSS.
 * @return string Safe CSS, or an empty string when it is not permitted/valid.
 */
function plc_sanitize_custom_css( $css ) {
    $css = is_string( $css ) ? wp_unslash( $css ) : '';
    $css = wp_check_invalid_utf8( $css );

    if ( ! current_user_can( 'unfiltered_html' ) ) {
        add_settings_error(
            'plc_settings',
            'plc_custom_css_not_allowed',
            __( 'You are not allowed to save custom CSS.', 'pro-login-customizer' )
        );

        return '';
    }

    if ( ! plc_is_safe_custom_css( $css ) ) {
        add_settings_error(
            'plc_settings',
            'plc_invalid_custom_css',
            __( 'Custom CSS cannot contain a closing style tag.', 'pro-login-customizer' )
        );

        return '';
    }

    return trim( $css );
}

/**
 * Enqueue Admin Assets
 *
 * @param string $hook_suffix Current admin page hook.
 */
function plc_admin_enqueue_scripts( $hook_suffix ) {
    if ( 'toplevel_page_pro-login-customizer' !== $hook_suffix ) {
        return;
    }

    // WP Media library
    wp_enqueue_media();

    // WP Color Picker
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    // Admin CSS & JS
    wp_enqueue_style(
        'plc-admin-style',
        PLC_URL . 'css/plc-admin.css',
        [ 'wp-color-picker' ],
        PLC_VERSION
    );

    wp_enqueue_script(
        'plc-admin-script',
        PLC_URL . 'js/plc-admin.js',
        [ 'jquery', 'wp-color-picker' ],
        PLC_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'plc_admin_enqueue_scripts' );

/**
 * Admin Page Content
 */
function plc_admin_page_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings  = plc_get_settings();
    $login_url = wp_login_url();
    ?>

    <div class="wrap plc-admin-wrap">

        <?php settings_errors( 'plc_settings' ); ?>

        <!-- Header Banner -->
        <div class="plc-header-banner">
            <div class="plc-header-title">
                <span class="plc-logo-icon dashicons dashicons-lock"></span>
                <div>
                    <h1>
                        <?php esc_html_e( 'Pro Login Customizer', 'pro-login-customizer' ); ?>
                        <span class="plc-badge">v<?php echo esc_html( PLC_VERSION ); ?></span>
                    </h1>
                    <p><?php esc_html_e( 'Craft stunning, modern animated login pages for WordPress.', 'pro-login-customizer' ); ?></p>
                </div>
            </div>
            <div class="plc-header-actions">
                <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="plc-btn-preview">
                    <span class="dashicons dashicons-external"></span>
                    <?php esc_html_e( 'Preview Login Screen', 'pro-login-customizer' ); ?>
                </a>
            </div>
        </div>

        <div class="plc-dashboard-grid">

            <!-- Left Main Column (Settings Form) -->
            <div class="plc-main-col">
                <form action="options.php" method="post" id="plc-settings-form">
                    <?php settings_fields( 'plc_settings_group' ); ?>

                    <div class="plc-main-card">
                        <!-- Navigation Tabs -->
                        <div class="plc-nav-tabs">
                            <a href="#branding" class="plc-nav-tab active" data-tab="branding">
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e( 'Logo & Branding', 'pro-login-customizer' ); ?>
                            </a>
                            <a href="#background" class="plc-nav-tab" data-tab="background">
                                <span class="dashicons dashicons-format-image"></span>
                                <?php esc_html_e( 'Background & Aurora', 'pro-login-customizer' ); ?>
                            </a>
                            <a href="#card-colors" class="plc-nav-tab" data-tab="card-colors">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <?php esc_html_e( 'Form & Colors', 'pro-login-customizer' ); ?>
                            </a>
                            <a href="#custom-css" class="plc-nav-tab" data-tab="custom-css">
                                <span class="dashicons dashicons-editor-code"></span>
                                <?php esc_html_e( 'Custom CSS', 'pro-login-customizer' ); ?>
                            </a>
                        </div>

                        <!-- Tab 1: Logo & Branding -->
                        <div class="plc-tab-content active" id="plc-tab-branding">
                            <div class="plc-section-header">
                                <h3><?php esc_html_e( 'Logo & Header Settings', 'pro-login-customizer' ); ?></h3>
                                <p><?php esc_html_e( 'Upload your custom brand logo and set dimensions.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-form-row">
                                <label for="plc_logo"><?php esc_html_e( 'Logo Image', 'pro-login-customizer' ); ?></label>
                                <div class="plc-input-group">
                                    <input
                                        type="url"
                                        id="plc_logo"
                                        name="plc_settings[logo]"
                                        value="<?php echo esc_attr( $settings['logo'] ); ?>"
                                        placeholder="https://example.com/logo.png"
                                    >
                                    <button type="button" class="button plc-upload-btn" data-target="plc_logo" data-preview="plc_logo_preview">
                                        <span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Upload Logo', 'pro-login-customizer' ); ?>
                                    </button>
                                </div>
                                <div class="plc-image-preview" id="plc_logo_preview" data-input="plc_logo" <?php echo empty( $settings['logo'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php if ( ! empty( $settings['logo'] ) ) : ?>
                                        <img src="<?php echo esc_url( $settings['logo'] ); ?>" alt="Logo Preview">
                                        <button type="button" class="plc-remove-btn dashicons dashicons-no-alt" title="<?php esc_attr_e( 'Remove image', 'pro-login-customizer' ); ?>"></button>
                                    <?php endif; ?>
                                </div>
                                <p class="description"><?php esc_html_e( 'Recommended format: Transparent PNG or SVG.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-form-row" style="display: flex; gap: 20px; max-width: 500px;">
                                <div style="flex: 1;">
                                    <label for="plc_logo_width"><?php esc_html_e( 'Logo Width (px)', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="number"
                                        id="plc_logo_width"
                                        name="plc_settings[logo_width]"
                                        value="<?php echo esc_attr( $settings['logo_width'] ); ?>"
                                        min="20"
                                        max="400"
                                    >
                                </div>
                                <div style="flex: 1;">
                                    <label for="plc_logo_height"><?php esc_html_e( 'Logo Height (px)', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="number"
                                        id="plc_logo_height"
                                        name="plc_settings[logo_height]"
                                        value="<?php echo esc_attr( $settings['logo_height'] ); ?>"
                                        min="20"
                                        max="300"
                                    >
                                </div>
                            </div>

                            <div class="plc-form-row">
                                <label for="plc_form_subtitle"><?php esc_html_e( 'Form Subtitle Text', 'pro-login-customizer' ); ?></label>
                                <input
                                    type="text"
                                    id="plc_form_subtitle"
                                    name="plc_settings[form_subtitle]"
                                    value="<?php echo esc_attr( $settings['form_subtitle'] ); ?>"
                                    placeholder="<?php esc_attr_e( 'Sign in to your account', 'pro-login-customizer' ); ?>"
                                >
                                <p class="description"><?php esc_html_e( 'Heading displayed right below your logo. Leave blank to hide.', 'pro-login-customizer' ); ?></p>
                            </div>
                        </div>

                        <!-- Tab 2: Background & Aurora Animation -->
                        <div class="plc-tab-content" id="plc-tab-background">
                            <div class="plc-section-header">
                                <h3><?php esc_html_e( 'Background & Aurora Flow Gradient', 'pro-login-customizer' ); ?></h3>
                                <p><?php esc_html_e( 'Configure your login page background image or dynamic animated Aurora gradient.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <!-- Background Image Option -->
                            <div class="plc-form-row">
                                <label for="plc_bg_image"><?php esc_html_e( 'Background Image', 'pro-login-customizer' ); ?></label>
                                <div class="plc-input-group">
                                    <input
                                        type="url"
                                        id="plc_bg_image"
                                        name="plc_settings[bg_image]"
                                        value="<?php echo esc_attr( $settings['bg_image'] ); ?>"
                                        placeholder="https://example.com/background.jpg"
                                    >
                                    <button type="button" class="button plc-upload-btn" data-target="plc_bg_image" data-preview="plc_bg_preview">
                                        <span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Upload Image', 'pro-login-customizer' ); ?>
                                    </button>
                                </div>
                                <div class="plc-image-preview" id="plc_bg_preview" data-input="plc_bg_image" <?php echo empty( $settings['bg_image'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php if ( ! empty( $settings['bg_image'] ) ) : ?>
                                        <img src="<?php echo esc_url( $settings['bg_image'] ); ?>" alt="Background Preview">
                                        <button type="button" class="plc-remove-btn dashicons dashicons-no-alt" title="<?php esc_attr_e( 'Remove image', 'pro-login-customizer' ); ?>"></button>
                                    <?php endif; ?>
                                </div>
                                <p class="description"><?php esc_html_e( 'If a background image is provided, it will take precedence. If left empty, the animated Aurora gradient below will be used.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-notice-box plc-bg-image-notice" <?php echo empty( $settings['bg_image'] ) ? 'style="display:none;"' : ''; ?>>
                                <span class="dashicons dashicons-info"></span>
                                <div>
                                    <strong><?php esc_html_e( 'Background Image Active:', 'pro-login-customizer' ); ?></strong>
                                    <?php esc_html_e( 'A background image is currently set. The Aurora gradient animation will remain dormant until the background image URL is cleared.', 'pro-login-customizer' ); ?>
                                </div>
                            </div>

                            <!-- Aurora Animation Configuration -->
                            <div class="plc-aurora-settings-group">
                                <div class="plc-section-header" style="margin-top: 20px;">
                                    <h3><?php esc_html_e( 'Aurora Animated Gradient Palette', 'pro-login-customizer' ); ?></h3>
                                    <p><?php esc_html_e( 'Customize the 4 harmonic color stops for the fluid background animation.', 'pro-login-customizer' ); ?></p>
                                </div>

                                <div class="plc-form-row">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input
                                            type="checkbox"
                                            name="plc_settings[aurora_enable]"
                                            value="1"
                                            <?php checked( $settings['aurora_enable'], '1' ); ?>
                                        >
                                        <strong><?php esc_html_e( 'Enable Animated Aurora Flow Gradient', 'pro-login-customizer' ); ?></strong>
                                    </label>
                                </div>

                                <div class="plc-color-grid">
                                    <div class="plc-color-item">
                                        <label for="plc_bg_color"><?php esc_html_e( 'Base / Stop 1 Color', 'pro-login-customizer' ); ?></label>
                                        <input
                                            type="text"
                                            class="plc-color-picker"
                                            id="plc_bg_color"
                                            name="plc_settings[bg_color]"
                                            value="<?php echo esc_attr( $settings['bg_color'] ); ?>"
                                        >
                                    </div>
                                    <div class="plc-color-item">
                                        <label for="plc_aurora_2"><?php esc_html_e( 'Aurora Stop 2', 'pro-login-customizer' ); ?></label>
                                        <input
                                            type="text"
                                            class="plc-color-picker"
                                            id="plc_aurora_2"
                                            name="plc_settings[aurora_color_2]"
                                            value="<?php echo esc_attr( $settings['aurora_color_2'] ); ?>"
                                        >
                                    </div>
                                    <div class="plc-color-item">
                                        <label for="plc_aurora_3"><?php esc_html_e( 'Aurora Stop 3', 'pro-login-customizer' ); ?></label>
                                        <input
                                            type="text"
                                            class="plc-color-picker"
                                            id="plc_aurora_3"
                                            name="plc_settings[aurora_color_3]"
                                            value="<?php echo esc_attr( $settings['aurora_color_3'] ); ?>"
                                        >
                                    </div>
                                    <div class="plc-color-item">
                                        <label for="plc_aurora_4"><?php esc_html_e( 'Aurora Stop 4', 'pro-login-customizer' ); ?></label>
                                        <input
                                            type="text"
                                            class="plc-color-picker"
                                            id="plc_aurora_4"
                                            name="plc_settings[aurora_color_4]"
                                            value="<?php echo esc_attr( $settings['aurora_color_4'] ); ?>"
                                        >
                                    </div>
                                </div>

                                <div class="plc-form-row">
                                    <label for="plc_aurora_speed"><?php esc_html_e( 'Animation Cycle Duration (seconds)', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="number"
                                        id="plc_aurora_speed"
                                        name="plc_settings[aurora_speed]"
                                        value="<?php echo esc_attr( $settings['aurora_speed'] ); ?>"
                                        min="3"
                                        max="60"
                                        style="max-width: 150px;"
                                    >
                                    <p class="description"><?php esc_html_e( 'Duration of one full continuous animation cycle (default: 15s). Lower numbers make it faster.', 'pro-login-customizer' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Form & Colors -->
                        <div class="plc-tab-content" id="plc-tab-card-colors">
                            <div class="plc-section-header">
                                <h3><?php esc_html_e( 'Form Colors & Glass Styling', 'pro-login-customizer' ); ?></h3>
                                <p><?php esc_html_e( 'Control button highlights, text colors, and glassmorphism container parameters.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-color-grid">
                                <div class="plc-color-item">
                                    <label for="plc_primary_color"><?php esc_html_e( 'Primary Button / Accent', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="text"
                                        class="plc-color-picker"
                                        id="plc_primary_color"
                                        name="plc_settings[primary_color]"
                                        value="<?php echo esc_attr( $settings['primary_color'] ); ?>"
                                    >
                                </div>
                                <div class="plc-color-item">
                                    <label for="plc_hover_color"><?php esc_html_e( 'Button Hover Color', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="text"
                                        class="plc-color-picker"
                                        id="plc_hover_color"
                                        name="plc_settings[hover_color]"
                                        value="<?php echo esc_attr( $settings['hover_color'] ); ?>"
                                    >
                                </div>
                                <div class="plc-color-item">
                                    <label for="plc_text_color"><?php esc_html_e( 'Form Text Color', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="text"
                                        class="plc-color-picker"
                                        id="plc_text_color"
                                        name="plc_settings[text_color]"
                                        value="<?php echo esc_attr( $settings['text_color'] ); ?>"
                                    >
                                </div>
                            </div>

                            <div class="plc-section-header" style="margin-top: 20px;">
                                <h3><?php esc_html_e( 'Glassmorphism Card Settings', 'pro-login-customizer' ); ?></h3>
                            </div>

                            <div class="plc-form-row">
                                <label for="plc_card_bg_color"><?php esc_html_e( 'Card Background (RGBA / HEX)', 'pro-login-customizer' ); ?></label>
                                <input
                                    type="text"
                                    id="plc_card_bg_color"
                                    name="plc_settings[card_bg_color]"
                                    value="<?php echo esc_attr( $settings['card_bg_color'] ); ?>"
                                    placeholder="rgba(255, 255, 255, 0.45)"
                                >
                                <p class="description"><?php esc_html_e( 'Supports CSS RGBA transparent values (e.g. rgba(255, 255, 255, 0.45) for frosted glass).', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-form-row" style="display: flex; gap: 20px; max-width: 500px;">
                                <div style="flex: 1;">
                                    <label for="plc_card_blur"><?php esc_html_e( 'Backdrop Blur (px)', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="number"
                                        id="plc_card_blur"
                                        name="plc_settings[card_blur]"
                                        value="<?php echo esc_attr( $settings['card_blur'] ); ?>"
                                        min="0"
                                        max="60"
                                    >
                                </div>
                                <div style="flex: 1;">
                                    <label for="plc_border_radius"><?php esc_html_e( 'Border Radius (px)', 'pro-login-customizer' ); ?></label>
                                    <input
                                        type="number"
                                        id="plc_border_radius"
                                        name="plc_settings[border_radius]"
                                        value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
                                        min="0"
                                        max="50"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Custom CSS -->
                        <div class="plc-tab-content" id="plc-tab-custom-css">
                            <div class="plc-section-header">
                                <h3><?php esc_html_e( 'Custom CSS', 'pro-login-customizer' ); ?></h3>
                                <p><?php esc_html_e( 'Add any additional custom CSS rules to fine-tune your login screen.', 'pro-login-customizer' ); ?></p>
                            </div>

                            <div class="plc-form-row">
                                <textarea
                                    id="plc_custom_css"
                                    name="plc_settings[custom_css]"
                                    rows="10"
                                    placeholder="/* Add custom CSS rules here */"
                                ><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>
                            </div>
                        </div>

                        <!-- Submit Bar -->
                        <div class="plc-submit-bar">
                            <?php submit_button( __( 'Save All Changes', 'pro-login-customizer' ), 'primary', 'submit', false ); ?>
                            <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="button button-secondary">
                                <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span>
                                <?php esc_html_e( 'Test Login Screen', 'pro-login-customizer' ); ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Sidebar Column (Plugin Details & Author) -->
            <div class="plc-sidebar-col">

                <!-- Plugin Info Widget -->
                <div class="plc-side-widget">
                    <h4>
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <?php esc_html_e( 'Plugin Information', 'pro-login-customizer' ); ?>
                    </h4>
                    <ul class="plc-info-list">
                        <li>
                            <span><?php esc_html_e( 'Plugin Version:', 'pro-login-customizer' ); ?></span>
                            <strong>v<?php echo esc_html( PLC_VERSION ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'Status:', 'pro-login-customizer' ); ?></span>
                            <strong style="color: #10b981;"><?php esc_html_e( 'Active & Ready', 'pro-login-customizer' ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'WordPress Core:', 'pro-login-customizer' ); ?></span>
                            <strong><?php echo esc_html( get_bloginfo( 'version' ) ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'PHP Version:', 'pro-login-customizer' ); ?></span>
                            <strong><?php echo esc_html( phpversion() ); ?></strong>
                        </li>
                    </ul>
                </div>

                <!-- Author & Developer Widget -->
                <div class="plc-side-widget">
                    <h4>
                        <span class="dashicons dashicons-businessman"></span>
                        <?php esc_html_e( 'Plugin Author', 'pro-login-customizer' ); ?>
                    </h4>
                    <div class="plc-author-card">
                        <div class="plc-author-avatar">JH</div>
                        <div class="plc-author-info">
                            <h5>Jahid Hasan</h5>
                            <span>WordPress & Web Developer</span>
                        </div>
                    </div>
                    <ul class="plc-side-links">
                        <li>
                            <a href="https://github.com/jahid-we" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-admin-site" style="margin-right: 4px;"></span> <?php esc_html_e( 'Author GitHub Profile', 'pro-login-customizer' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/jahid-we/pro_login_customizer" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-star-filled" style="margin-right: 4px; color: #f59e0b;"></span> <?php esc_html_e( 'Star on GitHub', 'pro-login-customizer' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wordpress.org/support/plugin/pro-login-customizer/reviews/#new-post" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-thumbs-up" style="margin-right: 4px; color: #10b981;"></span> <?php esc_html_e( 'Rate 5 Stars', 'pro-login-customizer' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wordpress.org/support/plugin/pro-login-customizer/" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-sos" style="margin-right: 4px; color: #3b82f6;"></span> <?php esc_html_e( 'Support & Feedback', 'pro-login-customizer' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Live Preview Quick Action -->
                <div class="plc-side-widget" style="background: linear-gradient(135deg, #1e1b4b, #312e81); color: #ffffff;">
                    <h4 style="color: #ffffff; border-bottom-color: rgba(255,255,255,0.15);">
                        <span class="dashicons dashicons-welcome-view-site" style="color: #818cf8;"></span>
                        <?php esc_html_e( 'Quick Preview', 'pro-login-customizer' ); ?>
                    </h4>
                    <p style="font-size: 13px; color: #cbd5e1; margin-bottom: 14px;">
                        <?php esc_html_e( 'Want to see your changes live? Open your login page in a private/incognito tab or preview now.', 'pro-login-customizer' ); ?>
                    </p>
                    <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="button" style="display: block; text-align: center; background: #818cf8; color: #ffffff; border: none; font-weight: 600; padding: 6px 0;">
                        <?php esc_html_e( 'Open Login Page', 'pro-login-customizer' ); ?>
                    </a>
                </div>

            </div>

        </div>

    </div>

    <?php
}

/**
 * Filter Login Body Classes
 *
 * @param array $classes Array of login body classes.
 * @return array
 */
function plc_login_body_class( $classes ) {
    $settings = plc_get_settings();

    if ( ! empty( $settings['bg_image'] ) ) {
        $classes[] = 'plc-has-bg-image';
    } elseif ( ! empty( $settings['aurora_enable'] ) && '1' === $settings['aurora_enable'] ) {
        $classes[] = 'plc-aurora-active';
    }

    return $classes;
}
add_filter( 'login_body_class', 'plc_login_body_class' );

/**
 * Enqueue Login Page CSS & Dynamic Inline Styles
 */
function plc_login_enqueue_style() {
    $settings = plc_get_settings();

    wp_enqueue_style(
        'plc-login-style',
        PLC_URL . 'css/plc-style.css',
        [],
        PLC_VERSION
    );

    // Calculate dynamic values
    $logo_url      = ! empty( $settings['logo'] ) ? esc_url( $settings['logo'] ) : PLC_URL . 'images/logo.png';
    $logo_width    = ! empty( $settings['logo_width'] ) ? absint( $settings['logo_width'] ) . 'px' : '160px';
    $logo_height   = ! empty( $settings['logo_height'] ) ? absint( $settings['logo_height'] ) . 'px' : '50px';
    $primary_color = ! empty( $settings['primary_color'] ) ? sanitize_hex_color( $settings['primary_color'] ) : '#6366f1';
    $hover_color   = ! empty( $settings['hover_color'] ) ? sanitize_hex_color( $settings['hover_color'] ) : '#4f46e5';
    $text_color    = ! empty( $settings['text_color'] ) ? sanitize_hex_color( $settings['text_color'] ) : '#0f172a';
    $bg_color      = ! empty( $settings['bg_color'] ) ? sanitize_hex_color( $settings['bg_color'] ) : '#030712';
    $aurora_1      = ! empty( $settings['aurora_color_1'] ) ? sanitize_hex_color( $settings['aurora_color_1'] ) : $bg_color;
    $aurora_2      = ! empty( $settings['aurora_color_2'] ) ? sanitize_hex_color( $settings['aurora_color_2'] ) : '#1e1b4b';
    $aurora_3      = ! empty( $settings['aurora_color_3'] ) ? sanitize_hex_color( $settings['aurora_color_3'] ) : '#0284c7';
    $aurora_4      = ! empty( $settings['aurora_color_4'] ) ? sanitize_hex_color( $settings['aurora_color_4'] ) : '#4f46e5';
    $aurora_speed  = ! empty( $settings['aurora_speed'] ) ? absint( $settings['aurora_speed'] ) . 's' : '15s';
    $card_bg       = ! empty( $settings['card_bg_color'] ) ? esc_attr( $settings['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $card_blur     = ! empty( $settings['card_blur'] ) ? absint( $settings['card_blur'] ) . 'px' : '28px';
    $border_radius = ! empty( $settings['border_radius'] ) ? absint( $settings['border_radius'] ) . 'px' : '24px';

    $custom_css = "
        :root {
            --plc-primary: {$primary_color};
            --plc-primary-hover: {$hover_color};
            --plc-text: {$text_color};
            --plc-bg-color: {$bg_color};
            --plc-aurora-1: {$aurora_1};
            --plc-aurora-2: {$aurora_2};
            --plc-aurora-3: {$aurora_3};
            --plc-aurora-4: {$aurora_4};
            --plc-aurora-speed: {$aurora_speed};
            --plc-card-bg: {$card_bg};
            --plc-card-blur: {$card_blur};
            --plc-radius: {$border_radius};
            --plc-logo: url('{$logo_url}');
            --plc-logo-width: {$logo_width};
            --plc-logo-height: {$logo_height};
        }
    ";

    // If background image is present, add variable
    if ( ! empty( $settings['bg_image'] ) ) {
        $bg_img_url = esc_url( $settings['bg_image'] );
        $custom_css .= "
            :root {
                --plc-bg-img: url('{$bg_img_url}');
            }
        ";
    }

    // Append user custom CSS
    if ( ! empty( $settings['custom_css'] ) && plc_is_safe_custom_css( $settings['custom_css'] ) ) {
        $custom_css .= "\n" . $settings['custom_css'];
    }

    wp_add_inline_style( 'plc-login-style', $custom_css );
}
add_action( 'login_enqueue_scripts', 'plc_login_enqueue_style' );

/**
 * Change Login Logo URL to Site Home
 */
function plc_login_logo_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'plc_login_logo_url' );

/**
 * Change Login Logo Title / Header Text
 */
function plc_login_logo_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'plc_login_logo_title' );

/**
 * Render Custom Subtitle below logo
 *
 * @param string $message Existing login header message.
 * @return string
 */
function plc_login_custom_subtitle( $message ) {
    $settings = plc_get_settings();

    if ( ! empty( $settings['form_subtitle'] ) ) {
        $subtitle_html = sprintf(
            '<div class="plc-custom-subtitle">%s</div>',
            esc_html( $settings['form_subtitle'] )
        );
        $message = $subtitle_html . $message;
    }

    return $message;
}
add_filter( 'login_message', 'plc_login_custom_subtitle' );
