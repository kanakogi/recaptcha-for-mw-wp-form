<?php

namespace MW_WP_Form_reCAPTCHA_V3\Controllers;

use MW_WP_Form_reCAPTCHA_V3\Config;

class EnqueueController
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
    }

    public function add_scripts()
    {
        global $post;
        if (has_shortcode($post->post_content, 'mwform_formkey')) {
            wp_enqueue_script('jquery');
            $option = get_option(Config::OPTION);
            $site_key = $option['site_key'];
            wp_enqueue_script("recaptcha-script", 'https://www.google.com/recaptcha/api.js?render=' . $site_key, array('jquery'), array(), true);

            $data = <<< EOL
grecaptcha.ready(function() {
    grecaptcha.execute('$site_key', {
        action: 'homepage'
    }).then(function(token) {
        var recaptchaResponse = jQuery('input[name="recaptcha-v3"]');
        recaptchaResponse.val(token);
    });
});
EOL;
            wp_add_inline_script('recaptcha-script', $data);
        }
    }
}
