<?php

namespace MW_WP_Form_reCAPTCHA_V3\Classes;

class Util
{
    // public $option;

    /**
     * POST値GET.
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    public static function input($key)
    {
        if (isset($_REQUEST[$key]) && $_REQUEST[$key] != '') {
            return self::h($_REQUEST[$key]);
        } else {
            return false;
        }
    }

    /**
     * POST値存在チェック.
     */
    public static function is($key)
    {
        if (isset($_REQUEST[$key]) && !empty($_REQUEST[$key])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * チェックボックス用.
     */
    public static function checked($value, $target)
    {
        if (!is_array($target)) {
            $target = array($target);
        }
        if (!is_array($value)) {
            $value = array($value);
        }
        $is_checked = false;
        foreach ($value as $key => $val) {
            if (in_array($val, $target)) {
                echo 'checked="checked"';
                $is_checked = true;
                break;
            }
        }

        return $is_checked;
    }

    /**
     * hメソッド.
     */
    public static function h($string)
    {
        if (is_array($string)) {
            return array_map(array('self', 'h'), $string);
        } else {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * ddメソッド.
     */
    public static function dd($value)
    {
        print_r($value);
        die;
    }

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

    /**
     * WPオプションに保存する.
     *
     * @param array $option 保存するデータ
     *
     * @return bool|object
     */
    public static function save_option($option)
    {
        $saved_option = get_option(Config::OPTION);
        if (is_array($saved_option) || !empty($saved_option)) {
            $option = array_merge($saved_option, $option);
        }
        if (update_option(Config::OPTION, $option)) {
            return true;
        } else {
            return new \WP_Error('error', 'エラーです。');
        }
    }

    /**
     * キーを指定してOPTIONデータを取得.
     *
     * @param string $key 取得するキー
     *
     * @return string,int,array
     */
    public static function get_option($key)
    {
        $option = get_option(Config::OPTION);

        return $option[$key];
    }

    /**
     * postにサムネイルを登録.
     *
     * @param [type] $posted_id [description]
     * @param [type] $url       [description]
     */
    public static function add_thumbnail($posted_id, $url)
    {
        //アップロードディレクトリ取得
        $wp_upload_dir = wp_upload_dir();
        //ファイル名取得
        $filename = basename($url);
        //ダウンロード後ファイルパス
        $filepath = $wp_upload_dir['path'] . '/' . $filename;
        //画像をダウンロード＆保存
        $image_data = file_get_contents($url);
        file_put_contents($filepath, $image_data);
        //ファイル属性取得
        $wp_filetype = wp_check_filetype($filepath, null);
        //添付ファイル情報設定
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . $filepath,
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $filename,
            'post_content' => '',
            'post_status' => 'inherit',
        );
        //添付ファイル登録
        $attach_id = wp_insert_attachment($attachment, $filepath, $posted_id);
        //サムネイル画像作成
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, $attach_data);
        //サムネイルID登録
        add_post_meta($posted_id, '_thumbnail_id', $attach_id, true);

        return $attach_id;
    }

    /**
     * viewsディレクトリのファイルを読み込む.
     *
     * @param string $file
     *
     * @return string
     */
    public static function view($file, $data = null)
    {
        extract($data);
        // 子テーマ→親テーマ→プラグインの順で、優先的に読み込む
        $childe_view_path = get_stylesheet_directory() . '/' . Config::NAME . '/' . $file . '.php';
        $parent_view_path = get_template_directory() . '/' . Config::NAME . '/' . $file . '.php';
        $plugin_view_path = Config::plugin_dir() . 'views/' . $file . '.php';
        if (file_exists($childe_view_path)) {
            $view_path = $childe_view_path;
        } elseif (file_exists($parent_view_path)) {
            $view_path = $parent_view_path;
        } else {
            $view_path = $plugin_view_path;
        }
        ob_start();
        require $view_path;
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}
