<?php

use MW_WP_Form_reCAPTCHA\Config;
use MW_WP_Form_reCAPTCHA\Classes\Functions;
?>

<div class="wrap">

    <?php
    if ($result) {
        Functions::display_notice('updated', __('Saved.', Config::TEXTDOMAIN));
    }
    ?>

    <h1><?php _e('reCAPTACHA v3 Setting', Config::TEXTDOMAIN) ?></h1>

    <p>
        <?php _e('Get Keys from <a href="https://www.google.com/recaptcha/admin/create" target="_blank">here</a>.', Config::TEXTDOMAIN) ?>
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
                        <p class="description"><?php _e('Secret Key has been saved.', Config::TEXTDOMAIN) ?></p>
                    <?php endif; ?>
                </td>
            </tr>
			<tr>
				<th>Threshold score (0.00 - 1.00)</th>
				<td>
					<input type="number" name="threshold_score" min="0" max="1" step="0.01" class="regular-text" value="<?= $option['threshold_score']?>">
				</td>
			</tr>
        </table>

        <input type="hidden" name="save_setting" value="1">
        <?php submit_button(); ?>

    </form>

    <hr>
    <h2><?php _e('Usage', Config::TEXTDOMAIN) ?></h2>
    <p><?php _e('Step1: Please enter the following short code into the MW WP FORM.', Config::TEXTDOMAIN) ?></p>
    <textarea style="width:500px; height: 4em; resize: none;" readonly>
[mwform_hidden name="recaptcha-v3"]
[mwform_error keys="recaptcha-v3"]
</textarea>
    <div><img src="<?= Config::plugin_url() ?>assets/img/admin/pic-0.png" alt=""></div>

    <p style="margin-top: 30px"><?php _e('Step2: Check the reCAPTCHA V3 of Validation Rule.', Config::TEXTDOMAIN) ?></p>
    <div><img src="<?= Config::plugin_url() ?>assets/img/admin/pic-1.png" alt=""></div>

</div>
