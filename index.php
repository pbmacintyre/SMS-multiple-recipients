<?php
/**
 * Copyright (C) 2019-2024 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

page_header(0);

function show_form($message, $label = "", $print_again = false) {

    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="EditTable">
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <img src="images/rc-logo.png"/>
                    <h2><?php echo app_name(); ?></h2>
                    <?php
                    if ($print_again == true) {
                        echo "<p class='msg_bad'>" . $message . "</strong></font>";
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
                    <input type="text" name="to_sms_number_1">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>2nd Receiving SMS #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_number_2">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>3rd Receiving SMS #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_number_3">
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;'>SMS Message:</p>
                </td>
                <td class="right_col">
                    <textarea name="sms_message"></textarea>
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

function check_sms_form() {
    show_errors();

    $print_again = false;
    $label = "";
    $message = "";

    /* ============================================ */
    /* ====== START data integrity checks ========= */
    /* ============================================ */

    $to_sms_number = strip_tags($_POST['to_sms_number']);
    $sms_message = strip_tags($_POST['sms_message']);

    if ($sms_message == "") {
        $print_again = true;
        $message = "No SMS message body has been provided";
    }
    if ($to_sms_number == "") {
        $print_again = true;
        $message = "No receiving SMS Number has been provided";
    }

    /* ========================================== */
    /* ====== END data integrity checks ========= */
    /* ========================================== */
    if ($print_again) {
        show_form($message);
    } else {
        send_sms($to_sms_number, $sms_message);
        $message = "SMS message has been sent Successfully";
        show_form($message);
    }

}

function check_fax_form() {
    show_errors();

    $print_again = false;
    $label = "";
    $message = "";

    /* ============================================ */
    /* ====== START data integrity checks ========= */
    /* ============================================ */

    $to_fax_number = strip_tags($_POST['to_fax_number']);
    $cover_note = strip_tags($_POST['cover_note']);
    $target_file = basename($_FILES["file_to_fax"]["name"]);

    if ($target_file == "") {
        $print_again = true;
        $label = "";
        $message = "No file selected to be uploaded";
    }
    if ($cover_note == "") {
        $print_again = true;
        $label = "cover_note";
        $message = "No cover note has been provided";
    }
    if ($to_fax_number == "") {
        $print_again = true;
        $label = "to_fax_number";
        $message = "No receiving Fax Number has been provided";
    }

    /* ========================================== */
    /* ====== END data integrity checks ========= */
    /* ========================================== */

    $file_with_path = upload_file();

    $fax_sent_id = send_fax($to_fax_number, $file_with_path, $cover_note);
    if ($fax_sent_id > 0) {
        $print_again = true;
        $label = "";
        $message = "Fax sent successfully (Sent id): " . $fax_sent_id;
        // clean out the file
        unlink($file_with_path);
    }
    show_form($message, $label, $print_again);
}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['send_sms'])) {
    check_sms_form();
} elseif (isset($_POST['send_fax'])) {
    check_fax_form();
} else {
    $message = "Please provide the needed information. <br/><br/>";
    show_form($message);
}

ob_end_flush();
page_footer();
