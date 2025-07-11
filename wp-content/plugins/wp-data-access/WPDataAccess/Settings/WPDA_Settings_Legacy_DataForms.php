<?php

namespace WPDataAccess\Settings {

use WPDataAccess\Utilities\WPDA_Message_Box;
use WPDataAccess\WPDA;

class WPDA_Settings_Legacy_DataForms extends WPDA_Settings_Legacy_Page {

    public function show() {

        if ( isset( $_REQUEST['action'] ) ) {
            $action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // input var okay.

            // Security check.
            $wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : ''; // input var okay.
            if ( ! wp_verify_nonce( $wp_nonce, 'wpda-forms-settings-' . WPDA::get_current_user_login() ) ) {
                wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) );
            }

            if ( 'save' === $action ) {
                WPDA::set_option(
                    WPDA::OPTION_PLUGIN_WPDADATAFORMS_ALLOW_ANONYMOUS_ACCESS,
                    isset( $_REQUEST['allow_anonymous_access'] ) ? 'on' : 'off'
                );
            } elseif ( 'setdefaults' === $action ) {
                // Set all data table settings back to default.
                WPDA::set_option( WPDA::OPTION_PLUGIN_WPDADATAFORMS_ALLOW_ANONYMOUS_ACCESS );
            }

            $msg = new WPDA_Message_Box(
                array(
                    'message_text' => __( 'Settings saved', 'wp-data-access' ),
                )
            );
            $msg->box();
        }

        $allow_anonymous_access = WPDA::get_option( WPDA::OPTION_PLUGIN_WPDADATAFORMS_ALLOW_ANONYMOUS_ACCESS );
        ?>

        <form id="wpda_settings_forms" method="post"
              action="?page=<?php echo esc_attr( $this->page ); ?>&tab=legacy&vtab=dataforms">
            <table class="wpda-table-settings">
                <tr style="border-top: 1px solid #ccc">
                    <th><?php echo __( 'Allow anonymous access', 'wp-data-access' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="allow_anonymous_access" <?php echo 'on' === $allow_anonymous_access ? 'checked' : ''; ?>/>Enabled
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __( 'Default jQuery UI theme', 'wp-data-access' ); ?></th>
                    <td>
                        <a href="<?php echo admin_url( 'options-general.php' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>?page=wpdataaccess&tab=frontend">
                            Change default jQuery UI theme
                        </a> (used to style <strong>Data Forms</strong>)
                    </td>
                </tr>
                <tr>
                    <th><span class="dashicons dashicons-info" style="float:right;font-size:300%;"></span></th>
                    <td>
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo __( 'Data Forms are styled according to the default jQuery UI theme', 'wp-data-access' ); ?>
                        <br/>
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo __( 'Individual styling per shortcode will be added later', 'wp-data-access' ); ?>
                    </td>
                </tr>
            </table>
            <div class="wpda-table-settings-button">
                <input type="hidden" name="action" value="save"/>
                <button type="submit" class="button button-primary">
                    <i class="fas fa-check wpda_icon_on_button"></i>
                    <?php echo __( 'Save Data Forms  Settings', 'wp-data-access' ); ?>
                </button>
                <a href="javascript:void(0)"
                   onclick="if (confirm('<?php echo __( 'Reset to defaults?', 'wp-data-access' ); ?>')) {
                       jQuery('input[name=&quot;action&quot;]').val('setdefaults');
                       jQuery('#wpda_settings_forms').trigger('submit')
                       }"
                   class="button">
                    <i class="fas fa-times-circle wpda_icon_on_button"></i>
                    <?php echo __( 'Reset Data Forms Settings To Defaults', 'wp-data-access' ); ?>
                </a>
            </div>
            <?php wp_nonce_field( 'wpda-forms-settings-' . WPDA::get_current_user_login(), '_wpnonce', false ); ?>
        </form>

        <?php
    }

    }

}