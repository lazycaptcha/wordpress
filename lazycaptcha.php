<?php
/**
 * Plugin Name:       LazyCaptcha
 * Plugin URI:        https://lazycaptcha.com
 * Description:       Self-hostable, privacy-friendly CAPTCHA for WordPress. Protects login, registration, comments, lost password, and WooCommerce checkout. Drop-in alternative to hCaptcha and reCAPTCHA.
 * Version:           0.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            LazyCaptcha
 * Author URI:        https://lazycaptcha.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       lazycaptcha
 * Domain Path:       /languages
 *
 * @package LazyCaptcha
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LAZYCAPTCHA_VERSION', '0.1.0');
define('LAZYCAPTCHA_FILE', __FILE__);
define('LAZYCAPTCHA_DIR', plugin_dir_path(__FILE__));
define('LAZYCAPTCHA_URL', plugin_dir_url(__FILE__));

require_once LAZYCAPTCHA_DIR . 'includes/class-lazycaptcha-verifier.php';
require_once LAZYCAPTCHA_DIR . 'includes/class-lazycaptcha-forms.php';
require_once LAZYCAPTCHA_DIR . 'includes/class-lazycaptcha-admin.php';
require_once LAZYCAPTCHA_DIR . 'includes/class-lazycaptcha.php';

add_action('plugins_loaded', function () {
    LazyCaptcha::instance();
});

register_activation_hook(__FILE__, function () {
    // Seed default options on activation
    $defaults = [
        'site_key'           => '',
        'secret_key'         => '',
        'base_url'           => 'https://lazycaptcha.com',
        'type'               => 'auto',
        'theme'              => 'light',
        'protect_login'      => '0',
        'protect_register'   => '1',
        'protect_lostpw'     => '1',
        'protect_comments'   => '1',
        'protect_woo'        => '1',
        'skip_logged_in'     => '1',
    ];
    foreach ($defaults as $key => $value) {
        if (get_option("lazycaptcha_{$key}") === false) {
            add_option("lazycaptcha_{$key}", $value);
        }
    }
});
