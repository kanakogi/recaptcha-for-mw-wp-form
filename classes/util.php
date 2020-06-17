<?php

namespace MW_WP_Form_reCAPTCHA\Classes;

class Util
{
    /**
     * ステータスに合わせてNoticeを表示する.
     *
     * @param string        $status 'updated' or 'error'
     * @param string|object $str    表示する文字列
     */
    public static function display_notice($status, $result)
    {
        if ($status == 'updated') {
            self::e_sccess($result);
        } elseif ($status == 'error') {
            if (is_wp_error($result)) {
                foreach ($result->errors as $error) {
                    self::e_error($error[0]);
                }
            } else {
                self::e_error('エラーです');
            }
        }
    }

    /**
     * Update Noticeを表示.
     *
     * @param string $str 表示する文字列
     */
    public static function e_sccess($str)
    {
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">';
        echo '<p><strong>' . $str . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
    }

    /**
     * Error Noticeを表示.
     *
     * @param string $str 表示する文字列
     */
    public static function e_error($str)
    {
        echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">';
        echo '<p><strong>' . $str . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
    }
}
