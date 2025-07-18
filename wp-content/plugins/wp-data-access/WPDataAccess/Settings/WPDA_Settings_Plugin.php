<?php

namespace WPDataAccess\Settings {

	use WPDataAccess\Connection\WPDADB;
	use WPDataAccess\Utilities\WPDA_Message_Box;
	use WPDataAccess\WPDA;

	class WPDA_Settings_Plugin extends WPDA_Settings {

		protected function add_content() {
			if ( isset( $_REQUEST['action'] ) ) {
				$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // input var okay.

				// Security check.
				if ( 'delete_remote_database' === $action ) {
					$wp_nonce = isset( $_REQUEST['_wpnoncedelrdb'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnoncedelrdb'] ) ) : ''; // input var okay.
					if ( ! wp_verify_nonce( $wp_nonce, 'wpda-delete-remote-database-' . WPDA::get_current_user_login() ) ) {
						wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) );
					}

					if ( isset( $_REQUEST['remote_database_name'] ) ) {
						$remote_database_name = sanitize_text_field( wp_unslash( $_REQUEST['remote_database_name'] ) ); // input var okay.
						WPDADB::del_remote_database( $remote_database_name );
					}
				} elseif ( 'update_remote_database' === $action ) {
					$wp_nonce = isset( $_REQUEST['_wpnonceupdrdb'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonceupdrdb'] ) ) : ''; // input var okay.
					if ( ! wp_verify_nonce( $wp_nonce, 'wpda-update-remote-database-' . WPDA::get_current_user_login() ) ) {
						wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) );
					}

					$database_old = isset( $_REQUEST['remote_database_old'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_database_old'] ) ) : ''; // input var okay.
					$database     = isset( $_REQUEST['remote_database'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_database'] ) ) : ''; // input var okay.
					$host         = isset( $_REQUEST['remote_host'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_host'] ) ) : ''; // input var okay.
					$username     = isset( $_REQUEST['remote_user'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_user'] ) ) : ''; // input var okay.
					$password     = isset( $_REQUEST['remote_passwd'] ) ? // Cannot use sanitize_text_field on password field!
						wp_unslash( $_REQUEST['remote_passwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$port         = isset( $_REQUEST['remote_port'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_port'] ) ) : ''; // input var okay.
					$schema       = isset( $_REQUEST['remote_schema'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_schema'] ) ) : ''; // input var okay.
					$enabled      = isset( $_REQUEST['remote_enabled'] ) ? true : false;

					// Add ssl
					$remote_ssl                = isset( $_REQUEST['remote_ssl'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_ssl'] ) ) : 'off'; // input var okay.
					$remote_client_key         = isset( $_REQUEST['remote_client_key'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_client_key'] ) ) : ''; // input var okay.
					$remote_client_certificate = isset( $_REQUEST['remote_client_certificate'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_client_certificate'] ) ) : ''; // input var okay.
					$remote_ca_certificate     = isset( $_REQUEST['remote_ca_certificate'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_ca_certificate'] ) ) : ''; // input var okay.
					$remote_certificate_path   = isset( $_REQUEST['remote_certificate_path'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_certificate_path'] ) ) : ''; // input var okay.
					$remote_specified_cipher   = isset( $_REQUEST['remote_specified_cipher'] ) ?
						sanitize_text_field( wp_unslash( $_REQUEST['remote_specified_cipher'] ) ) : ''; // input var okay.

					if ( '' === $database_old || '' === $database || '' === $host || '' === $username || '' === $password || '' === $port || '' === $schema || '' === $enabled ) {
						$msg = new WPDA_Message_Box(
							array(
								'message_text'           => sprintf( __( 'Cannot save remote database connection [missing arguments]', 'wp-data-access' ) ),
								'message_type'           => 'error',
								'message_is_dismissible' => false,
							)
						);
						$msg->box();
					} else {
						WPDADB::upd_remote_database(
							$database,
							$host,
							$username,
							$password,
							$port,
							$schema,
							! $enabled,
							$database_old,
							$remote_ssl,
							$remote_client_key,
							$remote_client_certificate,
							$remote_ca_certificate,
							$remote_certificate_path,
							$remote_specified_cipher
						);
					}
				} else {
					$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : ''; // input var okay.
					if ( ! wp_verify_nonce( $wp_nonce, 'wpda-plugin-settings-' . WPDA::get_current_user_login() ) ) {
						wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) );
					}

					if ( 'save' === $action ) {
						WPDA::set_option(
							WPDA::OPTION_PLUGIN_HIDE_NOTICES,
							isset( $_REQUEST['hide_foreign_notices'] ) ? 'on' : 'off' // input var okay.
						);

						WPDA::set_option(
							WPDA::OPTION_PLUGIN_HIDE_ADMIN_MENU,
							isset( $_REQUEST['hide_admin_menu'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['hide_admin_menu'] ) ) : 'off' // input var okay.
						);

						if ( isset( $_REQUEST['panel_cookies'] ) ) {
							WPDA::set_option(
								WPDA::OPTION_PLUGIN_PANEL_COOKIES,
								sanitize_text_field( wp_unslash( $_REQUEST['panel_cookies'] ) ) // input var okay.
							);
						}

						if ( ! isset( $_REQUEST['secret_key'] ) || ! isset( $_REQUEST['secret_iv'] ) ) {
							// Leave both values untouched
						} else {
							$secret_key_old = WPDA::get_option( WPDA::OPTION_PLUGIN_SECRET_KEY );
							$secret_iv_old  = WPDA::get_option( WPDA::OPTION_PLUGIN_SECRET_IV );

							$secret_key_new = sanitize_text_field( wp_unslash( $_REQUEST['secret_key'] ) ); // input var okay.
							$secret_iv_new  = sanitize_text_field( wp_unslash( $_REQUEST['secret_iv'] ) ); // input var okay.

							if ( $secret_key_old !== $secret_key_new || $secret_iv_old !== $secret_iv_new ) {
								// Update existing remote databases
								WPDADB::load_remote_databases(); // load remote databases with old secret key and iv
								WPDA::set_option( WPDA::OPTION_PLUGIN_SECRET_KEY, $secret_key_new ); // update secret key
								WPDA::set_option( WPDA::OPTION_PLUGIN_SECRET_IV, $secret_iv_new ); // update secret iv
								WPDADB::save_remote_databases(); // save remote databases with new secret key and iv
							}
						}

						if ( isset( $_REQUEST['remote_database_name'] ) && isset( $_REQUEST['remote_database_enabled'] ) ) {
							if ( is_array( $_REQUEST['remote_database_name'] ) &&
								is_array( $_REQUEST['remote_database_enabled'] ) &&
								count( $_REQUEST['remote_database_name'] ) === count( $_REQUEST['remote_database_enabled'] )//phpcs:ignore - 8.1 proof
							) {
								$i = 0;
								while ( $i < count( $_REQUEST['remote_database_name'] ) ) {//phpcs:ignore - 8.1 proof
									$rdb_name = sanitize_text_field( wp_unslash( $_REQUEST['remote_database_name'][ $i ] ) );
									$dbs      = WPDADB::get_remote_database( $rdb_name, true );
									if ( ! $dbs ) {
										$msg = new WPDA_Message_Box(
											array(
												'message_text'           => __( 'Remote database connection not found', 'wp-data-access' ),
												'message_type'           => 'error',
												'message_is_dismissible' => false,
											)
										);
										$msg->box();
									} else {
										WPDADB::upd_remote_database(
											$rdb_name,
											$dbs['host'],
											$dbs['username'],
											$dbs['password'],
											$dbs['port'],
											$dbs['database'],
											$_REQUEST['remote_database_enabled'][ $i ] === 'FALSE',
											false,
											$dbs['ssl'],
											$dbs['ssl_key'],
											$dbs['ssl_cert'],
											$dbs['ssl_ca'],
											$dbs['ssl_path'],
											$dbs['ssl_cipher']
										);
									}
									$i++;
								}
								WPDADB::save_remote_databases(); // save changes
							}
						}

						WPDA::set_option(
							WPDA::OPTION_PLUGIN_DEBUG,
							isset( $_REQUEST['debug'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['debug'] ) ) : 'off' // input var okay.
						);
					} elseif ( 'setdefaults' === $action ) {
						// Set all back-end settings back to default.
						WPDA::get_option( WPDA::OPTION_PLUGIN_HIDE_NOTICES );

						WPDA::set_option( WPDA::OPTION_PLUGIN_HIDE_ADMIN_MENU );

						WPDA::set_option( WPDA::OPTION_PLUGIN_PANEL_COOKIES );

						// DO NOT RESET SECRET KEY AND IV

						// DO NOT RESET RDBs

						WPDA::set_option( WPDA::OPTION_PLUGIN_DEBUG );
					}
				}

				$msg = new WPDA_Message_Box(
					array(
						'message_text' => __( 'Settings saved', 'wp-data-access' ),
					)
				);
				$msg->box();
			} elseif ( isset( $_REQUEST['msg'] ) && 'ok' === $_REQUEST['msg'] ) {
				$msg = new WPDA_Message_Box(
					array(
						'message_text' => __( 'Settings saved', 'wp-data-access' ),
					)
				);
				$msg->box();
			}

			// Get options.
			$hide_foreign_notices = WPDA::get_option( WPDA::OPTION_PLUGIN_HIDE_NOTICES );

			$hide_admin_menu = WPDA::get_option( WPDA::OPTION_PLUGIN_HIDE_ADMIN_MENU );

			$panel_cookies = WPDA::get_option( WPDA::OPTION_PLUGIN_PANEL_COOKIES );

			$secret_key = WPDA::get_option( WPDA::OPTION_PLUGIN_SECRET_KEY );
			$secret_iv  = WPDA::get_option( WPDA::OPTION_PLUGIN_SECRET_IV );

			$debug = WPDA::get_option( WPDA::OPTION_PLUGIN_DEBUG );

			$remote_databases = WPDADB::get_remote_databases( true );
			?>

			<style>
				.item_label {
					width: 14.9em;
					display: inline-block;
					padding-left: 0.3em;
				}

				.item_label_text {
					width: 7em;
					display: inline-block;
				}

				.item_label_format {
					width: 5em;
					padding: 0.6em;
					border-radius: 4px;
				}

				.item_label_align {
					float: right;
				}

				#wpda_update_database_popup {
					display: none;
					padding: 10px;
					position: absolute;
					top: 30px;
					left: 10px;
					color: black;
					overflow-y: auto;
					background-color: white;
					border: 1px solid #ccc;
					width: max-content;
				}

				#wpda_update_database_popup_header {
					background-color: #ccc;
					height: 30px;
					padding: 10px;
					margin-bottom: 10px;
				}
			</style>

			<script>
				function delete_remote_database(id) {
					remote_database_name = jQuery('#remote_database_name' + id).val();
					if (confirm("<?php echo __( 'Delete remote database connection from plugin repository?', 'wp-data-access' ); ?>")) {
						jQuery('#delete_remote_database_name').val(remote_database_name);
						jQuery('#wpda_delete_database').submit();
					}
				}

				function update_rdb_setting(id) {
					if (jQuery('#remote_database' + id).is(':checked')) {
						jQuery('#remote_database_enabled' + id).val('TRUE');
					} else {
						jQuery('#remote_database_enabled' + id).val('FALSE');
					}
				}

				function edit_rdb_setting(id) {
					jQuery('#wpda_update_database_popup').show();
					jQuery('#remote_database_old').val(id);
					jQuery('#remote_database').val(id);
					jQuery('#remote_host').val(remote_databases[id].host);
					jQuery('#remote_user').val(remote_databases[id].username);
					jQuery('#remote_passwd').val(remote_databases[id].password);
					jQuery('#remote_port').val(remote_databases[id].port);
					jQuery('#remote_schema').val(remote_databases[id].database);
					jQuery('#remote_enabled').prop('checked', !remote_databases[id].disabled);
					jQuery('#remote_ssl').prop('checked', remote_databases[id].ssl==="on");//.val(remote_databases[id].ssl);
					jQuery('#remote_client_key').val(remote_databases[id].ssl_key);
					jQuery('#remote_client_certificate').val(remote_databases[id].ssl_cert);
					jQuery('#remote_ca_certificate').val(remote_databases[id].ssl_ca);
					jQuery('#remote_certificate_path').val(remote_databases[id].ssl_path);
					jQuery('#remote_specified_cipher').val(remote_databases[id].ssl_cipher);
					if (remote_databases[id].ssl==="on") {
						jQuery('#remote_database_block_ssl').show();
					} else {
						jQuery('#remote_database_block_ssl').hide();
					}
				}

				function test_remote_clear(mode = '') {
					jQuery('#' + mode + 'remote_database_block_test_content').html('');
					jQuery('#' + mode + 'remote_database_block_test').hide();
					jQuery('#' + mode + 'remote_clear_button').hide();
				}

				function test_remote_connection(mode = '') {
					host = jQuery('#remote_host').val();
					user = jQuery('#remote_user').val();
					pass = jQuery('#remote_passwd').val();
					port = jQuery('#remote_port').val();
					dbs = jQuery('#remote_schema').val();

					url = '//' + window.location.host + window.location.pathname.replace('options-general','admin') +
						'?action=wpda_check_remote_database_connection';

					jQuery('#remote_test_button').val('Testing...');

					jQuery.ajax({
						method: 'POST',
						url: url,
						data: {
							host: host,
							user: user,
							passwd: pass,
							port: port,
							schema: dbs
						}
					}).done(
						function (msg) {
							jQuery('#remote_database_block_test_content').html(msg);
							jQuery('#remote_database_block_test').show();
						}
					).fail(
						function () {
							jQuery('#remote_database_block_test_content').html('Preparing connection...<br/>Establishing connection...<br/><br/><strong>Remote database connection invalid</strong>');
							jQuery('#remote_database_block_test').show();
						}
					).always(
						function () {
							jQuery('#remote_test_button').val('Test');
							jQuery('#remote_clear_button').show();
						}
					);
				}

				jQuery(function () {
					jQuery('#remote_database').keydown(function(e) {
						var field = this;
						setTimeout(function () {
							if (field.value.indexOf('rdb:') !== 0) {
								jQuery(field).val('rdb:');
							}
						}, 1);
					});
				});

				var remote_databases = new Object();
				<?php
				foreach ( $remote_databases as $key => $value ) {
					echo "remote_databases['$key'] = " . json_encode( $value ) . ';'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</script>

			<div id="wpda_update_database_popup">
				<div id="wpda_update_database_popup_header">
					<span style="display:inline-block;margin-top:5px;">
						<strong>
							<?php echo __( 'Edit Remote Database Connection', 'wp-data-access' ); ?>
						</strong>
					</span>
					<span class="button" style="float:right;height:10px;"
						  onclick="jQuery('#wpda_update_database_popup').hide()">x</span><br/>
				</div>

				<form id="wpda_update_database" method="post"
					  action="?page=<?php echo esc_attr( $this->page ); ?>&tab=plugin">

					<div>
						<label for="remote_database" style="vertical-align:baseline;"
							   class="database_item_label">Database name:</label>
						<input type="text" name="remote_database" id="remote_database" value="rdb:">
						<span>(local WordPress dashboard)</span>
						<div style="height:10px;"></div>
						<label for="remote_host" style="vertical-align:baseline;" class="database_item_label">MySQL host:</label>
						<input type="text" name="remote_host" id="remote_host">
						<span>(ip address or hostname)</span>
						<br/>
						<label for="remote_user" style="vertical-align:baseline;" class="database_item_label">MySQL username:</label>
						<input type="text" name="remote_user" id="remote_user">
						<br/>
						<label for="remote_passwd" style="vertical-align:baseline;" class="database_item_label">MySQL password:</label>
						<input type="password" name="remote_passwd" id="remote_passwd" autocomplete="new-password">
						<i class="fas fa-eye" onclick="wpda_toggle_password('remote_passwd', event)" style="cursor:pointer"></i>
						<br/>
						<label for="remote_port" style="vertical-align:baseline;" class="database_item_label">MySQL port:</label>
						<input type="text" name="remote_port" id="remote_port" value="3306">
						<br/>
						<label for="remote_schema" style="vertical-align:baseline;" class="database_item_label">MySQL schema:</label>
						<input type="text" name="remote_schema" id="remote_schema">
						<br/>
						<label for="remote_schema" style="vertical-align:baseline;line-height:30px;" class="database_item_label">
							Enabled:
						</label>
						<input type="checkbox" name="remote_enabled" id="remote_enabled">

						<br/>
						<label style="line-height:30px;" for="remote_ssl" style="vertical-align:baseline;" class="database_item_label">SSL:</label>
						<input type="checkbox" name="remote_ssl" id="remote_ssl" unchecked onclick="jQuery('#remote_database_block_ssl').toggle()">
						<div id="remote_database_block_ssl">
							<label for="remote_client_key" style="vertical-align:baseline;" class="database_item_label">Client key:</label>
							<input type="text" name="remote_client_key" id="remote_client_key">
							<br/>
							<label for="remote_client_certificate" style="vertical-align:baseline;" class="database_item_label">Client certificate:</label>
							<input type="text" name="remote_client_certificate" id="remote_client_certificate">
							<br/>
							<label for="remote_ca_certificate" style="vertical-align:baseline;" class="database_item_label">CA certificate:</label>
							<input type="text" name="remote_ca_certificate" id="remote_ca_certificate">
							<br/>
							<label for="remote_certificate_path" style="vertical-align:baseline;" class="database_item_label">Certificate path:</label>
							<input type="text" name="remote_certificate_path" id="remote_certificate_path">
							<br/>
							<label for="remote_specified_cipher" style="vertical-align:baseline;" class="database_item_label">Specified Cipher:</label>
							<input type="text" name="remote_specified_cipher" id="remote_specified_cipher">
						</div>

						<div style="height:10px;"></div>
						<label class="database_item_label"></label>
						<input type="button" value="Test" onclick="test_remote_connection(); return false;"
							   id="remote_test_button" class="button">
						<input type="button" value="Clear" onclick="test_remote_clear(); return false;"
							   id="remote_clear_button" class="button" style="display:none;">
						<div style="height:10px;"></div>
					</div>
					<div id="remote_database_block_test" style="display:none;">
						<div id="remote_database_block_test_content"
							 class="remote_database_block_test_content"></div>
						<div style="height:10px;"></div>
					</div>
					<input type="hidden" name="remote_database_old" id="remote_database_old" value=""">
					<input type="hidden" name="action" value="update_remote_database"/>
					<input type="submit" class="button button-secondary" value="<?php echo __( 'Save', 'wp-data-access' ); ?>">
					<a href="javascript:void(0)"
					   onclick="jQuery('#wpda_update_database_popup').hide()"
					   class="button button-secondary">
						<?php echo __( 'Cancel', 'wp-data-access' ); ?>
					</a>
					<?php
					$rdb_wp_nonce_action = 'wpda-update-remote-database-' . WPDA::get_current_user_login();
					$rdb_wp_nonce        = wp_create_nonce( $rdb_wp_nonce_action );
					?>
					<input type="hidden" name="_wpnonceupdrdb" value="<?php echo esc_attr( $rdb_wp_nonce ); ?>"/>
				</form>
			</div>
			<form id="wpda_delete_database" method="post" style="display:none;"
				  action="?page=<?php echo esc_attr( $this->page ); ?>&tab=plugin">
				<input type="hidden" name="remote_database_name" id="delete_remote_database_name" value=""/>
				<input type="hidden" name="action" value="delete_remote_database"/>
				<?php
				$rdb_wp_nonce_action = 'wpda-delete-remote-database-' . WPDA::get_current_user_login();
				$rdb_wp_nonce        = wp_create_nonce( $rdb_wp_nonce_action );
				?>
				<input type="hidden" name="_wpnoncedelrdb" value="<?php echo esc_attr( $rdb_wp_nonce ); ?>"/>
			</form>
			<form id="wpda_settings_plugin" method="post"
				  action="?page=<?php echo esc_attr( $this->page ); ?>&tab=plugin">
				<table class="wpda-table-settings" id="wpda_table_plugin">
					<tr>
						<th><?php echo __( 'Plugin menu', 'wp-data-access' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="hide_admin_menu" <?php echo 'on' === $hide_admin_menu ? 'checked="checked"' : ''; ?>/>
								<?php echo __( 'Hide plugin menu in admin panel', 'wp-data-access' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Notices', 'wp-data-access' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="hide_foreign_notices" <?php echo $hide_foreign_notices === 'on' ? 'checked' : ''; ?> />
								<?php echo __( 'Hide notices of other themes and plugins on WP Data Access admin pages', 'wp-data-access' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Panel cookies', 'wp-data-access' ); ?></th>
						<td>
							<label>
								<input
									type="radio"
									name="panel_cookies"
									value="clear"
									<?php echo 'clear' === $panel_cookies ? 'checked' : ''; ?>
								><?php echo __( 'Clear when switching panels', 'wp-data-access' ); ?>
							</label>
							<br/>
							<label>
								<input
									type="radio"
									name="panel_cookies"
									value="keep"
									<?php echo 'keep' === $panel_cookies ? 'checked' : ''; ?>
								><?php echo __( 'Keep when switching panels', 'wp-data-access' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Secret key and IV' ); ?></th>
						<td>
							<input type="text" name="secret_key" value="<?php echo esc_attr( $secret_key ); ?>"/>
							<br/>
							<input type="text" name="secret_iv" value="<?php echo esc_attr( $secret_iv ); ?>"/>
							<br/><br/>
							<span class="dashicons dashicons-info"></span><?php echo __( 'Existing remote database connection settings will be converted', 'wp-data-access' ); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Remote database connections' ); ?></th>
						<td>
							<?php
							$i = 0;
							foreach ( $remote_databases as $remote_database => $remote_database_settings ) {
								$checked = isset( $remote_database_settings['disabled'] ) && $remote_database_settings['disabled'] ? '' : 'checked';
								$enabled = isset( $remote_database_settings['disabled'] ) && $remote_database_settings['disabled'] ? 'FALSE' : 'TRUE';
								?>
								<a href="javascript:void(0)"
								   onclick="delete_remote_database('<?php echo esc_attr( $i ); ?>')"
								   style="text-decoration:none;"
								   class="wpda_tooltip"
								   title="<?php echo __( 'Delete remote database connection from plugin repository', 'wp-data-access' ); ?>">
									<span class="dashicons dashicons-trash" style="font-size:18px;"></span>
								</a>
								<label class="wpda_tooltip" title="<?php echo __( 'Disable remote database connection', 'wp-data-access' ); ?>">
									<input type="checkbox" name="remote_database[]" id="remote_database<?php echo esc_attr( $i ); ?>" onclick="update_rdb_setting('<?php echo esc_attr( $i ); ?>')" <?php echo esc_attr( $checked ); ?>>
									<input type="hidden" name="remote_database_name[]" id="remote_database_name<?php echo esc_attr( $i ); ?>" value="<?php echo esc_attr( $remote_database ); ?>">
									<input type="hidden" name="remote_database_enabled[]" id="remote_database_enabled<?php echo esc_attr( $i ); ?>" value="<?php echo esc_attr( $enabled ); ?>">
									<?php echo esc_attr( $remote_database ); ?>
								</label>
								<a href="javascript:void(0)"
								   onclick="edit_rdb_setting('<?php echo esc_attr( $remote_database ); ?>')"
								   style="text-decoration:none;"
								   class="wpda_tooltip"
								   title="<?php echo __( 'Edit remote database connection', 'wp-data-access' ); ?>">
									<span class="dashicons dashicons-edit" style="font-size:18px;"></span>
								</a><br/>
								<?php
								$i++;
							}
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo __( 'Debug mode', 'wp-data-access' ); ?></th>
						<td>
							<label>
								<input
									type="checkbox"
									name="debug" <?php echo 'on' === $debug ? 'checked' : ''; ?>
								><?php echo __( 'Enable debug mode', 'wp-data-access' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<div class="wpda-table-settings-button">
					<input type="hidden" name="action" value="save"/>
					<button type="submit" class="button button-primary">
						<i class="fas fa-check wpda_icon_on_button"></i>
						<?php echo __( 'Save Plugin Settings', 'wp-data-access' ); ?>
					</button>
					<a href="javascript:void(0)"
					   onclick="if (confirm('<?php echo __( 'Reset to defaults?', 'wp-data-access' ); ?>')) {
						   jQuery('input[name=&quot;action&quot;]').val('setdefaults');
						   jQuery('#wpda_settings_plugin').trigger('submit')
						   }"
					   class="button">
						<i class="fas fa-times-circle wpda_icon_on_button"></i>
						<?php echo __( 'Reset Plugin Settings To Defaults', 'wp-data-access' ); ?>
					</a>
				</div>
				<?php wp_nonce_field( 'wpda-plugin-settings-' . WPDA::get_current_user_login(), '_wpnonce', false ); ?>
			</form>
			<?php
		}

	}

}
