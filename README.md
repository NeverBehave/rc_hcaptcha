# rc_hcaptcha

Adds hCaptcha to the RoundCube login form

# Installation

See "Getting Started" on [https://plugins.roundcube.net/](https://plugins.roundcube.net/)

Plugin name is "neverbehave/rc_hcaptcha"

# Configuration

Edit `config.inc.php` file in <Your-roundcube-install-basepath>/plugins/hcaptcha:

```php
<?php
// See https://hcaptcha.com/
$rcmail_config['hcaptcha_site_key'] = 'Your SITE KEY';
$rcmail_config['hcaptcha_secret_key'] = 'Your Secret Key';
$remail_config['hcaptcha_send_client_ip'] = false; // Set true to Sent Client IP to Hcaptcha
$rcmail_config['hcaptcha_theme']  = 'light';  // dark
```
