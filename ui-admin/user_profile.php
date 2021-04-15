<?php
global $user_id;
?>
<h3><?php _e('Q&A Einstellungen', QA_TEXTDOMAIN);?></h3>
<table class="form-table">
    <tbody>
        <tr>
            <th>
                <label for="qa-notification"><?php _e('Erhalte E-Mail-Benachrichtigungen über neue Fragen', QA_TEXTDOMAIN); ?></label>
            </th>
            <td><input type="checkbox" id="qa-notification" name="qa_notification" <?php echo (get_user_meta($user_id, "qa_notification", true) == 1)?'checked="checked"':''; ?> value="1" /></td>
        </tr>
    </tbody>
</table>