<?php

namespace MW_WP_Form_reCAPTCHA\Controllers;

use MW_WP_Form_reCAPTCHA\Config;

class EnqueueController
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
    }

    public function add_scripts()
    {
        global $post;
        $option = get_option(Config::OPTION);
        $site_key = esc_html($option['site_key']);
        if (!empty($post) && has_shortcode($post->post_content, 'mwform_formkey') && !empty($site_key)) {
            wp_enqueue_script('jquery');
            wp_enqueue_script("recaptcha-script", 'https://www.google.com/recaptcha/api.js?render=' . $site_key, array('jquery'), array(), true);

            $data = <<< EOL
grecaptcha.ready(function() {
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        grecaptcha.execute('$site_key', {
            action: 'homepage'
        }).then(function(token) {
            var recaptchaResponse = jQuery('input[name="recaptcha-v3"]');
            recaptchaResponse.val(token);
            let form = e.target;
            if (form.querySelector("[name=submitConfirm]")) {
                const confirmButtonValue = form.querySelector("[name=submitConfirm]").value;
                const confirmButton = document.createElement("input");
                confirmButton.type = "hidden";
                confirmButton.value = confirmButtonValue;
                confirmButton.name = "submitConfirm";
                form.appendChild(confirmButton);
            } else if (e.submitter.name == 'submitBack' && form.querySelector("[name=submitBack]")) {
                const backButtonValue = form.querySelector("[name=submitBack]").value;
                const backButton = document.createElement("input");
                backButton.type = "hidden";
                backButton.value = backButtonValue;
                backButton.name = "submitBack";
                form.appendChild(backButton);
            }
            form.submit();
        });
    });
});
EOL;
            wp_add_inline_script('recaptcha-script', $data);
        }
    }
}
