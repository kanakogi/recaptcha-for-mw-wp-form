<?php
/*
Plugin Name: reCAPTCHA for MW WP Form
Description: You can use reCAPTCHA v3 on the MW WP Form.
Author: Nakashima Masahiro
Version: 1.1.1
Plugin URI: https://github.com/kanakogi/recaptcha-for-mw-wp-form
License: GPLv2 or later
Text Domain: mwfrv3
 */

use MW_WP_Form_reCAPTCHA\Controllers\AdminController;
use MW_WP_Form_reCAPTCHA\Controllers\ValidationController;
use MW_WP_Form_reCAPTCHA\Controllers\EnqueueController;
use MW_WP_Form_reCAPTCHA\Config;

// MW WP Form プラグインを有効化をチェック
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('mw-wp-form/mw-wp-form.php')) {

    include plugin_dir_path(__FILE__) . '/config.php';

    class MW_WP_Form_reCAPTCHA
    {
        public $plugin_dir_path;
        private $default_options = array(
            // 'is_recaptcha' => '',
            'site_key' => '',
            'secret_key' => '',
			'threshold_score' => ''
        );

        public function __construct()
        {
            $this->plugin_dir_path = plugin_dir_path(__FILE__);

            // Load
            load_plugin_textdomain(Config::TEXTDOMAIN, false, basename(dirname(__FILE__)) . '/languages');
            add_action('plugins_loaded', array($this, 'load_init_files'), 1);
            add_action('plugins_loaded', array($this, 'init'), 11);
            // activation
            register_activation_hook(__FILE__, array($this, 'activation_hook'));
        }

        /**
         * ファイルのinclude.
         */
        public function load_init_files()
        {
            // MW WP Form
            $config_class = WP_PLUGIN_DIR . '/mw-wp-form/classes/config.php';
            $validation_rule_class = WP_PLUGIN_DIR . '/mw-wp-form/classes/abstract/class.validation-rule.php';
            if (file_exists($config_class) && file_exists($validation_rule_class)) {
                include_once $config_class;
                include_once $validation_rule_class;
            }
            // classes
            include $this->plugin_dir_path . '/classes/util.php';
            include $this->plugin_dir_path . '/classes/functions.php';
            include $this->plugin_dir_path . '/classes/recaptcha-v3.php';
            // controllers
            include $this->plugin_dir_path . '/controllers/Controller.php';
            include $this->plugin_dir_path . '/controllers/AdminController.php';
            include $this->plugin_dir_path . '/controllers/ValidationController.php';
            include $this->plugin_dir_path . '/controllers/EnqueueController.php';
        }

        /**
         * Initialize.
         */
        public function init()
        {
            new ValidationController();

            // 管理画面
            if (is_admin()) {
                new AdminController();
            }
            // フロント
            else {
                new EnqueueController();
            }
        }

        public function activation_hook()
        {
            if (!get_option(Config::OPTION)) {
                update_option(Config::OPTION, $this->default_options);
            }
        }
    }
    new MW_WP_Form_reCAPTCHA();
}
