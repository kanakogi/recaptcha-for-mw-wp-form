<?php

namespace MW_WP_Form_reCAPTCHA;

class Config
{
    // const MODE = 'DEV'; // PRD, STG, DEV
    const NAME = 'mwfrv3';
    const OPTION = 'mwfrv3';
    const NONCE = 'mwfrv3-nonce';
    const TEXTDOMAIN = 'recaptcha-for-mw-wp-form';

    public static function plugin_url()
    {
        return plugin_dir_url(__FILE__);
    }

    public static function plugin_dir()
    {
        return plugin_dir_path(__FILE__);
    }
}
