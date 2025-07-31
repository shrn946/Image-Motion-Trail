<?php
/**
 * Plugin Name: Image Motion Trail Plugin
 * Description: Adds a smooth image animation effect to elements with class 'trail' using GSAP.
 * Version: 1.0
 * Author: WP DESIGN LAB
 * License: GPL2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode
function tv_effect_shortcode($atts) {
    $atts = shortcode_atts(array(
        'image_url' => plugin_dir_url(__FILE__) . 'img/demo.jpg',
    ), $atts, 'tv_effect');

    // Generate a unique ID for this instance
    static $instance = 0;
    $instance++;
    $unique_class = 'tv-effect-instance-' . $instance;

    ob_start();
    ?>
    <div class="tv-effect-wrapper <?php echo esc_attr($unique_class); ?>">
        <main class="tv-effect-main">
            <div class="frame-tv"></div>
            <div class="content-tv">
                <div class="trail"
                     style="background-image: url('<?php echo esc_url($atts['image_url']); ?>');">
                </div>
            </div>
        </main>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('image_trail', 'tv_effect_shortcode');






// Enqueue GSAP and custom effect script
function tv_effect_enqueue_scripts() {
    // Only load on frontend, not in admin
    if (is_admin()) {
        return;
    }

    // Enqueue base CSS
    wp_enqueue_style(
        'tv-effect-base',
        plugin_dir_url(__FILE__) . 'css/base.css',
        array(),
        '1.0',
        'all'
    );

    // Enqueue GSAP from CDN (note: removed trailing space in URL)
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js', // Fixed: removed extra space
        array(),
        '3.9.1',
        true
    );

    // Enqueue our custom effect script
    wp_enqueue_script(
        'tv-effect-script',
        plugin_dir_url(__FILE__) . 'js/index.js',
        array('gsap'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'tv_effect_enqueue_scripts');




// Add admin menu page
function tv_effect_menu_page() {
    add_options_page(
        'Image Trail Shortcode Guide',
        'Image Trail',
        'manage_options',
        'tv-effect-settings',
        'tv_effect_settings_page'
    );
}
add_action('admin_menu', 'tv_effect_menu_page');

// Render the settings page
function tv_effect_settings_page() {
    ?>
    <div class="wrap">
        <h1> TV Effect Shortcode Guide</h1>
        <p>This plugin adds a retro TV screen animation effect to any image using a simple shortcode.</p>

        <h2> Shortcode Usage</h2>
         <pre>[image_trail]</pre>

        <pre>[image_trail image_url="https://yoursite.com/wp-content/uploads/your-image.jpg"]</pre>

        <h3> Attributes</h3>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Default</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>image_url</code></td>
                    <td><?php echo plugin_dir_url(__FILE__) . 'img/demo.jpg'; ?></td>
                    <td>URL of the image to display inside the TV screen.</td>
                </tr>
            </tbody>
        </table>

        <h3> Example</h3>
        <p>Display a custom image:</p>
        <pre>[image_trail image_url="<?php echo plugin_dir_url(__FILE__); ?>img/demo.jpg"]</pre>

        <h3> Styling & Animation</h3>
        <p>The effect is powered by CSS animations (scan lines, glow, etc.) and JavaScript (optional interactions). You can customize the styles in:</p>
        <code><?php echo plugin_dir_path(__FILE__) . 'assets/style.css'; ?></code>

 

        
    </div>
    <?php
}
// Optional: Create directory and files on activation
function tv_effect_activate() {
    $upload_dir = wp_upload_dir();
    $plugin_assets_dir = WP_CONTENT_DIR . '/uploads/tv-effect-plugin/';
    if (!file_exists($plugin_assets_dir)) {
        wp_mkdir_p($plugin_assets_dir);
    }
    // Copy demo.jpg if not exists
    if (!file_exists($plugin_assets_dir . 'demo.jpg')) {
        $default_image = plugin_dir_path(__FILE__) . 'demo.jpg';
        if (file_exists($default_image)) {
            copy($default_image, $plugin_assets_dir . 'demo.jpg');
        }
    }
}
register_activation_hook(__FILE__, 'tv_effect_activate');