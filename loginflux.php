<?php
/**
 * Plugin Name:       Loginflux
 * Plugin URI:        https://github.com/jahid-we/Loginflux
 * Description:       Transform your login page with animated visual effects, dynamic backgrounds, glassmorphism, custom branding, and modern color controls.
 * Version:           1.2.0
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
define( 'LOGINFLUX_VERSION', '1.2.0' );
define( 'LOGINFLUX_URL', plugin_dir_url( __FILE__ ) );
define( 'LOGINFLUX_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOGINFLUX_BASENAME', plugin_basename( __FILE__ ) );

require_once LOGINFLUX_PATH . 'includes/deafult-setting.php';

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
    $sanitized['bg_image']         = isset( $input['bg_image'] ) ? esc_url_raw( $input['bg_image'] ) : '';

    // Animation Master Enable
    $anim_enabled                  = ! empty( $input['animation_enable'] ) || ! empty( $input['aurora_enable'] );
    $sanitized['animation_enable'] = $anim_enabled ? '1' : '0';
    $sanitized['aurora_enable']    = $sanitized['animation_enable']; // backward compatibility

    // Animation Style Selection
    $valid_anim_types              = [ '1', '2', '3', '4' ];
    $sanitized['animation_type']   = ( isset( $input['animation_type'] ) && in_array( (string) $input['animation_type'], $valid_anim_types, true ) ) ? (string) $input['animation_type'] : '3';

    // Animation 1 (Pulse Orb & Cyber Grid)
    $sanitized['anim1_bg']         = isset( $input['anim1_bg'] ) ? sanitize_hex_color( $input['anim1_bg'] ) : '#090d16';
    $sanitized['anim1_color_1']    = isset( $input['anim1_color_1'] ) ? sanitize_hex_color( $input['anim1_color_1'] ) : '#3b82f6';
    $sanitized['anim1_color_2']    = isset( $input['anim1_color_2'] ) ? sanitize_hex_color( $input['anim1_color_2'] ) : '#ec4899';
    $sanitized['anim1_speed']      = isset( $input['anim1_speed'] ) ? absint( $input['anim1_speed'] ) : 12;
    $sanitized['anim1_grid']       = ! empty( $input['anim1_grid'] ) ? '1' : '0';

    // Animation 2 (Nebula Glow & Noise)
    $sanitized['anim2_bg']         = isset( $input['anim2_bg'] ) ? sanitize_hex_color( $input['anim2_bg'] ) : '#0b0f19';
    $sanitized['anim2_color_1']    = isset( $input['anim2_color_1'] ) ? sanitize_hex_color( $input['anim2_color_1'] ) : '#06b6d4';
    $sanitized['anim2_color_2']    = isset( $input['anim2_color_2'] ) ? sanitize_hex_color( $input['anim2_color_2'] ) : '#8b5cf6';
    $sanitized['anim2_color_3']    = isset( $input['anim2_color_3'] ) ? sanitize_hex_color( $input['anim2_color_3'] ) : '#f43f5e';
    $sanitized['anim2_speed']      = isset( $input['anim2_speed'] ) ? absint( $input['anim2_speed'] ) : 10;
    $sanitized['anim2_noise']      = ! empty( $input['anim2_noise'] ) ? '1' : '0';

    // Animation 3 (Aurora Gradient Flow)
    $sanitized['bg_color']         = isset( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : '#030712';
    $sanitized['aurora_color_1']   = isset( $input['aurora_color_1'] ) ? sanitize_hex_color( $input['aurora_color_1'] ) : '#030712';
    $sanitized['aurora_color_2']   = isset( $input['aurora_color_2'] ) ? sanitize_hex_color( $input['aurora_color_2'] ) : '#1e1b4b';
    $sanitized['aurora_color_3']   = isset( $input['aurora_color_3'] ) ? sanitize_hex_color( $input['aurora_color_3'] ) : '#0284c7';
    $sanitized['aurora_color_4']   = isset( $input['aurora_color_4'] ) ? sanitize_hex_color( $input['aurora_color_4'] ) : '#4f46e5';
    $sanitized['aurora_speed']     = isset( $input['aurora_speed'] ) ? absint( $input['aurora_speed'] ) : 15;

    // Animation 4 (Ambient Mesh Spin)
    $sanitized['anim4_bg']         = isset( $input['anim4_bg'] ) ? sanitize_hex_color( $input['anim4_bg'] ) : '#0f172a';
    $sanitized['anim4_color_1']    = isset( $input['anim4_color_1'] ) ? sanitize_hex_color( $input['anim4_color_1'] ) : '#818cf8';
    $sanitized['anim4_color_2']    = isset( $input['anim4_color_2'] ) ? sanitize_hex_color( $input['anim4_color_2'] ) : '#c084fc';
    $sanitized['anim4_color_3']    = isset( $input['anim4_color_3'] ) ? sanitize_hex_color( $input['anim4_color_3'] ) : '#38bdf8';
    $sanitized['anim4_speed']      = isset( $input['anim4_speed'] ) ? absint( $input['anim4_speed'] ) : 20;

    // Theme Colors & Styling
    $sanitized['primary_color']    = isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#6366f1';
    $sanitized['hover_color']      = isset( $input['hover_color'] ) ? sanitize_hex_color( $input['hover_color'] ) : '#4f46e5';
    $sanitized['text_color']       = isset( $input['text_color'] ) ? sanitize_hex_color( $input['text_color'] ) : '#0f172a';
    $sanitized['card_bg_color']    = isset( $input['card_bg_color'] ) ? sanitize_text_field( $input['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $sanitized['card_blur']        = isset( $input['card_blur'] ) ? absint( $input['card_blur'] ) : 28;
    $sanitized['border_radius']    = isset( $input['border_radius'] ) ? absint( $input['border_radius'] ) : 24;

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
                                <p><?php esc_html_e( 'Configure your login page background image or choose from 4 modern animated visual effects.', 'loginflux' ); ?></p>
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
                                <p class="description"><?php esc_html_e( 'If a background image is provided, it takes precedence over animations. If left empty, your chosen animation style will be displayed.', 'loginflux' ); ?></p>
                            </div>

                            <div class="loginflux-notice-box loginflux-bg-image-notice" <?php echo empty( $settings['bg_image'] ) ? 'style="display:none;"' : ''; ?>>
                                <span class="dashicons dashicons-info"></span>
                                <div>
                                    <strong><?php esc_html_e( 'Background Image Active:', 'loginflux' ); ?></strong>
                                    <?php esc_html_e( 'A background image is currently set. The selected visual animation will remain dormant until the background image URL is removed.', 'loginflux' ); ?>
                                </div>
                            </div>

                            <!-- Animated Visual Effect Configuration -->
                            <div class="loginflux-anim-settings-group">
                                <div class="loginflux-section-header" style="margin-top: 24px;">
                                    <h3><?php esc_html_e( 'Visual Animation Engine', 'loginflux' ); ?></h3>
                                    <p><?php esc_html_e( 'Select which animation to run on your login screen and fine-tune its colors and speed.', 'loginflux' ); ?></p>
                                </div>

                                <div class="loginflux-form-row">
                                    <label class="loginflux-switch-label" style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                                        <input
                                            type="checkbox"
                                            id="loginflux_animation_enable"
                                            name="loginflux_settings[animation_enable]"
                                            value="1"
                                            <?php checked( $settings['animation_enable'], '1' ); ?>
                                        >
                                        <strong><?php esc_html_e( 'Activate Background Animation', 'loginflux' ); ?></strong>
                                    </label>
                                    <p class="description"><?php esc_html_e( 'Toggle to activate or deactivate animated visuals when no background image is active.', 'loginflux' ); ?></p>
                                </div>

                                <!-- Animation Style Selector Cards -->
                                <div class="loginflux-form-row">
                                    <label><strong><?php esc_html_e( 'Select Animation Style', 'loginflux' ); ?></strong></label>
                                    <div class="loginflux-anim-selector-grid">

                                        <!-- Option 1: Pulse Orb & Cyber Grid -->
                                        <label class="loginflux-anim-card <?php echo ( '1' === (string) $settings['animation_type'] ) ? 'active' : ''; ?>">
                                            <input
                                                type="radio"
                                                name="loginflux_settings[animation_type]"
                                                value="1"
                                                class="loginflux-anim-radio"
                                                <?php checked( $settings['animation_type'], '1' ); ?>
                                            >
                                            <div class="loginflux-anim-card-header">
                                                <span class="loginflux-anim-badge">Animation 1</span>
                                                <span class="loginflux-anim-card-radio-mark dashicons dashicons-yes"></span>
                                            </div>
                                            <div class="loginflux-anim-preview-banner anim-1-preview">
                                                <div class="anim-preview-orb anim-orb-1"></div>
                                                <div class="anim-preview-orb anim-orb-2"></div>
                                                <div class="anim-preview-grid"></div>
                                            </div>
                                            <div class="loginflux-anim-card-body">
                                                <h4><?php esc_html_e( 'Pulse Orb & Tech Grid', 'loginflux' ); ?></h4>
                                                <p><?php esc_html_e( 'Dual rotating glowing orbs with a linear cyberpunk tech grid.', 'loginflux' ); ?></p>
                                            </div>
                                        </label>

                                        <!-- Option 2: Nebula Glow & Noise -->
                                        <label class="loginflux-anim-card <?php echo ( '2' === (string) $settings['animation_type'] ) ? 'active' : ''; ?>">
                                            <input
                                                type="radio"
                                                name="loginflux_settings[animation_type]"
                                                value="2"
                                                class="loginflux-anim-radio"
                                                <?php checked( $settings['animation_type'], '2' ); ?>
                                            >
                                            <div class="loginflux-anim-card-header">
                                                <span class="loginflux-anim-badge">Animation 2</span>
                                                <span class="loginflux-anim-card-radio-mark dashicons dashicons-yes"></span>
                                            </div>
                                            <div class="loginflux-anim-preview-banner anim-2-preview">
                                                <div class="anim-preview-nebula anim-nebula-1"></div>
                                                <div class="anim-preview-nebula anim-nebula-2"></div>
                                                <div class="anim-preview-nebula anim-nebula-3"></div>
                                                <div class="anim-preview-noise"></div>
                                            </div>
                                            <div class="loginflux-anim-card-body">
                                                <h4><?php esc_html_e( 'Nebula Glow & Noise', 'loginflux' ); ?></h4>
                                                <p><?php esc_html_e( 'Tri-color pulsing ambient nebula with organic noise filter.', 'loginflux' ); ?></p>
                                            </div>
                                        </label>

                                        <!-- Option 3: Aurora Gradient Flow -->
                                        <label class="loginflux-anim-card <?php echo ( '3' === (string) $settings['animation_type'] ) ? 'active' : ''; ?>">
                                            <input
                                                type="radio"
                                                name="loginflux_settings[animation_type]"
                                                value="3"
                                                class="loginflux-anim-radio"
                                                <?php checked( $settings['animation_type'], '3' ); ?>
                                            >
                                            <div class="loginflux-anim-card-header">
                                                <span class="loginflux-anim-badge">Animation 3</span>
                                                <span class="loginflux-anim-card-radio-mark dashicons dashicons-yes"></span>
                                            </div>
                                            <div class="loginflux-anim-preview-banner anim-3-preview">
                                                <div class="anim-preview-aurora"></div>
                                            </div>
                                            <div class="loginflux-anim-card-body">
                                                <h4><?php esc_html_e( 'Aurora Flow Wave', 'loginflux' ); ?></h4>
                                                <p><?php esc_html_e( 'Hypnotic 4-color dynamic shifting Aurora fluid flow.', 'loginflux' ); ?></p>
                                            </div>
                                        </label>

                                        <!-- Option 4: Ambient Mesh Spin -->
                                        <label class="loginflux-anim-card <?php echo ( '4' === (string) $settings['animation_type'] ) ? 'active' : ''; ?>">
                                            <input
                                                type="radio"
                                                name="loginflux_settings[animation_type]"
                                                value="4"
                                                class="loginflux-anim-radio"
                                                <?php checked( $settings['animation_type'], '4' ); ?>
                                            >
                                            <div class="loginflux-anim-card-header">
                                                <span class="loginflux-anim-badge">Animation 4</span>
                                                <span class="loginflux-anim-card-radio-mark dashicons dashicons-yes"></span>
                                            </div>
                                            <div class="loginflux-anim-preview-banner anim-4-preview">
                                                <div class="anim-preview-mesh-wrap">
                                                    <div class="anim-mesh-blob blob-1"></div>
                                                    <div class="anim-mesh-blob blob-2"></div>
                                                    <div class="anim-mesh-blob blob-3"></div>
                                                </div>
                                            </div>
                                            <div class="loginflux-anim-card-body">
                                                <h4><?php esc_html_e( 'Ambient Mesh Spin', 'loginflux' ); ?></h4>
                                                <p><?php esc_html_e( 'Continuous 360-degree rotating 3-point ambient gradient mesh.', 'loginflux' ); ?></p>
                                            </div>
                                        </label>

                                    </div>
                                </div>

                                <!-- Panel 1: Animation 1 Controls -->
                                <div class="loginflux-anim-panel" data-anim-panel="1" style="<?php echo ( '1' === (string) $settings['animation_type'] ) ? '' : 'display: none;'; ?>">
                                    <div class="loginflux-section-header">
                                        <h3><?php esc_html_e( 'Animation 1: Pulse Orb & Cyber Grid Settings', 'loginflux' ); ?></h3>
                                        <p><?php esc_html_e( 'Customize the glowing orb colors, background tone, and rotation speed.', 'loginflux' ); ?></p>
                                    </div>

                                    <div class="loginflux-color-grid">
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim1_bg"><?php esc_html_e( 'Base Background Color', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim1_bg"
                                                name="loginflux_settings[anim1_bg]"
                                                value="<?php echo esc_attr( $settings['anim1_bg'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim1_color_1"><?php esc_html_e( 'Glowing Orb 1 (Top-Left)', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim1_color_1"
                                                name="loginflux_settings[anim1_color_1]"
                                                value="<?php echo esc_attr( $settings['anim1_color_1'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim1_color_2"><?php esc_html_e( 'Glowing Orb 2 (Bottom-Right)', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim1_color_2"
                                                name="loginflux_settings[anim1_color_2]"
                                                value="<?php echo esc_attr( $settings['anim1_color_2'] ); ?>"
                                            >
                                        </div>
                                    </div>

                                    <div class="loginflux-form-row loginflux-inline-fields">
                                        <div style="flex: 1;">
                                            <label for="loginflux_anim1_speed"><?php esc_html_e( 'Pulse & Spin Duration (seconds)', 'loginflux' ); ?></label>
                                            <input
                                                type="number"
                                                id="loginflux_anim1_speed"
                                                name="loginflux_settings[anim1_speed]"
                                                value="<?php echo esc_attr( $settings['anim1_speed'] ); ?>"
                                                min="3"
                                                max="60"
                                            >
                                            <p class="description"><?php esc_html_e( 'Default: 12s. Lower is faster.', 'loginflux' ); ?></p>
                                        </div>
                                        <div style="flex: 1; display: flex; align-items: flex-end; padding-bottom: 22px;">
                                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                                <input
                                                    type="checkbox"
                                                    name="loginflux_settings[anim1_grid]"
                                                    value="1"
                                                    <?php checked( $settings['anim1_grid'], '1' ); ?>
                                                >
                                                <span><?php esc_html_e( 'Show Tech Grid Overlay', 'loginflux' ); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel 2: Animation 2 Controls -->
                                <div class="loginflux-anim-panel" data-anim-panel="2" style="<?php echo ( '2' === (string) $settings['animation_type'] ) ? '' : 'display: none;'; ?>">
                                    <div class="loginflux-section-header">
                                        <h3><?php esc_html_e( 'Animation 2: Nebula Glow & Noise Settings', 'loginflux' ); ?></h3>
                                        <p><?php esc_html_e( 'Control the 3 nebula cloud stops and subtle noise layer.', 'loginflux' ); ?></p>
                                    </div>

                                    <div class="loginflux-color-grid">
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim2_bg"><?php esc_html_e( 'Base Background Color', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim2_bg"
                                                name="loginflux_settings[anim2_bg]"
                                                value="<?php echo esc_attr( $settings['anim2_bg'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim2_color_1"><?php esc_html_e( 'Nebula Color 1', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim2_color_1"
                                                name="loginflux_settings[anim2_color_1]"
                                                value="<?php echo esc_attr( $settings['anim2_color_1'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim2_color_2"><?php esc_html_e( 'Nebula Color 2', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim2_color_2"
                                                name="loginflux_settings[anim2_color_2]"
                                                value="<?php echo esc_attr( $settings['anim2_color_2'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim2_color_3"><?php esc_html_e( 'Nebula Color 3', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim2_color_3"
                                                name="loginflux_settings[anim2_color_3]"
                                                value="<?php echo esc_attr( $settings['anim2_color_3'] ); ?>"
                                            >
                                        </div>
                                    </div>

                                    <div class="loginflux-form-row loginflux-inline-fields">
                                        <div style="flex: 1;">
                                            <label for="loginflux_anim2_speed"><?php esc_html_e( 'Nebula Bounce Duration (seconds)', 'loginflux' ); ?></label>
                                            <input
                                                type="number"
                                                id="loginflux_anim2_speed"
                                                name="loginflux_settings[anim2_speed]"
                                                value="<?php echo esc_attr( $settings['anim2_speed'] ); ?>"
                                                min="3"
                                                max="60"
                                            >
                                            <p class="description"><?php esc_html_e( 'Default: 10s. Lower is faster.', 'loginflux' ); ?></p>
                                        </div>
                                        <div style="flex: 1; display: flex; align-items: flex-end; padding-bottom: 22px;">
                                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                                <input
                                                    type="checkbox"
                                                    name="loginflux_settings[anim2_noise]"
                                                    value="1"
                                                    <?php checked( $settings['anim2_noise'], '1' ); ?>
                                                >
                                                <span><?php esc_html_e( 'Enable Noise Texture Overlay', 'loginflux' ); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel 3: Animation 3 Controls -->
                                <div class="loginflux-anim-panel" data-anim-panel="3" style="<?php echo ( '3' === (string) $settings['animation_type'] ) ? '' : 'display: none;'; ?>">
                                    <div class="loginflux-section-header">
                                        <h3><?php esc_html_e( 'Animation 3: Aurora Flow Gradient Palette', 'loginflux' ); ?></h3>
                                        <p><?php esc_html_e( 'Customize the 4 harmonic color stops for the fluid animated gradient wave.', 'loginflux' ); ?></p>
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
                                        <p class="description"><?php esc_html_e( 'Duration of one continuous gradient cycle (default: 15s). Lower numbers make it faster.', 'loginflux' ); ?></p>
                                    </div>
                                </div>

                                <!-- Panel 4: Animation 4 Controls -->
                                <div class="loginflux-anim-panel" data-anim-panel="4" style="<?php echo ( '4' === (string) $settings['animation_type'] ) ? '' : 'display: none;'; ?>">
                                    <div class="loginflux-section-header">
                                        <h3><?php esc_html_e( 'Animation 4: Ambient Mesh Spin Settings', 'loginflux' ); ?></h3>
                                        <p><?php esc_html_e( 'Configure the 3 radial color points and the rotation cycle.', 'loginflux' ); ?></p>
                                    </div>

                                    <div class="loginflux-color-grid">
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim4_bg"><?php esc_html_e( 'Base Background Color', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim4_bg"
                                                name="loginflux_settings[anim4_bg]"
                                                value="<?php echo esc_attr( $settings['anim4_bg'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim4_color_1"><?php esc_html_e( 'Mesh Color 1 (Top-Left)', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim4_color_1"
                                                name="loginflux_settings[anim4_color_1]"
                                                value="<?php echo esc_attr( $settings['anim4_color_1'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim4_color_2"><?php esc_html_e( 'Mesh Color 2 (Bottom-Right)', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim4_color_2"
                                                name="loginflux_settings[anim4_color_2]"
                                                value="<?php echo esc_attr( $settings['anim4_color_2'] ); ?>"
                                            >
                                        </div>
                                        <div class="loginflux-color-item">
                                            <label for="loginflux_anim4_color_3"><?php esc_html_e( 'Mesh Color 3 (Center)', 'loginflux' ); ?></label>
                                            <input
                                                type="text"
                                                class="loginflux-color-picker"
                                                id="loginflux_anim4_color_3"
                                                name="loginflux_settings[anim4_color_3]"
                                                value="<?php echo esc_attr( $settings['anim4_color_3'] ); ?>"
                                            >
                                        </div>
                                    </div>

                                    <div class="loginflux-form-row">
                                        <label for="loginflux_anim4_speed"><?php esc_html_e( 'Full Rotation Spin Duration (seconds)', 'loginflux' ); ?></label>
                                        <input
                                            type="number"
                                            id="loginflux_anim4_speed"
                                            name="loginflux_settings[anim4_speed]"
                                            value="<?php echo esc_attr( $settings['anim4_speed'] ); ?>"
                                            min="3"
                                            max="60"
                                            style="max-width: 150px;"
                                        >
                                        <p class="description"><?php esc_html_e( 'Default: 20s. Duration of a complete 360-degree rotation.', 'loginflux' ); ?></p>
                                    </div>
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
    } elseif ( ! empty( $settings['animation_enable'] ) && '1' === (string) $settings['animation_enable'] ) {
        $anim_type = ! empty( $settings['animation_type'] ) ? (string) $settings['animation_type'] : '3';
        switch ( $anim_type ) {
            case '1':
                $classes[] = 'loginflux-anim-1-active';
                if ( empty( $settings['anim1_grid'] ) || '1' !== (string) $settings['anim1_grid'] ) {
                    $classes[] = 'loginflux-grid-disabled';
                }
                break;
            case '2':
                $classes[] = 'loginflux-anim-2-active';
                if ( empty( $settings['anim2_noise'] ) || '1' !== (string) $settings['anim2_noise'] ) {
                    $classes[] = 'loginflux-noise-disabled';
                }
                break;
            case '4':
                $classes[] = 'loginflux-anim-4-active';
                break;
            case '3':
            default:
                $classes[] = 'loginflux-anim-3-active';
                $classes[] = 'loginflux-aurora-active';
                break;
        }
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

    // Calculate dynamic branding & form values
    $logo_url      = ! empty( $settings['logo'] ) ? esc_url( $settings['logo'] ) : LOGINFLUX_URL . 'images/logo.png';
    $logo_width    = ! empty( $settings['logo_width'] ) ? absint( $settings['logo_width'] ) . 'px' : '160px';
    $logo_height   = ! empty( $settings['logo_height'] ) ? absint( $settings['logo_height'] ) . 'px' : '50px';
    $primary_color = ! empty( $settings['primary_color'] ) ? sanitize_hex_color( $settings['primary_color'] ) : '#6366f1';
    $hover_color   = ! empty( $settings['hover_color'] ) ? sanitize_hex_color( $settings['hover_color'] ) : '#4f46e5';
    $text_color    = ! empty( $settings['text_color'] ) ? sanitize_hex_color( $settings['text_color'] ) : '#0f172a';
    $card_bg       = ! empty( $settings['card_bg_color'] ) ? esc_attr( $settings['card_bg_color'] ) : 'rgba(255, 255, 255, 0.45)';
    $card_blur     = ! empty( $settings['card_blur'] ) ? absint( $settings['card_blur'] ) . 'px' : '28px';
    $border_radius = ! empty( $settings['border_radius'] ) ? absint( $settings['border_radius'] ) . 'px' : '24px';

    // Animation 1 (Pulse Orb & Cyber Grid)
    $anim1_bg      = ! empty( $settings['anim1_bg'] ) ? sanitize_hex_color( $settings['anim1_bg'] ) : '#090d16';
    $anim1_color1  = ! empty( $settings['anim1_color_1'] ) ? sanitize_hex_color( $settings['anim1_color_1'] ) : '#3b82f6';
    $anim1_color2  = ! empty( $settings['anim1_color_2'] ) ? sanitize_hex_color( $settings['anim1_color_2'] ) : '#ec4899';
    $anim1_speed   = ! empty( $settings['anim1_speed'] ) ? absint( $settings['anim1_speed'] ) . 's' : '12s';

    // Animation 2 (Nebula Glow & Noise)
    $anim2_bg      = ! empty( $settings['anim2_bg'] ) ? sanitize_hex_color( $settings['anim2_bg'] ) : '#0b0f19';
    $anim2_color1  = ! empty( $settings['anim2_color_1'] ) ? sanitize_hex_color( $settings['anim2_color_1'] ) : '#06b6d4';
    $anim2_color2  = ! empty( $settings['anim2_color_2'] ) ? sanitize_hex_color( $settings['anim2_color_2'] ) : '#8b5cf6';
    $anim2_color3  = ! empty( $settings['anim2_color_3'] ) ? sanitize_hex_color( $settings['anim2_color_3'] ) : '#f43f5e';
    $anim2_speed   = ! empty( $settings['anim2_speed'] ) ? absint( $settings['anim2_speed'] ) . 's' : '10s';

    // Animation 3 (Aurora Gradient Flow)
    $bg_color      = ! empty( $settings['bg_color'] ) ? sanitize_hex_color( $settings['bg_color'] ) : '#030712';
    $aurora_1      = ! empty( $settings['aurora_color_1'] ) ? sanitize_hex_color( $settings['aurora_color_1'] ) : $bg_color;
    $aurora_2      = ! empty( $settings['aurora_color_2'] ) ? sanitize_hex_color( $settings['aurora_color_2'] ) : '#1e1b4b';
    $aurora_3      = ! empty( $settings['aurora_color_3'] ) ? sanitize_hex_color( $settings['aurora_color_3'] ) : '#0284c7';
    $aurora_4      = ! empty( $settings['aurora_color_4'] ) ? sanitize_hex_color( $settings['aurora_color_4'] ) : '#4f46e5';
    $aurora_speed  = ! empty( $settings['aurora_speed'] ) ? absint( $settings['aurora_speed'] ) . 's' : '15s';

    // Animation 4 (Ambient Mesh Spin)
    $anim4_bg      = ! empty( $settings['anim4_bg'] ) ? sanitize_hex_color( $settings['anim4_bg'] ) : '#0f172a';
    $anim4_color1  = ! empty( $settings['anim4_color_1'] ) ? sanitize_hex_color( $settings['anim4_color_1'] ) : '#818cf8';
    $anim4_color2  = ! empty( $settings['anim4_color_2'] ) ? sanitize_hex_color( $settings['anim4_color_2'] ) : '#c084fc';
    $anim4_color3  = ! empty( $settings['anim4_color_3'] ) ? sanitize_hex_color( $settings['anim4_color_3'] ) : '#38bdf8';
    $anim4_speed   = ! empty( $settings['anim4_speed'] ) ? absint( $settings['anim4_speed'] ) . 's' : '20s';

    $dynamic_css = "
        :root {
            --loginflux-primary: {$primary_color};
            --loginflux-primary-hover: {$hover_color};
            --loginflux-text: {$text_color};
            --loginflux-card-bg: {$card_bg};
            --loginflux-card-blur: {$card_blur};
            --loginflux-radius: {$border_radius};
            --loginflux-logo: url('{$logo_url}');
            --loginflux-logo-width: {$logo_width};
            --loginflux-logo-height: {$logo_height};

            --loginflux-anim1-bg: {$anim1_bg};
            --loginflux-anim1-color1: {$anim1_color1};
            --loginflux-anim1-color2: {$anim1_color2};
            --loginflux-anim1-speed: {$anim1_speed};

            --loginflux-anim2-bg: {$anim2_bg};
            --loginflux-anim2-color1: {$anim2_color1};
            --loginflux-anim2-color2: {$anim2_color2};
            --loginflux-anim2-color3: {$anim2_color3};
            --loginflux-anim2-speed: {$anim2_speed};

            --loginflux-bg-color: {$bg_color};
            --loginflux-aurora-1: {$aurora_1};
            --loginflux-aurora-2: {$aurora_2};
            --loginflux-aurora-3: {$aurora_3};
            --loginflux-aurora-4: {$aurora_4};
            --loginflux-aurora-speed: {$aurora_speed};

            --loginflux-anim4-bg: {$anim4_bg};
            --loginflux-anim4-color1: {$anim4_color1};
            --loginflux-anim4-color2: {$anim4_color2};
            --loginflux-anim4-color3: {$anim4_color3};
            --loginflux-anim4-speed: {$anim4_speed};
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


