# LazyCaptcha for WordPress

Self-hostable, privacy-friendly CAPTCHA plugin for WordPress. Drop-in alternative to hCaptcha and reCAPTCHA.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Features

- Protects **login, registration, lost password, comments, WooCommerce register + checkout**
- `[lazycaptcha]` shortcode — drop the widget anywhere
- **Gutenberg block** with type/theme controls in the inspector
- Four challenge types: image puzzles, proof-of-work, behavioral, text/math
- Server-to-server verification using `wp_remote_post`
- Skip-for-logged-in-users toggle
- Configurable for self-hosted LazyCaptcha instances

## Installation

### From WordPress.org (once published)

Search for "LazyCaptcha" under **Plugins → Add New** and click Install.

### From GitHub

1. Download the latest release ZIP from the [Releases](../../releases) page
2. **Plugins → Add New → Upload Plugin** → upload the ZIP
3. Activate

### From source

```bash
cd wp-content/plugins/
git clone https://github.com/yourusername/lazycaptcha-wordpress.git lazycaptcha
```

Then activate through WordPress admin.

## Configuration

**Settings → LazyCaptcha**

| Setting | Default | Purpose |
|---------|---------|---------|
| Site Key | — | Public UUID from your LazyCaptcha dashboard |
| Secret Key | — | Private key for server-side verification |
| LazyCaptcha URL | `https://lazycaptcha.com` | Your instance URL |
| Challenge type | `auto` | Image / PoW / behavioral / text-math |
| Theme | `light` | Widget appearance |
| Protect login | Off | |
| Protect registration | On | |
| Protect lost password | On | |
| Protect comments | On | |
| Protect WooCommerce | On | |
| Skip for logged-in users | On | |

## Shortcode

```
[lazycaptcha]
[lazycaptcha type="image_puzzle" theme="dark"]
```

## Gutenberg block

Search for "LazyCaptcha" in the block inserter. The block renders a placeholder in the editor and the real widget on the front end (server-rendered via `render_callback`).

## Developer API

The `LazyCaptcha_Verifier` class is exposed if you want to verify tokens in your own code:

```php
$verifier = LazyCaptcha::instance()->verifier;
$result = $verifier->verify($_POST['lazycaptcha-token']);
if (!$result['success']) {
    wp_die('Captcha failed');
}
```

## WooCommerce integration

When WooCommerce is active and **Protect WooCommerce** is on, the widget appears on:
- Customer registration (`woocommerce_register_form`)
- Checkout (before the place-order button)

Verification uses `wc_add_notice()` for user-facing errors.

## License

[MIT](LICENSE)
