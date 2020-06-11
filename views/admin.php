<?php

use MW_WP_Form_reCAPTCHA_V3\Config;
use MW_WP_Form_reCAPTCHA_V3\Classes\Functions;
?>

<div class="wrap">

    <?php
    if ($result) {
        Functions::display_notice('updated', '保存しました。');
    }
    ?>

    <h1>reCAPTACHA v3 Setting</h1>

    <p>
        <a href="https://www.google.com/recaptcha/admin/create" target="_blank">
            ここからKeysを取得してください。
        </a>
    </p>

    <form method="post" action="" class="wpt-form" enctype="multipart/form-data">
        <?php wp_nonce_field(Config::NONCE); ?>

        <table class="form-table">
            <?php
            /*
             ?>
            <tr>
                <th>有効化</th>
                <td>
                    <input type="checkbox" name="is_recaptcha" class="" value="1" <?php if ($option['is_recaptcha']) : ?>checked<?php endif; ?>>
                </td>
            </tr>
            <?php
             */
            ?>
            <tr>
                <th>Site Key</th>
                <td>
                    <input type="text" name="site_key" class="regular-text" value="<?= $option['site_key'] ?>">
                </td>
            </tr>
            <tr>
                <th>Secret Key</th>
                <td>
                    <input type="text" name="secret_key" class="regular-text" value="">
                    <?php if ($option['secret_key'] != '') : ?>
                        <p class="description">データが保存されています</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <input type="hidden" name="save_setting" value="1">
        <?php submit_button(); ?>

    </form>

    <hr>
    <p>次のショートコードをMW WP FORM に入力してください。</p>
    <textarea style="width:500px; height: 4em; resize: none;" readonly>
[mwform_hidden name="recaptcha-v3"]
[mwform_error keys="recaptcha-v3"]
</textarea>

</div>