<?php
/**
 * Main LazyCaptcha orchestrator.
 *
 * @package LazyCaptcha
 */

if (!defined('ABSPATH')) {
    exit;
}

class LazyCaptcha
{
    private static ?LazyCaptcha $instance = null;

    public LazyCaptcha_Verifier $verifier;
    public LazyCaptcha_Forms $forms;
    public LazyCaptcha_Admin $admin;

    public static function instance(): LazyCaptcha
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->verifier = new LazyCaptcha_Verifier();
        $this->forms    = new LazyCaptcha_Forms($this->verifier);
        $this->admin    = new LazyCaptcha_Admin();

        add_action('wp_enqueue_scripts', [$this, 'enqueue_widget_script']);
        add_action('login_enqueue_scripts', [$this, 'enqueue_widget_script']);
        add_shortcode('lazycaptcha', [$this, 'shortcode']);
        add_action('init', [$this, 'register_block']);
        add_action('init', [$this, 'load_textdomain']);
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain('lazycaptcha', false, dirname(plugin_basename(LAZYCAPTCHA_FILE)) . '/languages');
    }

    public function enqueue_widget_script(): void
    {
        $base = rtrim(get_option('lazycaptcha_base_url', 'https://lazycaptcha.com'), '/');
        wp_enqueue_script(
            'lazycaptcha-widget',
            $base . '/api/captcha/v1/lazycaptcha.js',
            [],
            LAZYCAPTCHA_VERSION,
            true
        );
    }

    public function render_widget(array $atts = []): string
    {
        $site_key = trim((string) get_option('lazycaptcha_site_key', ''));
        if ($site_key === '') {
            if (current_user_can('manage_options')) {
                return '<p style="color:#b00;">' . esc_html__('LazyCaptcha site key is not configured.', 'lazycaptcha') . '</p>';
            }
            return '';
        }

        $type  = esc_attr($atts['type']  ?? get_option('lazycaptcha_type', 'auto'));
        $theme = esc_attr($atts['theme'] ?? get_option('lazycaptcha_theme', 'auto'));

        return sprintf(
            '<div class="lazycaptcha" data-sitekey="%s" data-type="%s" data-theme="%s"></div>',
            esc_attr($site_key),
            $type,
            $theme
        );
    }

    public function shortcode($atts): string
    {
        $atts = shortcode_atts([
            'type'  => '',
            'theme' => '',
        ], $atts, 'lazycaptcha');
        return $this->render_widget(array_filter($atts));
    }

    public function register_block(): void
    {
        if (!function_exists('register_block_type')) {
            return;
        }
        register_block_type(LAZYCAPTCHA_DIR . 'blocks/lazycaptcha', [
            'render_callback' => function ($attributes) {
                return $this->render_widget($attributes);
            },
        ]);
    }
}
