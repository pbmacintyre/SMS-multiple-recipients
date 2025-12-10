<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

page_header(0);

function show_form($message, $print_again = false) {

    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="EditTable">
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <img src="images/rc-logo.png"/>
                    <h2><?php echo app_name(); ?></h2>
                    <?php
                    if ($print_again) {
                        echo "<p class='msg_bad'>" . $message . "</p>";
                    } else {
                        echo "<p class='msg_good'>" . $message . "</p>";
                    } ?>
                    <hr>
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>1st Receiving SMS #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_numbers[]">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>2nd Receiving SMS #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_numbers[]">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>3rd Receiving SMS #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_numbers[]">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>SMS Message:</p>
                </td>
                <td class="right_col">
                    <textarea name="sms_message"><?php if ($print_again) { echo strip_tags($_POST['sms_message']); } ?></textarea>
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value="   Send SMS   " name="send_sms">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <hr>
                </td>
            </tr>
        </table>
    </form>
    <?php
}

function check_form() {
    show_errors();

    $print_again = false;
    $message = "";

    /* ============================================ */
    /* ====== START data integrity checks ========= */
    /* ============================================ */

    $sms_message = strip_tags($_POST['sms_message']);

    if ($sms_message == "") {
        $print_again = true;
        $message = "No SMS message body has been provided";
    }

    $all_blank = empty(array_filter($_POST['to_sms_numbers'], function($v) {
        return trim($v) !== '';
    }));

    if ($all_blank) {
        $print_again = true;
        $message = "No receiving SMS Numbers have been provided";
    } else {
        $to_sms_numbers = [];
        foreach ($_POST['to_sms_numbers'] as $number) {
            if (strip_tags($number) != '') {
                $to_sms_numbers[] = ['phoneNumber' => strip_tags($number)];
            }
        }
    }

    /* ========================================== */
    /* ====== END data integrity checks ========= */
    /* ========================================== */
    if ($print_again) {
        show_form($message, $print_again);
    } else {
        send_sms($to_sms_numbers, $sms_message);
        $message = "SMS message has been sent Successfully";
        show_form($message);
    }

}


/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['send_sms'])) {
    check_form();
} else {
    $message = "Please provide the needed information.";
    show_form($message);
}

ob_end_flush();
page_footer();
