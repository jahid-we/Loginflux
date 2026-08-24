<?php
/**
 * Plugin Name:       Loginflux
 * Plugin URI:        https://github.com/jahid-we/Loginflux
 * Description:       Transform your login page with animated visual effects, dynamic backgrounds, glassmorphism, custom branding, and modern color controls.
 * Version:           1.1.0
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
define( 'LOGINFLUX_VERSION', '1.1.0' );
define( 'LOGINFLUX_URL', plugin_dir_url( __FILE__ ) );
define( 'LOGINFLUX_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOGINFLUX_BASENAME', plugin_basename( __FILE__ ) );



/**
 * Get Default Plugin Settings
 *
 * @return array
 */
function jzlf_get_default_settings() {
    return [
        'logo'             => LOGINFLUX_URL . 'images/logo.png',
        'logo_width'       => '160',
        'logo_height'      => '50',
        'form_subtitle'    => __( 'Sign in to your account', 'loginflux' ),
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
    ];
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
register_activation_hook( __FILE__, 'jzlf_activate' );

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

/**
 * Get Plugin Settings merged with defaults (with safe backward-compatible migration)
 *
 * @return array
 */
function jzlf_get_settings() {
    $defaults = jzlf_get_default_settings();
    $settings = get_option( 'loginflux_settings', [] );

    if ( ! is_array( $settings ) ) {
        $settings = [];
    }

    return wp_parse_args( $settings, $defaults );
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

/**
 * Register Settings
 */
function jzlf_register_settings() {
    register_setting(
        'loginflux_settings_group',
        'loginflux_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'jzlf_sanitize_settings',
            'default'           => jzlf_get_default_settings(),
        ]
    );
}
add_action( 'admin_init', 'jzlf_register_settings' );

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

/**
 * Sanitize Settings
 *
 * @param array $input Raw form input.
 * @return array Sanitized settings.
 */
function jzlf_sanitize_settings( $input ) {
    $sanitized = [];

    if ( ! is_array( $input ) ) {
        return $sanitized;
    }

    // Logo & Branding
    $sanitized['logo']          = isset( $input['logo'] ) ? esc_url_raw( $input['logo'] ) : '';
    $sanitized['logo_width']    = isset( $input['logo_width'] ) ? absint( $input['logo_width'] ) : 160;
    $sanitized['logo_height']   = isset( $input['logo_height'] ) ? absint( $input['logo_height'] ) : 50;
    $sanitized['form_subtitle'] = isset( $input['form_subtitle'] ) ? sanitize_text_field( $input['form_subtitle'] ) : '';

    // Background & Visual Effects
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

    return $sanitized;
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

/**
 * Admin Page Content
 */
function jzlf_admin_page_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings  = jzlf_get_settings();
    $login_url = wp_login_url();
    ?>

    <div class="wrap loginflux-admin-wrap">

        <?php settings_errors( 'loginflux_settings' ); ?>
        <?php
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading query parameter for flash notice display only.
        if ( isset( $_GET['loginflux_settings_reset'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['loginflux_settings_reset'] ) ) ) :
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( 'Settings have been restored to their default values.', 'loginflux' ); ?></p>
            </div>
        <?php endif; ?>

        <!-- Header Banner -->
        <div class="loginflux-header-banner">
            <div class="loginflux-header-title">
                <span class="loginflux-logo-icon dashicons dashicons-lock"></span>
                <div>
                    <h1>
                        <?php esc_html_e( 'Loginflux', 'loginflux' ); ?>
                        <span class="loginflux-badge">v<?php echo esc_html( LOGINFLUX_VERSION ); ?></span>
                    </h1>
                    <p><?php esc_html_e( 'Transform your login page with animated visual effects, dynamic backgrounds, and custom styling.', 'loginflux' ); ?></p>
                </div>
            </div>
            <div class="loginflux-header-actions">
                <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="loginflux-btn-preview">
                    <span class="dashicons dashicons-external"></span>
                    <?php esc_html_e( 'Preview Login Screen', 'loginflux' ); ?>
                </a>
            </div>
        </div>

        <div class="loginflux-dashboard-grid">

            <!-- Left Main Column (Settings Form) -->
            <div class="loginflux-main-col">
                <form action="options.php" method="post" id="loginflux-settings-form">
                    <?php settings_fields( 'loginflux_settings_group' ); ?>
                    <?php wp_nonce_field( 'jzlf_reset_settings', 'jzlf_reset_settings_nonce' ); ?>

                    <div class="loginflux-main-card">
                        <!-- Navigation Tabs -->
                        <div class="loginflux-nav-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Loginflux customizer sections', 'loginflux' ); ?>">
                            <a href="#branding" class="loginflux-nav-tab active" id="loginflux-nav-branding" data-tab="branding" role="tab" aria-controls="loginflux-tab-branding" aria-selected="true" tabindex="0">
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e( 'Logo & Branding', 'loginflux' ); ?>
                            </a>
                            <a href="#background" class="loginflux-nav-tab" id="loginflux-nav-background" data-tab="background" role="tab" aria-controls="loginflux-tab-background" aria-selected="false" tabindex="-1">
                                <span class="dashicons dashicons-format-image"></span>
                                <?php esc_html_e( 'Background & Effects', 'loginflux' ); ?>
                            </a>
                            <a href="#card-colors" class="loginflux-nav-tab" id="loginflux-nav-card-colors" data-tab="card-colors" role="tab" aria-controls="loginflux-tab-card-colors" aria-selected="false" tabindex="-1">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <?php esc_html_e( 'Form & Colors', 'loginflux' ); ?>
                            </a>
                        </div>

                        <!-- Tab 1: Logo & Branding -->
                        <div class="loginflux-tab-content active" id="loginflux-tab-branding" role="tabpanel" aria-labelledby="loginflux-nav-branding" aria-hidden="false" tabindex="0">
                            <div class="loginflux-section-header">
                                <h3><?php esc_html_e( 'Logo & Header Settings', 'loginflux' ); ?></h3>
                                <p><?php esc_html_e( 'Upload your custom brand logo and set dimensions.', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-form-row">
                                <label for="loginflux_logo"><?php esc_html_e( 'Logo Image', 'loginflux' ); ?></label>
                                <div class="loginflux-input-group">
                                    <input
                                        type="url"
                                        id="loginflux_logo"
                                        name="loginflux_settings[logo]"
                                        value="<?php echo esc_attr( $settings['logo'] ); ?>"
                                        placeholder="https://example.com/logo.png"
                                    >
                                    <button type="button" class="button loginflux-upload-btn" data-target="loginflux_logo" data-preview="loginflux_logo_preview">
                                        <span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Upload Logo', 'loginflux' ); ?>
                                    </button>
                                </div>
                                <div class="loginflux-image-preview" id="loginflux_logo_preview" data-input="loginflux_logo" <?php echo empty( $settings['logo'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php if ( ! empty( $settings['logo'] ) ) : ?>
                                        <img src="<?php echo esc_url( $settings['logo'] ); ?>" alt="Logo Preview">
                                        <button type="button" class="loginflux-remove-btn dashicons dashicons-no-alt" title="<?php esc_attr_e( 'Remove image', 'loginflux' ); ?>"></button>
                                    <?php endif; ?>
                                </div>
                                <p class="description"><?php esc_html_e( 'Recommended format: Transparent PNG or SVG.', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-form-row loginflux-inline-fields">
                                <div style="flex: 1;">
                                    <label for="loginflux_logo_width"><?php esc_html_e( 'Logo Width (px)', 'loginflux' ); ?></label>
                                    <input
                                        type="number"
                                        id="loginflux_logo_width"
                                        name="loginflux_settings[logo_width]"
                                        value="<?php echo esc_attr( $settings['logo_width'] ); ?>"
                                        min="20"
                                        max="400"
                                    >
                                </div>
                                <div style="flex: 1;">
                                    <label for="loginflux_logo_height"><?php esc_html_e( 'Logo Height (px)', 'loginflux' ); ?></label>
                                    <input
                                        type="number"
                                        id="loginflux_logo_height"
                                        name="loginflux_settings[logo_height]"
                                        value="<?php echo esc_attr( $settings['logo_height'] ); ?>"
                                        min="20"
                                        max="300"
                                    >
                                </div>
                            </div>

                            <div class="loginflux-form-row">
                                <label for="loginflux_form_subtitle"><?php esc_html_e( 'Form Subtitle Text', 'loginflux' ); ?></label>
                                <input
                                    type="text"
                                    id="loginflux_form_subtitle"
                                    name="loginflux_settings[form_subtitle]"
                                    value="<?php echo esc_attr( $settings['form_subtitle'] ); ?>"
                                    placeholder="<?php esc_attr_e( 'Sign in to your account', 'loginflux' ); ?>"
                                >
                                <p class="description"><?php esc_html_e( 'Heading displayed right below your logo. Leave blank to hide.', 'loginflux' ); ?></p>
                            </div>
                        </div>

                        <!-- Tab 2: Background & Visual Animation -->
                        <div class="loginflux-tab-content" id="loginflux-tab-background" role="tabpanel" aria-labelledby="loginflux-nav-background" aria-hidden="true" tabindex="0">
                            <div class="loginflux-section-header">
                                <h3><?php esc_html_e( 'Background & Dynamic Visual Animation', 'loginflux' ); ?></h3>
                                <p><?php esc_html_e( 'Configure your login page background image or animated visual effects.', 'loginflux' ); ?></p>
                            </div>

                            <!-- Background Image Option -->
                            <div class="loginflux-form-row">
                                <label for="loginflux_bg_image"><?php esc_html_e( 'Background Image', 'loginflux' ); ?></label>
                                <div class="loginflux-input-group">
                                    <input
                                        type="url"
                                        id="loginflux_bg_image"
                                        name="loginflux_settings[bg_image]"
                                        value="<?php echo esc_attr( $settings['bg_image'] ); ?>"
                                        placeholder="https://example.com/background.jpg"
                                    >
                                    <button type="button" class="button loginflux-upload-btn" data-target="loginflux_bg_image" data-preview="loginflux_bg_preview">
                                        <span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Upload Image', 'loginflux' ); ?>
                                    </button>
                                </div>
                                <div class="loginflux-image-preview" id="loginflux_bg_preview" data-input="loginflux_bg_image" <?php echo empty( $settings['bg_image'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php if ( ! empty( $settings['bg_image'] ) ) : ?>
                                        <img src="<?php echo esc_url( $settings['bg_image'] ); ?>" alt="Background Preview">
                                        <button type="button" class="loginflux-remove-btn dashicons dashicons-no-alt" title="<?php esc_attr_e( 'Remove image', 'loginflux' ); ?>"></button>
                                    <?php endif; ?>
                                </div>
                                <p class="description"><?php esc_html_e( 'If a background image is provided, it will take precedence. If left empty, the animated visual gradient below will be used.', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-notice-box loginflux-bg-image-notice" <?php echo empty( $settings['bg_image'] ) ? 'style="display:none;"' : ''; ?>>
                                <span class="dashicons dashicons-info"></span>
                                <div>
                                    <strong><?php esc_html_e( 'Background Image Active:', 'loginflux' ); ?></strong>
                                    <?php esc_html_e( 'A background image is currently set. The animated gradient will remain dormant until the background image URL is cleared.', 'loginflux' ); ?>
                                </div>
                            </div>

                            <!-- Animated Flow Configuration -->
                            <div class="loginflux-aurora-settings-group">
                                <div class="loginflux-section-header" style="margin-top: 20px;">
                                    <h3><?php esc_html_e( 'Animated Flow Gradient Palette', 'loginflux' ); ?></h3>
                                    <p><?php esc_html_e( 'Customize the harmonic color stops for the fluid animated background.', 'loginflux' ); ?></p>
                                    <p class="loginflux-aurora-help-text"><?php esc_html_e( 'To use the animated gradient flow, leave the Background Image field empty. A background image takes precedence over the animation.', 'loginflux' ); ?></p>
                                </div>

                                <div class="loginflux-form-row">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input
                                            type="checkbox"
                                            name="loginflux_settings[aurora_enable]"
                                            value="1"
                                            <?php checked( $settings['aurora_enable'], '1' ); ?>
                                        >
                                        <strong><?php esc_html_e( 'Enable Animated Flow Gradient', 'loginflux' ); ?></strong>
                                    </label>
                                </div>

                                <div class="loginflux-color-grid">
                                    <div class="loginflux-color-item">
                                        <label for="loginflux_bg_color"><?php esc_html_e( 'Base / Stop 1 Color', 'loginflux' ); ?></label>
                                        <input
                                            type="text"
                                            class="loginflux-color-picker"
                                            id="loginflux_bg_color"
                                            name="loginflux_settings[bg_color]"
                                            value="<?php echo esc_attr( $settings['bg_color'] ); ?>"
                                        >
                                    </div>
                                    <div class="loginflux-color-item">
                                        <label for="loginflux_aurora_2"><?php esc_html_e( 'Gradient Stop 2', 'loginflux' ); ?></label>
                                        <input
                                            type="text"
                                            class="loginflux-color-picker"
                                            id="loginflux_aurora_2"
                                            name="loginflux_settings[aurora_color_2]"
                                            value="<?php echo esc_attr( $settings['aurora_color_2'] ); ?>"
                                        >
                                    </div>
                                    <div class="loginflux-color-item">
                                        <label for="loginflux_aurora_3"><?php esc_html_e( 'Gradient Stop 3', 'loginflux' ); ?></label>
                                        <input
                                            type="text"
                                            class="loginflux-color-picker"
                                            id="loginflux_aurora_3"
                                            name="loginflux_settings[aurora_color_3]"
                                            value="<?php echo esc_attr( $settings['aurora_color_3'] ); ?>"
                                        >
                                    </div>
                                    <div class="loginflux-color-item">
                                        <label for="loginflux_aurora_4"><?php esc_html_e( 'Gradient Stop 4', 'loginflux' ); ?></label>
                                        <input
                                            type="text"
                                            class="loginflux-color-picker"
                                            id="loginflux_aurora_4"
                                            name="loginflux_settings[aurora_color_4]"
                                            value="<?php echo esc_attr( $settings['aurora_color_4'] ); ?>"
                                        >
                                    </div>
                                </div>

                                <div class="loginflux-form-row">
                                    <label for="loginflux_aurora_speed"><?php esc_html_e( 'Animation Cycle Duration (seconds)', 'loginflux' ); ?></label>
                                    <input
                                        type="number"
                                        id="loginflux_aurora_speed"
                                        name="loginflux_settings[aurora_speed]"
                                        value="<?php echo esc_attr( $settings['aurora_speed'] ); ?>"
                                        min="3"
                                        max="60"
                                        style="max-width: 150px;"
                                    >
                                    <p class="description"><?php esc_html_e( 'Duration of one full continuous animation cycle (default: 15s). Lower numbers make it faster.', 'loginflux' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Form & Colors -->
                        <div class="loginflux-tab-content" id="loginflux-tab-card-colors" role="tabpanel" aria-labelledby="loginflux-nav-card-colors" aria-hidden="true" tabindex="0">
                            <div class="loginflux-section-header">
                                <h3><?php esc_html_e( 'Form Colors & Glass Styling', 'loginflux' ); ?></h3>
                                <p><?php esc_html_e( 'Control button highlights, text colors, and glassmorphism container parameters.', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-color-grid">
                                <div class="loginflux-color-item">
                                    <label for="loginflux_primary_color"><?php esc_html_e( 'Primary Button / Accent', 'loginflux' ); ?></label>
                                    <input
                                        type="text"
                                        class="loginflux-color-picker"
                                        id="loginflux_primary_color"
                                        name="loginflux_settings[primary_color]"
                                        value="<?php echo esc_attr( $settings['primary_color'] ); ?>"
                                    >
                                </div>
                                <div class="loginflux-color-item">
                                    <label for="loginflux_hover_color"><?php esc_html_e( 'Button Hover Color', 'loginflux' ); ?></label>
                                    <input
                                        type="text"
                                        class="loginflux-color-picker"
                                        id="loginflux_hover_color"
                                        name="loginflux_settings[hover_color]"
                                        value="<?php echo esc_attr( $settings['hover_color'] ); ?>"
                                    >
                                </div>
                                <div class="loginflux-color-item">
                                    <label for="loginflux_text_color"><?php esc_html_e( 'Form Text Color', 'loginflux' ); ?></label>
                                    <input
                                        type="text"
                                        class="loginflux-color-picker"
                                        id="loginflux_text_color"
                                        name="loginflux_settings[text_color]"
                                        value="<?php echo esc_attr( $settings['text_color'] ); ?>"
                                    >
                                </div>
                            </div>

                            <div class="loginflux-section-header" style="margin-top: 20px;">
                                <h3><?php esc_html_e( 'Glassmorphism Card Settings', 'loginflux' ); ?></h3>
                            </div>

                            <div class="loginflux-form-row">
                                <label for="loginflux_card_bg_color"><?php esc_html_e( 'Card Background (RGBA / HEX)', 'loginflux' ); ?></label>
                                <input
                                    type="text"
                                    id="loginflux_card_bg_color"
                                    name="loginflux_settings[card_bg_color]"
                                    value="<?php echo esc_attr( $settings['card_bg_color'] ); ?>"
                                    placeholder="rgba(255, 255, 255, 0.45)"
                                >
                                <p class="description"><?php esc_html_e( 'Supports CSS RGBA transparent values (e.g. rgba(255, 255, 255, 0.45) for frosted glass).', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-form-row loginflux-inline-fields">
                                <div style="flex: 1;">
                                    <label for="loginflux_card_blur"><?php esc_html_e( 'Backdrop Blur (px)', 'loginflux' ); ?></label>
                                    <input
                                        type="number"
                                        id="loginflux_card_blur"
                                        name="loginflux_settings[card_blur]"
                                        value="<?php echo esc_attr( $settings['card_blur'] ); ?>"
                                        min="0"
                                        max="60"
                                    >
                                </div>
                                <div style="flex: 1;">
                                    <label for="loginflux_border_radius"><?php esc_html_e( 'Border Radius (px)', 'loginflux' ); ?></label>
                                    <input
                                        type="number"
                                        id="loginflux_border_radius"
                                        name="loginflux_settings[border_radius]"
                                        value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
                                        min="0"
                                        max="50"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Submit Bar -->
                        <div class="loginflux-submit-bar">
                            <div class="loginflux-settings-actions">
                                <?php submit_button( __( 'Save All Changes', 'loginflux' ), 'primary', 'submit', false ); ?>
                                <button
                                    type="submit"
                                    name="action"
                                    value="jzlf_reset_settings"
                                    formaction="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                                    formmethod="post"
                                    class="button loginflux-reset-settings-button"
                                    onclick="return confirm('<?php echo esc_js( __( 'Restore every setting to its default value? This cannot be undone.', 'loginflux' ) ); ?>');"
                                ><?php esc_html_e( 'Reset to Defaults', 'loginflux' ); ?></button>
                            </div>
                            <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="button button-secondary loginflux-test-login-button">
                                <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                <?php esc_html_e( 'Test Login Screen', 'loginflux' ); ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Sidebar Column (Plugin Details & Author) -->
            <div class="loginflux-sidebar-col">

                <!-- Plugin Info Widget -->
                <div class="loginflux-side-widget">
                    <h4>
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <?php esc_html_e( 'Plugin Information', 'loginflux' ); ?>
                    </h4>
                    <ul class="loginflux-info-list">
                        <li>
                            <span><?php esc_html_e( 'Plugin Version:', 'loginflux' ); ?></span>
                            <strong>v<?php echo esc_html( LOGINFLUX_VERSION ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'Status:', 'loginflux' ); ?></span>
                            <strong style="color: #10b981;"><?php esc_html_e( 'Active & Ready', 'loginflux' ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'WordPress Core:', 'loginflux' ); ?></span>
                            <strong><?php echo esc_html( get_bloginfo( 'version' ) ); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e( 'PHP Version:', 'loginflux' ); ?></span>
                            <strong><?php echo esc_html( phpversion() ); ?></strong>
                        </li>
                    </ul>
                </div>

                <!-- Author & Developer Widget -->
                <div class="loginflux-side-widget">
                    <h4>
                        <span class="dashicons dashicons-businessman"></span>
                        <?php esc_html_e( 'Plugin Author', 'loginflux' ); ?>
                    </h4>
                    <div class="loginflux-author-card">
                        <div class="loginflux-author-avatar">JH</div>
                        <div class="loginflux-author-info">
                            <h5>Jahid Hasan</h5>
                            <span>WordPress & Web Developer</span>
                        </div>
                    </div>
                    <ul class="loginflux-side-links">
                        <li>
                            <a href="https://github.com/jahid-we" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-admin-site" style="margin-right: 4px;"></span> <?php esc_html_e( 'Author GitHub Profile', 'loginflux' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/jahid-we/Loginflux" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-star-filled" style="margin-right: 4px; color: #f59e0b;"></span> <?php esc_html_e( 'Star on GitHub', 'loginflux' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wordpress.org/support/plugin/loginflux/reviews/#new-post" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-thumbs-up" style="margin-right: 4px; color: #10b981;"></span> <?php esc_html_e( 'Rate 5 Stars', 'loginflux' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wordpress.org/support/plugin/loginflux/" target="_blank" rel="noopener noreferrer">
                                <span><span class="dashicons dashicons-sos" style="margin-right: 4px; color: #3b82f6;"></span> <?php esc_html_e( 'Support & Feedback', 'loginflux' ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Live Preview Quick Action -->
                <div class="loginflux-side-widget" style="background: linear-gradient(135deg, #1e1b4b, #312e81); color: #ffffff;">
                    <h4 style="color: #ffffff; border-bottom-color: rgba(255,255,255,0.15);">
                        <span class="dashicons dashicons-welcome-view-site" style="color: #818cf8;"></span>
                        <?php esc_html_e( 'Quick Preview', 'loginflux' ); ?>
                    </h4>
                    <p style="font-size: 13px; color: #cbd5e1; margin-bottom: 14px;">
                        <?php esc_html_e( 'Want to see your changes live? Open your login page in a private/incognito tab or preview now.', 'loginflux' ); ?>
                    </p>
                    <a href="<?php echo esc_url( $login_url ); ?>" target="_blank" class="button" style="display: block; text-align: center; background: #818cf8; color: #ffffff; border: none; font-weight: 600; padding: 6px 0;">
                        <?php esc_html_e( 'Open Login Page', 'loginflux' ); ?>
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
function jzlf_login_body_class( $classes ) {
    $settings = jzlf_get_settings();

    if ( ! empty( $settings['bg_image'] ) ) {
        $classes[] = 'loginflux-has-bg-image';
    } elseif ( ! empty( $settings['aurora_enable'] ) && '1' === $settings['aurora_enable'] ) {
        $classes[] = 'loginflux-aurora-active';
    }

    return $classes;
}
add_filter( 'login_body_class', 'jzlf_login_body_class' );

/**
 * Enqueue Login Page CSS & Dynamic Inline Styles
 */
function jzlf_login_enqueue_style() {
    $settings = jzlf_get_settings();

    wp_enqueue_style(
        'loginflux-login-style',
        LOGINFLUX_URL . 'css/loginflux-style.css',
        [],
        LOGINFLUX_VERSION
    );

    // Calculate dynamic values
    $logo_url      = ! empty( $settings['logo'] ) ? esc_url( $settings['logo'] ) : LOGINFLUX_URL . 'images/logo.png';
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

    $dynamic_css = "
        :root {
            --loginflux-primary: {$primary_color};
            --loginflux-primary-hover: {$hover_color};
            --loginflux-text: {$text_color};
            --loginflux-bg-color: {$bg_color};
            --loginflux-aurora-1: {$aurora_1};
            --loginflux-aurora-2: {$aurora_2};
            --loginflux-aurora-3: {$aurora_3};
            --loginflux-aurora-4: {$aurora_4};
            --loginflux-aurora-speed: {$aurora_speed};
            --loginflux-card-bg: {$card_bg};
            --loginflux-card-blur: {$card_blur};
            --loginflux-radius: {$border_radius};
            --loginflux-logo: url('{$logo_url}');
            --loginflux-logo-width: {$logo_width};
            --loginflux-logo-height: {$logo_height};
        }
    ";

    // If background image is present, add variable
    if ( ! empty( $settings['bg_image'] ) ) {
        $bg_img_url = esc_url( $settings['bg_image'] );
        $dynamic_css .= "
            :root {
                --loginflux-bg-img: url('{$bg_img_url}');
            }
        ";
    }


    wp_add_inline_style( 'loginflux-login-style', $dynamic_css );
}
add_action( 'login_enqueue_scripts', 'jzlf_login_enqueue_style' );

/**
 * Change Login Logo URL to Site Home
 */
function jzlf_login_logo_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'jzlf_login_logo_url' );

/**
 * Change Login Logo Title / Header Text
 */
function jzlf_login_logo_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'jzlf_login_logo_title' );

/**
 * Render Custom Subtitle below logo
 *
 * @param string $message Existing login header message.
 * @return string
 */
function jzlf_login_custom_subtitle( $message ) {
    $settings = jzlf_get_settings();

    if ( ! empty( $settings['form_subtitle'] ) ) {
        $subtitle_html = sprintf(
            '<div class="loginflux-custom-subtitle">%s</div>',
            esc_html( $settings['form_subtitle'] )
        );
        $message = $subtitle_html . $message;
    }

    return $message;
}
add_filter( 'login_message', 'jzlf_login_custom_subtitle' );


