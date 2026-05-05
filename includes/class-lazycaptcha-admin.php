<?php
/**
 * Admin settings page.
 *
 * @package LazyCaptcha
 */

if (!defined('ABSPATH')) {
    exit;
}

class LazyCaptcha_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('plugin_action_links_' . plugin_basename(LAZYCAPTCHA_FILE), [$this, 'settings_link']);
    }

    public function register_menu(): void
    {
        add_options_page(
            __('LazyCaptcha', 'lazycaptcha'),
            __('LazyCaptcha', 'lazycaptcha'),
            'manage_options',
            'lazycaptcha',
            [$this, 'render_page']
        );
    }

    public function settings_link(array $links): array
    {
        $url = admin_url('options-general.php?page=lazycaptcha');
        array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'lazycaptcha') . '</a>');
        return $links;
    }

    public function register_settings(): void
    {
        $fields = [
            'site_key', 'secret_key', 'base_url', 'type', 'theme', 'widget', 'width',
            'protect_login', 'protect_register', 'protect_lostpw',
            'protect_comments', 'protect_woo', 'skip_logged_in',
        ];
        foreach ($fields as $field) {
            register_setting('lazycaptcha', "lazycaptcha_{$field}");
        }
    }

    public function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('LazyCaptcha Settings', 'lazycaptcha'); ?></h1>

            <?php if (isset($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Settings saved.', 'lazycaptcha'); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('lazycaptcha'); ?>

                <h2 class="title"><?php esc_html_e('API Keys', 'lazycaptcha'); ?></h2>
                <p><?php printf(
                    wp_kses(
                        __('Get your keys from the <a href="%s" target="_blank" rel="noopener">LazyCaptcha dashboard</a>.', 'lazycaptcha'),
                        ['a' => ['href' => [], 'target' => [], 'rel' => []]]
                    ),
                    esc_url('https://lazycaptcha.com')
                ); ?></p>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="lazycaptcha_site_key"><?php esc_html_e('Site Key', 'lazycaptcha'); ?></label></th>
                        <td><input type="text" id="lazycaptcha_site_key" name="lazycaptcha_site_key" value="<?php echo esc_attr(get_option('lazycaptcha_site_key', '')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="lazycaptcha_secret_key"><?php esc_html_e('Secret Key', 'lazycaptcha'); ?></label></th>
                        <td><input type="password" id="lazycaptcha_secret_key" name="lazycaptcha_secret_key" value="<?php echo esc_attr(get_option('lazycaptcha_secret_key', '')); ?>" class="regular-text" autocomplete="new-password" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="lazycaptcha_base_url"><?php esc_html_e('LazyCaptcha URL', 'lazycaptcha'); ?></label></th>
                        <td>
                            <input type="url" id="lazycaptcha_base_url" name="lazycaptcha_base_url" value="<?php echo esc_attr(get_option('lazycaptcha_base_url', 'https://lazycaptcha.com')); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Default: https://lazycaptcha.com. Change only if self-hosting.', 'lazycaptcha'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php esc_html_e('Appearance', 'lazycaptcha'); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="lazycaptcha_type"><?php esc_html_e('Challenge type', 'lazycaptcha'); ?></label></th>
                        <td>
                            <select id="lazycaptcha_type" name="lazycaptcha_type">
                                <?php $current_type = get_option('lazycaptcha_type', 'auto'); ?>
                                <option value="auto" <?php selected($current_type, 'auto'); ?>><?php esc_html_e('Auto', 'lazycaptcha'); ?></option>
                                <option value="image_puzzle" <?php selected($current_type, 'image_puzzle'); ?>><?php esc_html_e('Image puzzles', 'lazycaptcha'); ?></option>
                                <option value="pow" <?php selected($current_type, 'pow'); ?>><?php esc_html_e('Proof of Work (invisible)', 'lazycaptcha'); ?></option>
                                <option value="behavioral" <?php selected($current_type, 'behavioral'); ?>><?php esc_html_e('Behavioral (invisible)', 'lazycaptcha'); ?></option>
                                <option value="text_math" <?php selected($current_type, 'text_math'); ?>><?php esc_html_e('Text / Math', 'lazycaptcha'); ?></option>
                                <option value="press_hold" <?php selected($current_type, 'press_hold'); ?>><?php esc_html_e('Press and Hold', 'lazycaptcha'); ?></option>
                                <option value="rotate_align" <?php selected($current_type, 'rotate_align'); ?>><?php esc_html_e('Rotate to Align (high-friction)', 'lazycaptcha'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="lazycaptcha_theme"><?php esc_html_e('Theme', 'lazycaptcha'); ?></label></th>
                        <td>
                            <select id="lazycaptcha_theme" name="lazycaptcha_theme">
                                <?php $current_theme = get_option('lazycaptcha_theme', 'auto'); ?>
                                <option value="light" <?php selected($current_theme, 'light'); ?>><?php esc_html_e('Light', 'lazycaptcha'); ?></option>
                                <option value="dark" <?php selected($current_theme, 'dark'); ?>><?php esc_html_e('Dark', 'lazycaptcha'); ?></option>
                                <option value="auto" <?php selected($current_theme, 'auto'); ?>><?php esc_html_e('Auto', 'lazycaptcha'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="lazycaptcha_widget"><?php esc_html_e('Widget preset', 'lazycaptcha'); ?></label></th>
                        <td>
                            <select id="lazycaptcha_widget" name="lazycaptcha_widget">
                                <?php $current_widget = get_option('lazycaptcha_widget', 'standard'); ?>
                                <option value="standard" <?php selected($current_widget, 'standard'); ?>><?php esc_html_e('Standard', 'lazycaptcha'); ?></option>
                                <option value="compact" <?php selected($current_widget, 'compact'); ?>><?php esc_html_e('Compact', 'lazycaptcha'); ?></option>
                                <option value="newsletter" <?php selected($current_widget, 'newsletter'); ?>><?php esc_html_e('Newsletter', 'lazycaptcha'); ?></option>
                                <option value="login" <?php selected($current_widget, 'login'); ?>><?php esc_html_e('Login', 'lazycaptcha'); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e('Newsletter stays intentionally skinny. Login keeps the sequence UI minimal.', 'lazycaptcha'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="lazycaptcha_width"><?php esc_html_e('Width override', 'lazycaptcha'); ?></label></th>
                        <td>
                            <input type="text" id="lazycaptcha_width" name="lazycaptcha_width" value="<?php echo esc_attr(get_option('lazycaptcha_width', '')); ?>" class="regular-text" placeholder="420px" />
                            <p class="description"><?php esc_html_e('Optional CSS width value. The hosted widget caps widths at 500px.', 'lazycaptcha'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php esc_html_e('Protected forms', 'lazycaptcha'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php
                    $toggles = [
                        'protect_login'    => __('Login form', 'lazycaptcha'),
                        'protect_register' => __('Registration form', 'lazycaptcha'),
                        'protect_lostpw'   => __('Lost password form', 'lazycaptcha'),
                        'protect_comments' => __('Comment form', 'lazycaptcha'),
                        'protect_woo'      => __('WooCommerce register &amp; checkout (if WooCommerce is active)', 'lazycaptcha'),
                        'skip_logged_in'   => __('Skip CAPTCHA for logged-in users', 'lazycaptcha'),
                    ];
                    foreach ($toggles as $key => $label) :
                        $val = get_option("lazycaptcha_{$key}", '0');
                        ?>
                        <tr>
                            <th scope="row"><label for="lazycaptcha_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                            <td>
                                <input type="hidden" name="lazycaptcha_<?php echo esc_attr($key); ?>" value="0" />
                                <input type="checkbox" id="lazycaptcha_<?php echo esc_attr($key); ?>" name="lazycaptcha_<?php echo esc_attr($key); ?>" value="1" <?php checked($val, '1'); ?> />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <?php submit_button(); ?>
            </form>

            <hr>
            <h2 class="title"><?php esc_html_e('Usage', 'lazycaptcha'); ?></h2>
            <p><?php esc_html_e('Insert the widget anywhere with the shortcode:', 'lazycaptcha'); ?></p>
            <p><code>[lazycaptcha]</code></p>
            <p><code>[lazycaptcha widget="newsletter"]</code></p>
            <p><code>[lazycaptcha widget="standard" width="420px"]</code></p>
            <p><?php esc_html_e('Or in Gutenberg, add the "LazyCaptcha" block.', 'lazycaptcha'); ?></p>
        </div>
        <?php
    }
}
