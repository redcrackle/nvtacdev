<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h3><?php IS_PROFILE_PAGE ? _e( 'Your Forum Signature', 'gd-bbpress-tools' ) : _e( 'User Forum Signature', 'gd-bbpress-tools' ); ?></h3>
<table class="form-table">
    <tr>
        <th><label for="signature"><?php _e( 'Signature', 'gd-bbpress-tools' ); ?></label></th>
        <td>
            <textarea name="signature" id="signature" rows="5" cols="30"><?php echo esc_textarea( $_signature ); ?></textarea>
        </td>
    </tr>
</table>
