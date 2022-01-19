<?php

namespace MW_WP_Form_reCAPTCHA\Controllers;

use MW_WP_Form_reCAPTCHA\Controllers\Controller;
use MW_WP_Form_reCAPTCHA\Config;
use MWF_Config;

class AdminController extends Controller
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'), 11);
    }

    public function admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=' . MWF_Config::NAME,
            esc_html__('reCAPTACHA v3', 'mw-wp-form'),
            esc_html__('reCAPTACHA v3', 'mw-wp-form'),
            MWF_Config::CAPABILITY,
            MWF_Config::NAME . '-recaptcha-v3',
            array(&$this, 'show_options_page')
        );
    }

    public function show_options_page()
    {
        $result = $this->save();
        $this->assign('result', $result);
        $this->assign('option', get_option(Config::OPTION));
        $this->render('admin');
    }

    private function save()
    {
        if (isset($_POST['save_setting']) && wp_verify_nonce($_POST['_wpnonce'], Config::NONCE)) {
            $option = get_option(Config::OPTION);

            // if (isset($_POST['is_recaptcha']) && $_POST['site_key'] == true) {
            //     $option['is_recaptcha'] = true;
            // } else {
            //     $option['is_recaptcha'] = false;
            // }

            if (isset($_POST['site_key']) && $_POST['site_key'] != '') {
                $option['site_key'] = sanitize_text_field($_POST['site_key']);
            }

            if (isset($_POST['secret_key']) && $_POST['secret_key'] != '') {
                $option['secret_key'] = sanitize_text_field($_POST['secret_key']);
            }

            if ( isset( $_POST['threshold_score'] ) && is_numeric( $_POST['threshold_score'] ) )
            {
                $val = (float) $_POST['threshold_score'];
                $val = min( max( $val, 0 ), 1 );
                $option['threshold_score'] = sprintf('%.2f', $val);
            }

            return update_option(Config::OPTION, $option);
        } else {
            return false;
        }
    }
}
