<?php

namespace MW_WP_Form_reCAPTCHA\Controllers;

use MW_WP_Form_reCAPTCHA\Classes\MW_WP_Form_ReCaptchaV3_Validation;

class ValidationController
{

    public function __construct()
    {
        add_filter('mwform_validation_rules', array($this, 'add_validation_recaptcha_v3_rules'));
    }

    function add_validation_recaptcha_v3_rules($validation_rules)
    {
        // 追加するバリデーションルールのオブジェクトは MW_Validation_Rule クラスを継承している必要があります。
        $instance = new MW_WP_Form_ReCaptchaV3_Validation();
        $validation_rules[$instance->get_name()] = $instance;
        return $validation_rules;
    }
}
