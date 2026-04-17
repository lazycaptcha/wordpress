<?php
/**
 * Hooks LazyCaptcha into WordPress forms.
 *
 * @package LazyCaptcha
 */

if (!defined('ABSPATH')) {
    exit;
}

class LazyCaptcha_Forms
{
    public function __construct(private LazyCaptcha_Verifier $verifier)
    {
        // Login
        if (get_option('lazycaptcha_protect_login', '0') === '1') {
            add_action('login_form', [$this, 'render_login']);
            add_filter('authenticate', [$this, 'verify_login'], 30, 3);
        }

        // Registration
        if (get_option('lazycaptcha_protect_register', '1') === '1') {
            add_action('register_form', [$this, 'render_register']);
            add_filter('registration_errors', [$this, 'verify_register'], 10, 3);
        }

        // Lost password
        if (get_option('lazycaptcha_protect_lostpw', '1') === '1') {
            add_action('lostpassword_form', [$this, 'render_lostpw']);
            add_action('lostpassword_post', [$this, 'verify_lostpw'], 10, 1);
        }

        // Comments
        if (get_option('lazycaptcha_protect_comments', '1') === '1') {
            add_filter('comment_form_submit_field', [$this, 'append_to_comment_form'], 10, 2);
            add_filter('preprocess_comment', [$this, 'verify_comment']);
        }

        // WooCommerce (optional)
        if (get_option('lazycaptcha_protect_woo', '1') === '1') {
            add_action('woocommerce_register_form', [$this, 'render_register']);
            add_filter('woocommerce_process_registration_errors', [$this, 'verify_woo_register'], 10, 4);
            add_action('woocommerce_review_order_before_submit', [$this, 'render_checkout']);
            add_action('woocommerce_checkout_process', [$this, 'verify_woo_checkout']);
        }
    }

    private function should_skip_logged_in(): bool
    {
        return get_option('lazycaptcha_skip_logged_in', '1') === '1' && is_user_logged_in();
    }

    private function widget(): string
    {
        return LazyCaptcha::instance()->render_widget();
    }

    private function token_from_request(): string
    {
        $token = $_POST['lazycaptcha-token'] ?? '';
        return is_string($token) ? sanitize_text_field($token) : '';
    }

    // --- Login ---------------------------------------------------------------

    public function render_login(): void
    {
        echo $this->widget();
    }

    public function verify_login($user, string $username, string $password)
    {
        if ($user instanceof WP_User) {
            return $user;
        }
        if (empty($username) && empty($password)) {
            return $user;
        }
        if (!$this->verifier->check($this->token_from_request())) {
            return new WP_Error(
                'lazycaptcha_failed',
                __('<strong>Error:</strong> CAPTCHA verification failed. Please try again.', 'lazycaptcha')
            );
        }
        return $user;
    }

    // --- Registration --------------------------------------------------------

    public function render_register(): void
    {
        echo $this->widget();
    }

    public function verify_register($errors, string $sanitized_user_login = '', string $user_email = '')
    {
        if (!$this->verifier->check($this->token_from_request())) {
            $errors->add(
                'lazycaptcha_failed',
                __('<strong>Error:</strong> CAPTCHA verification failed.', 'lazycaptcha')
            );
        }
        return $errors;
    }

    // --- Lost password -------------------------------------------------------

    public function render_lostpw(): void
    {
        echo $this->widget();
    }

    public function verify_lostpw($errors): void
    {
        if (is_wp_error($errors) && !$this->verifier->check($this->token_from_request())) {
            $errors->add(
                'lazycaptcha_failed',
                __('<strong>Error:</strong> CAPTCHA verification failed.', 'lazycaptcha')
            );
        }
    }

    // --- Comments ------------------------------------------------------------

    public function append_to_comment_form(string $submit_field, array $args): string
    {
        if ($this->should_skip_logged_in()) {
            return $submit_field;
        }
        return $this->widget() . $submit_field;
    }

    public function verify_comment(array $commentdata): array
    {
        if ($this->should_skip_logged_in()) {
            return $commentdata;
        }
        if (!$this->verifier->check($this->token_from_request())) {
            wp_die(
                __('CAPTCHA verification failed. Please go back and try again.', 'lazycaptcha'),
                __('CAPTCHA failed', 'lazycaptcha'),
                ['back_link' => true]
            );
        }
        return $commentdata;
    }

    // --- WooCommerce ---------------------------------------------------------

    public function verify_woo_register($errors, $username, $password, $email)
    {
        if (!$this->verifier->check($this->token_from_request())) {
            $errors->add(
                'lazycaptcha_failed',
                __('CAPTCHA verification failed. Please try again.', 'lazycaptcha')
            );
        }
        return $errors;
    }

    public function render_checkout(): void
    {
        if ($this->should_skip_logged_in()) {
            return;
        }
        echo $this->widget();
    }

    public function verify_woo_checkout(): void
    {
        if ($this->should_skip_logged_in()) {
            return;
        }
        if (!$this->verifier->check($this->token_from_request())) {
            wc_add_notice(
                __('CAPTCHA verification failed. Please try again.', 'lazycaptcha'),
                'error'
            );
        }
    }
}
