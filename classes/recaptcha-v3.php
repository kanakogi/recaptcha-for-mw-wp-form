<?php

namespace MW_WP_Form_reCAPTCHA\Classes;

use MW_WP_Form_reCAPTCHA\Config;
use MWF_Config;
use MW_WP_Form_Abstract_Validation_Rule;

class MW_WP_Form_ReCaptchaV3_Validation extends MW_WP_Form_Abstract_Validation_Rule
{
    /**
     * バリデーションルールの名前を定義
     */
    protected $name = 'recaptcha_v3';

    /**
     * 入力チェックをする関数
     */
    public function rule($name, array $options = array())
    {
        /**
         * 入力値を取得
         */
        $value = $this->Data->get($name);

        /**
         * 設定値は $options から取得
         */
        $is_reCAPTCHA = isset($options['is_reCAPTCHA']) ? $options['is_reCAPTCHA'] : '';

        $plugin_option = get_option(Config::OPTION);
        $secret_key = isset($plugin_option['secret_key']) ? $plugin_option['secret_key'] : '';

        /**
         * 何らかのチェックをして、エラーがあったらエラーメッセージを返す
         */
        if ($is_reCAPTCHA == true) {
            if (!isset($secret_key) || $secret_key == '') {
                $defaults = array(
                    'message' => 'reCAPTCHA Secret key を入力してください。'
                );
                $options = array_merge($defaults, $options);
                return $options['message'];
            }

            if (isset($value) && $value != '' && $name == 'recaptcha-v3' && isset($_POST['recaptcha-v3']) && !isset($_POST['submitBack'])) {
                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $value;
                $verifyResponse = file_get_contents($url);
                $reCAPTCHA = json_decode($verifyResponse);

                if ($reCAPTCHA->success) {
                    // 人間だからOK
                } else {
                    $defaults = array(
                        'message' => 'reCAPTCHA Secret key が不正です。'
                    );
                    $options = array_merge($defaults, $options);
                    return $options['message'];
                }
            }
        }
    }

    /**
     * フォーム編集画面の「バリデーションルール」に設定を追加する
     */
    public function admin($key, $value)
    {
        if (is_array($value[$this->getName()]) && isset($value[$this->getName()]['is_reCAPTCHA'])) {
            $is_reCAPTCHA = $value[$this->getName()]['is_reCAPTCHA'];
        }
        ?>
        <table>
            <tr>
                <td>reCAPTCHA V3</td>
                <td><input type="checkbox" value="1" name="<?php echo MWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr($this->getName()); ?>][is_reCAPTCHA]" <?php if ($is_reCAPTCHA) : ?>checked<?php endif; ?> /></td>
            </tr>
        </table>
<?php
    }
}
