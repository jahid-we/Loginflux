=== Loginflux ===
Contributors: jahidzendforce
Donate Link: https://buymeacoffee.com/jahid.hasan
Author URI: https://github.com/jahid-we
Plugin URI: https://wordpress.org/plugins/loginflux/
Tags: login, custom login, login page, animated background, aurora background
Requires at least: 6.0
Tested up to: 7.1
Stable tag: 1.3.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform your login page with animated visual effects, dynamic backgrounds, glassmorphism, custom branding, and modern color controls.

== Description ==

**Loginflux** is a lightweight, modern plugin that transforms the default WordPress login page into a state-of-the-art visual experience.

Featuring built-in animated visual effects—including dynamic **Fluid Flow gradients**—and an ultra-sleek **Glassmorphism container**, you can effortlessly match the login experience with your brand identity directly from an intuitive, tabbed WordPress admin dashboard.

### 🌟 Key Features

* **8 Modern Animated Background Engines:** Choose between Animation 1 (Pulse Orb & Cyber Grid), Animation 2 (Nebula Glow & Noise), Animation 3 (Aurora Gradient Flow), Animation 4 (Ambient Mesh Spin), Animation 5 (Cosmic Starfield & Stardust), Animation 6 (Holographic Prism & Cyber Waves), Animation 7 (Retro Synthwave & Neon Horizon), and Animation 8 (Liquid Morphing Blobs) with full color and speed controls.
* **Smart Background Fallback:** Automatically switches between your uploaded custom background image and fluid animated visual gradients.
* **WordPress Media Library Integration:** Seamlessly upload or choose logos and background images using the native WordPress Media Uploader.
* **Ultra-Modern Glassmorphism UI:** Frosted glass effect with configurable backdrop blur, card background opacity, and border radius.
* **Branding & Logo Controls:** Customize your logo image, logo dimensions, and subtitle text.
* **Live Color Customizer:** Use the built-in WordPress Color Picker to customize primary button colors, hover states, text colors, and all animation palettes.
* **Clean & Lightweight:** Designed to keep the login screen fast and avoid unnecessary external JavaScript dependencies.
* **Translation Ready:** Fully internationalized and compatible with WordPress translation standards using the `loginflux` text domain.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/loginflux` directory, or install it through the WordPress Plugins menu.
2. Activate Loginflux through the **Plugins** menu in WordPress.
3. Go to **Settings → Loginflux** to customize your branding, animated backgrounds, and colors.
4. Save your changes and preview your new login page.

== Need Help? ==

* [LinkedIn](https://linkedin.com/in/jahid-hasan-6891123a)
* [About Author](https://github.com/jahid-we)

== Frequently Asked Questions ==

= How do the animated background gradients work? =

When no background image is set, Loginflux renders a dynamic CSS animated gradient flow background using customizable color stops. You can adjust the gradient colors and animation speed from the Loginflux settings.

= Can I use my own background wallpaper instead? =

Yes! Upload or select a background image from the WordPress Media Library in the Background & Effects settings. When a background image is set, it takes priority over the animated gradient background.

= Does this plugin affect login security or authentication? =

No. Loginflux only customizes the visual presentation and styling of the WordPress login screen. It does not modify WordPress authentication, password validation, or login security functionality.

= Is it translation ready? =

Yes. Loginflux follows WordPress internationalization standards and uses the `loginflux` text domain.

== Screenshots ==

1. Modern tabbed settings dashboard with live login preview and plugin information sidebar.
2. Animated fluid gradient background with a modern glassmorphism login form.

== Changelog ==

= 1.3.0 =
* Added 4 new animated visual engines: Animation 5 (Cosmic Starfield & Stardust Flow), Animation 6 (Holographic Prism & Cyber Waves), Animation 7 (Retro Synthwave & Neon Horizon), and Animation 8 (Liquid Morphing Blobs).
* Added dedicated color pickers, animation speed, blur controls, and visual overlay toggles for all new animation styles.
* Added modern interactive card selector previews for all 8 animation engines in the admin dashboard.
* Added accessibility support with `@media (prefers-reduced-motion)` for motion-sensitive users.
* Updated default settings schema and sanitization callbacks for all new animation parameters.

= 1.2.1 =
* Fixed admin reset flash notice persistence on page reload and URL state cleanup.
* Fixed plugin activation redirect hook path.
* Refactored codebase architecture into clean modular components for improved performance and maintainability.

= 1.2.0 =
* Added 4 selectable animation engines: Animation 1 (Pulse Orb & Tech Grid), Animation 2 (Nebula Glow & Noise), Animation 3 (Aurora Gradient Flow), and Animation 4 (Ambient Mesh Spin).
* Added interactive visual card switcher for animation styles in the admin dashboard.
* Added dedicated color palette pickers and speed controls for each individual animation style.
* Added cyber tech grid and noise texture overlay toggles.
* Enhanced login screen CSS rendering and dynamic CSS custom properties.

= 1.1.0 =
* Initial release as Loginflux.
* Added animated Fluid Flow gradient background with customizable color stops.
* Added native WordPress Media Uploader for logo and background images.
* Added WordPress Color Picker controls for login page styling.
* Added modern two-column admin dashboard with plugin information and login preview.
* Added custom logo width and height controls.
* Added custom subtitle text support.
* Added glassmorphism card background, blur, and border-radius controls.