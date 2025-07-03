<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div>
    <label for="signature"><?php _e( 'Forum Signature', 'gd-bbpress-tools' ); ?></label>
    <textarea name="signature" id="signature" rows="5" cols="30"><?php echo esc_textarea( $_signature ); ?></textarea><br/>
    <span class="description">
        <?php echo sprintf( __( 'Signature length is limited to %s characters.', 'gd-bbpress-tools' ), $this->max_length ); ?><br/>
        <?php do_action( 'bbp_user_edit_signature_info' ); ?>
    </span>
</div>
