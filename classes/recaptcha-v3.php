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
		if( strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ) return '';

        /**
         * 入力値を取得
         */
        $value = $this->Data->get($name);
        $value = !empty($value) ? $value : '';

        /**
         * 設定値は $options から取得
         */
        $is_reCAPTCHA = isset($options['is_reCAPTCHA']) ? $options['is_reCAPTCHA'] : '';

        $plugin_option = get_option(Config::OPTION);
        $secret_key = isset($plugin_option['secret_key']) ? esc_html($plugin_option['secret_key']) : '';
		$threshold_score = isset($plugin_option['threshold_score']) ? (float) $plugin_option['threshold_score'] : 0;

        /**
         * 何らかのチェックをして、エラーがあったらエラーメッセージを返す
         */
        if ($is_reCAPTCHA == true) {
            if (!isset($secret_key) || $secret_key == '') {
                $defaults = array(
                    'message' => __('Enter reCAPTCHA Secret key.', Config::TEXTDOMAIN)
                );
                $options = array_merge($defaults, $options);
                return $options['message'];
            }

            if ($name == 'recaptcha-v3' && !isset($_POST['submitBack'])) {
                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $value;
                $response = wp_remote_get($url);
                if (!is_wp_error($response) && $response["response"]["code"] === 200) {
                    $reCAPTCHA = json_decode($response["body"]);

                    if ($reCAPTCHA->success) {
                        if ( $reCAPTCHA->score >= $threshold_score )
                        {
                            // 人間だからOK
                        }
                        else
                        {
                            $defaults = array(
                                'message' => __('Failed reCAPTCHA access.', Config::TEXTDOMAIN)
                            );
                            $options = array_merge($defaults, $options);
                            return $options['message'];
                        }
                    } else {
                        $defaults = array(
                            'message' => __('Invalid reCAPTCHA Secret key.', Config::TEXTDOMAIN)
                        );
                        $options = array_merge($defaults, $options);
                        return $options['message'];
                    }
                } else {
                    $defaults = array(
                        'message' => __('Failed reCAPTCHA access.', Config::TEXTDOMAIN)
                    );
                    $options = array_merge($defaults, $options);
                    return $options['message'];
                }
            }
        }

        return '';
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
