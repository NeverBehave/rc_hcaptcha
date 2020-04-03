<?php

class hcaptcha extends rcube_plugin
{
    public function init()
    {
        $this->load_config();

        $rcmail = rcmail::get_instance();
        if ($rcmail->config->get('hcaptcha_public_key') != '' && $rcmail->config->get('hcaptcha_secret_key') != '') {
            $this->add_hook('template_object_loginform', [$this, 'template_object_loginform']);
            $this->add_hook('authenticate', [$this, 'authenticate']);
        }
    }

    public function template_object_loginform(array $loginform): array
    {
        $rcmail = rcmail::get_instance();
        $key = $rcmail->config->get('hcaptcha_public_key');
        $theme = $rcmail->config->get('hcaptcha_theme') ?? 'dark';

        $src = "https://hcaptcha.com/1/api.js?hl=" . urlencode($rcmail->user->language);
        $script = html::tag('script', ['type' => "text/javascript", 'src' => $src]);
        $this->include_script($src);

        $loginform['content'] = str_ireplace(
            '</tbody>',
            '<tr><td class="title"></td><td class="input"><div class="h-captcha" data-theme="' . html::quote($theme) . '" data-sitekey="' . html::quote($key) . '"></div></td></tr></tbody>',
            $loginform['content']
        );

        return $loginform;
    }

    public function authenticate(array $args)
    {
        $rcmail = rcmail::get_instance();
        $secret = $rcmail->config->get('hcaptcha_secret_key');
        $hcaptcha = new \neverbehave\Hcaptcha($secret);
        $cf = new \CloudFlare\IpRewrite();

        $response = filter_input(INPUT_POST, 'g-hcaptcha-response');
        $ip = $cf->isCloudFlare() ? $cf->getRewrittenIP() : rcube_utils::remote_addr();
        $result = null;
        if ($rcmail->config->get('hcaptcha_send_client_ip')) {
            $result = $hcaptcha->challenge($response);
        } else {
            $result = $hcaptcha->challenge($response, $ip);
        }

        if ($result->isSuccess()) {
            return $args;
        }

        $this->add_texts('localization/');
        $rcmail->output->show_message('hcaptcha.hcaptchafailed', 'error');
        $rcmail->output->set_env('task', 'login');
        $rcmail->output->send('login');
        return null;
    }
}
