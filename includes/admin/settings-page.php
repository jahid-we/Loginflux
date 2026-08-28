<?php
/**
 * Admin Settings Page Content
 *
 * @package Loginflux
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
                        <div class="loginflux-author-avatar">
                            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Jahid Hasan Avatar">
                                <defs>
                                    <linearGradient id="lf_av_bg" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#4f46e5" />
                                        <stop offset="100%" stop-color="#06b6d4" />
                                    </linearGradient>
                                    <linearGradient id="lf_av_hoodie" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#1e1b4b" />
                                        <stop offset="100%" stop-color="#312e81" />
                                    </linearGradient>
                                    <clipPath id="lf_av_clip">
                                        <circle cx="32" cy="32" r="32" />
                                    </clipPath>
                                </defs>
                                <g clip-path="url(#lf_av_clip)">
                                    <!-- Background -->
                                    <rect width="64" height="64" fill="url(#lf_av_bg)" />
                                    <!-- Soft glow circle behind avatar -->
                                    <circle cx="32" cy="28" r="22" fill="#ffffff" fill-opacity="0.16" />
                                    <!-- Body / Hoodie -->
                                    <path d="M12 64 C12 49, 20 44, 32 44 C44 44, 52 49, 52 64 Z" fill="url(#lf_av_hoodie)" />
                                    <!-- Collar / Inner Shirt -->
                                    <path d="M26 44 L32 52 L38 44 Z" fill="#4338ca" />
                                    <path d="M28 44 L32 49 L36 44 Z" fill="#ffd4b2" />
                                    <!-- Hoodie drawstrings -->
                                    <path d="M28 50 L27 57" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M36 50 L37 57" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" />
                                    <!-- Neck -->
                                    <rect x="28" y="38" width="8" height="7" rx="2" fill="#f6b88b" />
                                    <!-- Ears -->
                                    <circle cx="20" cy="31" r="3" fill="#f6b88b" />
                                    <circle cx="44" cy="31" r="3" fill="#f6b88b" />
                                    <!-- Head / Face -->
                                    <rect x="22" y="19" width="20" height="22" rx="9" fill="#ffd4b2" />
                                    <!-- Hair Back & Volume -->
                                    <path d="M21 24 C20 16 26 12 32 12 C39 12 44 16 43 24 C41 20 37 17 32 17 C26 17 22 20 21 24 Z" fill="#1e130c" />
                                    <!-- Modern Swoop / Front Hair -->
                                    <path d="M21 21 C22 14 29 11 37 12 C43 12.8 44 17 43 21 C40 17.5 35 17 32 17.5 C27 18.2 24 19.5 21 21 Z" fill="#2c1a0e" />
                                    <path d="M21 22 C22 20 24 18 28 17 C24 19 22 22 21 25 Z" fill="#1e130c" />
                                    <!-- Glasses -->
                                    <rect x="23" y="25" width="7.5" height="6" rx="2" fill="#ffffff" fill-opacity="0.1" stroke="#0f172a" stroke-width="1.5" />
                                    <rect x="33.5" y="25" width="7.5" height="6" rx="2" fill="#ffffff" fill-opacity="0.1" stroke="#0f172a" stroke-width="1.5" />
                                    <path d="M30.5 28 L33.5 28" stroke="#0f172a" stroke-width="1.5" />
                                    <path d="M21.5 27 L23 27" stroke="#0f172a" stroke-width="1.3" />
                                    <path d="M41 27 L42.5 27" stroke="#0f172a" stroke-width="1.3" />
                                    <!-- Eyes -->
                                    <circle cx="26.8" cy="28" r="1.3" fill="#0f172a" />
                                    <circle cx="37.2" cy="28" r="1.3" fill="#0f172a" />
                                    <circle cx="27.3" cy="27.5" r="0.5" fill="#ffffff" />
                                    <circle cx="37.7" cy="27.5" r="0.5" fill="#ffffff" />
                                    <!-- Eyebrows -->
                                    <path d="M24 23 C25.5 22 28 22.5 29 23.5" stroke="#1e130c" stroke-width="1.3" stroke-linecap="round" fill="none" />
                                    <path d="M35 23.5 C36 22.5 38.5 22 40 23" stroke="#1e130c" stroke-width="1.3" stroke-linecap="round" fill="none" />
                                    <!-- Friendly Smile -->
                                    <path d="M29 35 Q32 38 35 35" stroke="#ba4a00" stroke-width="1.4" stroke-linecap="round" fill="none" />
                                    <!-- Rosy Cheeks -->
                                    <ellipse cx="24" cy="32" rx="1.6" ry="1" fill="#e74c3c" fill-opacity="0.25" />
                                    <ellipse cx="40" cy="32" rx="1.6" ry="1" fill="#e74c3c" fill-opacity="0.25" />
                                    <!-- Code symbol badge on hoodie -->
                                    <path d="M29 56 L27 58 L29 60" stroke="#38bdf8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                    <path d="M35 56 L37 58 L35 60" stroke="#38bdf8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                    <path d="M33 55 L31 61" stroke="#38bdf8" stroke-width="1.1" stroke-linecap="round" />
                                </g>
                            </svg>
                        </div>
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
                            <a href="https://github.com/jahid-we/loginflux" target="_blank" rel="noopener noreferrer">
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
