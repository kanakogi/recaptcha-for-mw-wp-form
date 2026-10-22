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
        $site_key = '';
		if (is_array($option) && isset($option['site_key'])) {
			$site_key = esc_html($option['site_key']);
		}
        if (!empty($post) && has_shortcode($post->post_content, 'mwform_formkey') && !empty($site_key)) {
            wp_enqueue_script('jquery');
            wp_enqueue_script("recaptcha-script", 'https://www.google.com/recaptcha/api.js?render=' . $site_key, array('jquery'), false, true);

            // A token generated on page load can expire while the user fills in
            // the form. Generate it when the form is submitted instead.
            $data = <<<'EOL'
(function(siteKey) {
    document.addEventListener('submit', async function(event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.querySelector('input[name="recaptcha-v3"]')) return;
        if (form.dataset.recaptchaFresh === '1') {
            delete form.dataset.recaptchaFresh;
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        if (form.dataset.recaptchaBusy === '1') return;
        form.dataset.recaptchaBusy = '1';

        const submitter = event.submitter;
        try {
            if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') throw new Error('reCAPTCHA is unavailable');
            await Promise.race([
                new Promise(resolve => window.grecaptcha.ready(resolve)),
                new Promise((_, reject) => setTimeout(() => reject(new Error('reCAPTCHA timed out')), 10000))
            ]);
            form.querySelector('input[name="recaptcha-v3"]').value = await Promise.race([
                window.grecaptcha.execute(siteKey, { action: 'submit' }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('reCAPTCHA timed out')), 10000))
            ]);
            form.dataset.recaptchaFresh = '1';
            delete form.dataset.recaptchaBusy;
            setTimeout(() => submitter ? form.requestSubmit(submitter) : form.requestSubmit(), 0);
        } catch (error) {
            delete form.dataset.recaptchaBusy;
            let message = form.querySelector('.mwfrv3-error');
            if (!message) {
                message = document.createElement('small');
                message.className = 'mwfrv3-error';
                message.setAttribute('role', 'alert');
                message.setAttribute('tabindex', '-1');
                message.style.cssText = 'display:block;box-sizing:border-box;width:100%;padding:8px;margin:0 0 12px;background:#fff;color:#8b1d1d;font-size:16px;line-height:1.5;text-align:left';
                const anchor = submitter || form.querySelector('[type="submit"]');
                if (anchor && anchor.parentNode) anchor.parentNode.insertBefore(message, anchor);
                else form.appendChild(message);
            }
            message.textContent = 'reCAPTCHA could not be loaded. Please try again.';
            message.focus({ preventScroll: true });
            message.scrollIntoView({ block: 'center' });
        }
    }, true);
})(SITE_KEY);
EOL;
            $data = str_replace('SITE_KEY', wp_json_encode($site_key), $data);
            wp_add_inline_script('recaptcha-script', $data, 'before');
        }
    }
}
