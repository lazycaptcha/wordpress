=== LazyCaptcha ===
Contributors: lazycaptcha
Tags: captcha, anti-spam, bot protection, hcaptcha, recaptcha
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Self-hostable, privacy-friendly CAPTCHA for WordPress. Drop-in alternative to hCaptcha and reCAPTCHA.

== Description ==

LazyCaptcha is a CAPTCHA plugin for WordPress that you can self-host, keeping your visitors' data under your control. It protects your forms with four challenge types:

* **Image puzzles** — grid selection, slide-to-fit, rotate-to-align
* **Proof of Work** — invisible SHA-256 puzzle solved in a Web Worker
* **Behavioral** — silent scoring from mouse movement and timing
* **Text / Math** — distorted text or arithmetic

**What it protects out of the box:**
* Login form
* Registration form
* Lost password form
* Comment form
* WooCommerce register & checkout

**Also includes:**
* `[lazycaptcha]` shortcode — drop the widget anywhere
* Gutenberg block with inspector controls
* Admin settings page with all four challenge types
* Skip-for-logged-in-users toggle
* Support for self-hosted LazyCaptcha instances

== Installation ==

1. Upload the `lazycaptcha` folder to `/wp-content/plugins/`, or install via **Plugins → Add New**
2. Activate through the **Plugins** menu
3. Go to **Settings → LazyCaptcha** and enter your Site Key and Secret Key from [lazycaptcha.com](https://lazycaptcha.com)
4. Pick which forms to protect and save

== Frequently Asked Questions ==

= Is LazyCaptcha really self-hostable? =

Yes. The entire LazyCaptcha service is open source (MIT). This plugin points at the hosted service by default, but you can change the **LazyCaptcha URL** setting to your own instance.

= Does this work with third-party form plugins? =

Contact Form 7, Gravity Forms, and WPForms have their own captcha integration APIs. For now this plugin covers core WP forms + WooCommerce. Third-party bridges are on the roadmap — or you can use the `[lazycaptcha]` shortcode inside those form builders' HTML fields.

= Will this slow down my site? =

No. The widget script loads async/defer, and the actual challenge only runs when a user interacts with it. Verification is a single server-to-server POST on form submit.

== Changelog ==

= 0.1.0 =
* Initial release
* Login, registration, lost-password, comments, WooCommerce hooks
* Gutenberg block + shortcode
* Admin settings page
