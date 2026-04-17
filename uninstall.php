<?php
/**
 * Uninstall — remove all plugin options.
 *
 * @package LazyCaptcha
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = [
    'lazycaptcha_site_key',
    'lazycaptcha_secret_key',
    'lazycaptcha_base_url',
    'lazycaptcha_type',
    'lazycaptcha_theme',
    'lazycaptcha_protect_login',
    'lazycaptcha_protect_register',
    'lazycaptcha_protect_lostpw',
    'lazycaptcha_protect_comments',
    'lazycaptcha_protect_woo',
    'lazycaptcha_skip_logged_in',
];

foreach ($options as $opt) {
    delete_option($opt);
    delete_site_option($opt);
}
