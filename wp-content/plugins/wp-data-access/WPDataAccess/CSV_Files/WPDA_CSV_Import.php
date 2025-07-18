<?php // phpcs:ignore Standard.Category.SniffName.ErrorCode

namespace WPDataAccess\CSV_Files {

	use WPDataAccess\Connection\WPDADB;
	use WPDataAccess\Dashboard\WPDA_Dashboard;
	use WPDataAccess\Data_Dictionary\WPDA_List_Columns_Cache;
	use WPDataAccess\Plugin_Table_Models\WPDA_CSV_Uploads_Model;
	use WPDataAccess\Utilities\WPDA_Message_Box;
	use WPDataAccess\List_Table\WPDA_List_View;
	use WPDataAccess\WPDA;

	/**
	 * CSV import class
	 */
	class WPDA_CSV_Import {

		/**
		 * Data schema name
		 *
		 * @var null
		 */
		protected $schema_name = null;

		/**
		 * Page action
		 *
		 * @var null
		 */
		protected $action = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			global $wpdb;
			$this->schema_name = $wpdb->dbname;

			$this->action =
				isset( $_REQUEST['action'] ) ? // phpcs:ignore WordPress.Security.NonceVerification
					sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification
		}

		/**
		 * Show HTML page
		 *
		 * @return void
		 */
		public function show() {
			?>
			<div class="wrap">
				<h1 class="wp-heading-inline">
					<?php
					if (
						isset( $_REQUEST['action'] ) && // phpcs:ignore WordPress.Security.NonceVerification
						! (
							'-1' === $_REQUEST['action'] ||  // phpcs:ignore WordPress.Security.NonceVerification
							'bulk-delete' === $_REQUEST['action'] ||  // phpcs:ignore WordPress.Security.NonceVerification
							'delete' === $_REQUEST['action'] // phpcs:ignore WordPress.Security.NonceVerification
						)
					) {
						?>
						<a
							href="<?php echo esc_attr( admin_url( 'admin.php' ) ); ?>?page=<?php echo esc_attr( \WP_Data_Access_Admin::PAGE_MAIN ); ?>&page_action=wpda_import_csv"
							style="display: inline-block; vertical-align: unset; margin-top: 6px;"
							class="dashicons dashicons-arrow-left-alt2 wpda_tooltip"
							title="Back to CSV file list"
						></a>
						<?php
					}
					?>
					<?php echo __( 'Import CSV ' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</h1>
				<?php $this->show_body(); ?>
			</div>
			<?php
		}

		/**
		 * Show page body
		 *
		 * @return void
		 */
		protected function show_body() {
			if ( 'upload' === $this->action ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( isset( $_REQUEST['action2'] ) && 'save' === $_REQUEST['action2'] ) { // phpcs:ignore WordPress.Security.NonceVerification
					$this->upload_file();
				} else {
					$this->show_body_upload();
				}
			} elseif ( 'mapping' === $this->action ) {
				$this->show_body_mapping();
			} elseif ( 'import_start' === $this->action ) {
				$this->show_body_import_start();
			} elseif ( 'import' === $this->action ) {
				$this->show_body_import();
			} elseif ( 'reload' === $this->action ) {
				$this->show_body_reload();
			} else {
				$this->show_body_main();
			}
		}

		/**
		 * Show mapping page
		 *
		 * @return void
		 */
		protected function show_body_mapping() {
			$csv_mapping = new WPDA_CSV_Mapping();
			$csv_mapping->show();
		}

		/**
		 * Show main page body
		 *
		 * @return void
		 */
		protected function show_body_main() {
			$csv_list_view = new WPDA_List_View(
				array(
					'page_hook_suffix' => 'CSV',
					'table_name'       => WPDA_CSV_Uploads_Model::get_base_table_name(),
					'list_table_class' => 'WPDataAccess\\CSV_Files\\WPDA_CSV_List_Table',
					'edit_form_class'  => 'WPDataAccess\\Simple_Form\\WPDA_Simple_Form',
					'subtitle'         => '',
				)
			);
			$csv_list_view->show();
		}

		/**
		 * Show upload body
		 *
		 * @return void
		 */
		protected function show_body_upload() {
			$csv_id   =
				isset( $_REQUEST['csv_id'] ) ? // phpcs:ignore WordPress.Security.NonceVerification
					sanitize_text_field( wp_unslash( $_REQUEST['csv_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$csv_name =
				isset( $_REQUEST['csv_name'] ) ? // phpcs:ignore WordPress.Security.NonceVerification
					sanitize_text_field( wp_unslash( $_REQUEST['csv_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			?>
			<br/>
			<fieldset class="wpda_fieldset">
				<legend>
					<?php echo __( 'Select a file and click upload', 'wp-data-access' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</legend>
				<form id="form_import_table"
						method="post"
						action="?page=wpda&page_action=wpda_import_csv"
						enctype="multipart/form-data">
					<label for="csv_name">
						Import name
						<input id="csv_name"
								name="csv_name"
								type="text"
								value="<?php echo esc_attr( $csv_name ); ?>"
								<?php
								if ( '' !== $csv_name ) {
									echo 'disabled';
								}
								?>
						/>
						<?php if ( '' !== $csv_name ) { ?>
							<input id="csv_name"
									name="csv_name"
									type="hidden"
									value="<?php echo esc_attr( $csv_name ); ?>"
							/>
						<?php } ?>
					</label>
					<br/><br/>
					<input type="file" name="filename" id="filename" accept=".csv">
					<button type="submit"
							class="button button-primary"
							onclick="if (jQuery('#csv_name').val()===''||jQuery('#filename').val()==='') { alert('Please enter an import name and select a file'); return false; }"
					>
						<i class="fas fa-check wpda_icon_on_button"></i>
						<?php echo __( 'Upload', 'wp-data-access' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
					<button type="button"
							onclick="window.location.href='?page=wpda&page_action=wpda_import_csv'"
							class="button button-secondary"
					>
						<i class="fas fa-times-circle wpda_icon_on_button"></i>
						<?php echo __( 'Cancel', 'wp-data-access' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
					<input type="hidden"
							name="action"
							value="upload"
					/>
					<input type="hidden"
							name="action2"
							value="save"
					/>
					<input type="hidden"
							name="csv_id"
							value="<?php echo esc_attr( $csv_id ); ?>"
					/>
					<?php wp_nonce_field( "wpda-import-csv-{$this->schema_name}", '_wpnonce', false ); ?>
				</form>
			</fieldset>
			<?php
		}

		/**
		 * Start import
		 *
		 * @return void
		 */
		protected function show_body_import_start() {
			$csv_id   = isset( $_REQUEST['csv_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['csv_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$csv_name = isset( $_REQUEST['csv_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['csv_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$page     = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			?>
			<style type="text/css">
				.wpda-label {
					font-weight: bold;
					display: inline-block;
					width: 110px;
				}
				.wpda-import-form {
					margin-left: 115px;
					width: 110px;
				}
			</style>
			<br/>
			<form action="?page=<?php echo esc_attr( $page ); ?>&page_action=wpda_import_csv" method="post">
			<fieldset class="wpda_fieldset">
				<legend>
					Start Import
				</legend>
				<p>
					<label class="wpda-label">Import Name</label>
					<input type="text" value="<?php echo esc_attr( $csv_name ); ?>" readonly />
				</p>
				<p>
					<label class="wpda-label"></label>
					<label style="padding-left:5px">
						<input type="checkbox" name="truncate_table" />
						<strong>Truncate table before import?</strong> (This will remove all rows from your table and cannot be undone!)
					</label>
				</p>
				<p class="wpda-import-form">
					<input type='hidden' name='action' value='import' />
					<input type='hidden' name='csv_id' value='<?php echo esc_attr( $csv_id ); ?>' />
					<input type='hidden' name='_wpnonce' value='<?php echo esc_attr( $wp_nonce ); ?>'>
					<input type="submit" class="button" value="Start Import" />
				</p>
			</fieldset>
			</form>
			<?php
		}

		/**
		 * Import body
		 *
		 * @return void
		 */
		protected function show_body_import() {
			$csv_id =
				isset( $_REQUEST['csv_id'] ) ? // phpcs:ignore WordPress.Security.NonceVerification
					sanitize_text_field( wp_unslash( $_REQUEST['csv_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

			// Security check.
			$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '?'; // phpcs:ignore WordPress.Security.NonceVerification
			if ( ! wp_verify_nonce( $wp_nonce, "wpda-import-csv-{$csv_id}" ) ) {
				wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}

			$truncate_table =
				isset( $_REQUEST['truncate_table'] ) ? // phpcs:ignore WordPress.Security.NonceVerification
					sanitize_text_field( wp_unslash( $_REQUEST['truncate_table'] ) ) : 'off'; // phpcs:ignore WordPress.Security.NonceVerification

			echo '<p>Reading CSV file info...</p>';
			$dbrow = WPDA_CSV_Uploads_Model::query( $csv_id );

			global $wpdb;
			if ( 1 === $wpdb->num_rows ) {
				if ( ! isset( $dbrow[0]->csv_real_file_name ) ) {
					echo '<p style="font-weight: bold">ERROR: No CSV file found for this import</p>';
					return;
				}

				if ( ! isset( $dbrow[0]->csv_mapping ) ) {
					echo '<p style="font-weight: bold">ERROR: Cannot import CSV file without column mapping</p>';
					return;
				}

				echo '<p>Validating column mapping...</p>';
				$upload_dir         = WPDA::get_plugin_upload_dir();
				$file_name          = $upload_dir . $dbrow[0]->csv_real_file_name;
				$mapping            = json_decode( $dbrow[0]->csv_mapping, true );
				$delimiter          = isset( $mapping['settings']['delimiter'] ) ? $mapping['settings']['delimiter'] : ',';
				$date_format        = isset( $mapping['settings']['date_format'] ) ? $mapping['settings']['date_format'] : '%Y-%m-%d';
				$has_header_columns = isset( $mapping['settings']['has_header_columns'] ) ? $mapping['settings']['has_header_columns'] : true;
				$schema_name        = isset( $mapping['database']['wpdaschema_name'] ) ? esc_attr( $mapping['database']['wpdaschema_name'] ) : '';
				$table_name         = isset( $mapping['database']['table_name'] ) ? esc_attr( $mapping['database']['table_name'] ) : '';
				$table_name         = str_replace( '`', '', $table_name );
				$table_columns      = isset( $mapping['columns'] ) ? $mapping['columns'] : array();

				if ( 'on' === $truncate_table ) {
					// Truncate table.
					$wpdadb = WPDADB::get_db_connection( $schema_name );
					$wpdadb->query(
						$wpdb->prepare(
							'truncate table `%1s`', // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
							array(
								WPDA::remove_backticks( $table_name ),
							)
						)
					);
					echo '<p><strong>Table `' . esc_attr( $table_name ) . '` truncated...</strong></p>';
				}

				$columns_inserted = '';
				foreach ( $table_columns as $table_column ) {
					$columns_inserted .= $table_column . ',';
				}
				$columns_inserted = substr( $columns_inserted, 0, strlen( $columns_inserted ) - 1 );

				$data_type         = array();
				$data_type_before  = array();
				$data_type_after   = array();
				$wpda_list_columns = WPDA_List_Columns_Cache::get_list_columns( $schema_name, $table_name );
				$column_data_types = $wpda_list_columns->get_table_columns();
				foreach ( $column_data_types as $column ) {
					$data_type[ $column['column_name'] ] = WPDA::get_type( $column['data_type'] );
					switch ( $data_type[ $column['column_name'] ] ) {
						case 'number':
							$data_type_before[ $column['column_name'] ] = '';
							$data_type_after[ $column['column_name'] ]  = '';
							break;
						case 'date':
							$data_type_before[ $column['column_name'] ] = "str_to_date('";
							$data_type_after[ $column['column_name'] ]  = "','$date_format')";
							break;
						default:
							$data_type_before[ $column['column_name'] ] = "'";
							$data_type_after[ $column['column_name'] ]  = "'";
					}
				}

				// phpcs:disable
				echo '<p>Enabling buffering...</p>';
				set_time_limit( 0 );
				@ini_set( 'zlib.output_compression', false );
				@ini_set( 'implicit_flush', true );
				@ini_set( 'output_buffering', true );
				@ini_set( 'display_errors', false );
				ob_implicit_flush( true );
				echo '<p>Reading CSV file...</p>';
				@ini_set( 'auto_detect_line_endings', true );
				// phpcs:enable

				if ( false !== ( $fp = fopen( $file_name, 'rb' ) ) ) { // phpcs:ignore
					$wpdadb = WPDADB::get_db_connection( $schema_name );
					if ( null !== $wpdadb ) {
						$row      = 0;
						$inserted = 0;
						$errors   = 0;
						echo '<p>Connecting to database...</p>';
						$suppress_errors = $wpdadb->suppress_errors( true );
						while ( false !== ( $data = fgetcsv( $fp, 0, $delimiter, '"' ) ) ) { // phpcs:ignore
							if ( 0 === $row && 'true' === $has_header_columns ) { // phpcs:ignore
								// Skip first row.
							} else {
								// Prepare insert array.
								$wpda_insert_column_values = array();
								$row_values_valid          = true;
								for ( $column = 0; $column < count( $data ); $column++ ) { // phpcs:ignore
									if ( isset( $table_columns[ $column ] ) ) {
										// Add column to array.
										if ( isset( $table_columns[ $column ] ) ) {
											if (
												'number' === $data_type[ $table_columns[ $column ] ] ||
												'date' === $data_type[ $table_columns[ $column ] ]
											) {
												if ( null === $data[ $column ] || 'null' === $data[ $column ] || '' === $data[ $column ] ) {
													$wpda_insert_column_values[ $table_columns[ $column ] ] = null;
												} else {
													if ( 'number' === $data_type[ $table_columns[ $column ] ] ) {
														$wpda_insert_column_values[ $table_columns[ $column ] ] = $data[ $column ];
													} else {
														try {
															$date_value   = substr( $data[ $column ], 0, 10 );
															$convert_date = \DateTime::createFromFormat( str_replace( '%', '', $date_format ), $date_value );
															if ( false === $convert_date ) {
																$error_msg = 'Cannot convert ' . esc_attr( $date_value ) . ' to date (using format ' . esc_attr( $date_format ) . ')';
																echo "<div>ERROR: {$error_msg}</div>";
																$row_values_valid = false;

																// Write error to log file
																WPDA::wpda_log_wp_error( $error_msg );
															} else {
																$wpda_insert_column_values[ $table_columns[ $column ] ] = $convert_date->format( 'Y-m-d' );
															}
														} catch ( \Exception $e ) {
															echo '<div>Cannot convert ' . esc_attr( $date_value ) . ' to date (using format ' . esc_attr( $date_format ) . ')</div>';
															echo "<div>ERROR: {$error_msg}</div>";
															$row_values_valid = false;

															// Write error to log file
															WPDA::wpda_log_wp_error( $error_msg );
														}
													}
												}
											} else {
												$wpda_insert_column_values[ $table_columns[ $column ] ] = $data[ $column ];
											}
										}
									}
								}
								if ( $row_values_valid ) {
									// Insert row.
									$result = $wpdadb->insert(
										$table_name,
										$wpda_insert_column_values
									); // db call ok; no-cache ok.
									if ( 1 === $result ) {
										$inserted++;
									} else {
										// Try to insert with plain sql.
										$insert  = "insert into `{$wpdadb->dbname}`.`{$table_name}` ";
										$insert .= "({$columns_inserted}) values (";
										for ( $column = 0; $column < count( $data ); $column++ ) { // phpcs:ignore
											if ( isset( $table_columns[ $column ] ) ) {
												if (
													'' === $data[ $column ] &&
													(
														'number' === $data_type[ $table_columns[ $column ] ] ||
														'date' === $data_type[ $table_columns[ $column ] ]
													)
												) {
													$insert .=
														'null,';
												} else {
													$insert .=
														$data_type_before[ $table_columns[ $column ] ] .
														str_replace( "'", "\'", $data[ $column ] ) .
														$data_type_after[ $table_columns[ $column ] ] .
														',';
												}
											}
										}
										$insert  = substr( $insert, 0, strlen( $insert ) - 1 );
										$insert .= ')';
										if ( false === $wpdadb->query( $insert ) ) {
											if ( '' === $wpdadb->last_error ) {
												echo 'Error: ' . esc_attr( $insert ) . '<br/>';

												// Write error to log file
												WPDA::wpda_log_wp_error( $insert );
											} else {
												echo 'Error: ' . esc_attr( $wpdadb->last_error ) . '<br/>';

												// Write error to log file
												WPDA::wpda_log_wp_error( $wpdadb->last_error );
											}
											$errors++;
										} else {
											$inserted++;
										}
									}
								} else {
									$errors++;
								}
							}
							$row++;
						}
						$wpdadb->suppress_errors( $suppress_errors );
					}
					fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

					$row--;
					echo "<p style='font-weight: bold'>Import ready!</p>";
					echo esc_attr( $row ) . ' rows processed<br/>';
					echo esc_attr( $inserted ) . ' rows inserted<br/>';
					echo esc_attr( $errors ) . ' rows with errors<br/>';
				} else {
					echo '<p style="font-weight: bold">ERROR: File not found</p>';
				}
			}
		}

		/**
		 * Body reload
		 *
		 * @return void
		 */
		protected function show_body_reload() {
			$csv_id =
				isset( $_REQUEST['csv_id'] ) ?
					sanitize_text_field( wp_unslash( $_REQUEST['csv_id'] ) ) : ''; // input var okay.

			// Security check.
			$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '?'; // input var okay.
			if ( ! wp_verify_nonce( $wp_nonce, "wpda-reload-csv-{$csv_id}" ) ) {
				wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}

			$this->show_body_upload();
		}

		/**
		 * Upload file
		 *
		 * @return void
		 */
		protected function upload_file() {
			// Security check.
			$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '?'; // input var okay.
			if ( ! wp_verify_nonce( $wp_nonce, "wpda-import-csv-{$this->schema_name}" ) ) {
				wp_die( __( 'ERROR: Not authorized', 'wp-data-access' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}

			// phpcs:disable
			$temp_file_name = isset( $_FILES['filename']['tmp_name'] ) ?
				sanitize_text_field( $_FILES['filename']['tmp_name'] ) : ''; // For Windows: do NOT unslash!
			// phpcs:enable
			$orig_file_name = isset( $_FILES['filename']['name'] ) ?
				sanitize_text_field( wp_unslash( $_FILES['filename']['name'] ) ) : '';

			if ( isset( $_FILES['filename'] ) && isset( $_REQUEST['csv_name'] ) ) {

				if (
					isset( $_FILES['filename']['error'] ) &&
					UPLOAD_ERR_OK === $_FILES['filename']['error'] &&
					is_uploaded_file( $temp_file_name )
				) {
					echo '<br/>';
					echo __( 'Uploading file', 'wp-data-access' ) . ' <strong>' . esc_attr( $orig_file_name ) . '</strong>...'; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '<br/><br/>';

					$upload_dir     = WPDA::get_plugin_upload_dir();
					$real_file_name = 'wpda_csv_upload_' . gmdate( 'YmdHis' ) . '.csv';

					// Process file and save a local copy.
					$fp = $this->file_pointer = fopen( $temp_file_name, 'rb' ); // phpcs:ignore
					if ( false !== $this->file_pointer ) {
						if ( ! is_dir( $upload_dir ) ) {
                            WPDA::wpda_create_content_folder();
						}

						$fw = fopen( $upload_dir . "{$real_file_name}", 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
						while ( ! feof( $this->file_pointer ) ) {
							$file_content = fread( $this->file_pointer, 1024 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread
							fwrite( $fw, $file_content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
						}
					}
					fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					fclose( $fw ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

					echo __( 'Saving file info...', 'wp-data-access' ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo '<br/><br/>';

					$csv_id   =
						isset( $_REQUEST['csv_id'] ) ?
							sanitize_text_field( wp_unslash( $_REQUEST['csv_id'] ) ) : ''; // input var okay.
					$csv_name = sanitize_text_field( wp_unslash( $_REQUEST['csv_name'] ) ); // input var okay.

					if ( '' === $csv_id ) {
						// New CSV import.
						$result = WPDA_CSV_Uploads_Model::insert( $csv_name, $real_file_name, $orig_file_name );
					} else {
						// Reload CSV.
						$oldrow = WPDA_CSV_Uploads_Model::query( $csv_id );
						$result = WPDA_CSV_Uploads_Model::update( $csv_id, $real_file_name, $orig_file_name );
						if ( $result ) {
							// Remove old file.
							if ( isset( $oldrow[0]->csv_real_file_name ) ) {
								unlink( WPDA::get_plugin_upload_dir() . $oldrow[0]->csv_real_file_name );
							}
						}
					}
					if ( false === $result ) {
						$msg = new WPDA_Message_Box(
							array(
								'message_text'           => __( 'Processing CSV file failed', 'wp-data-access' ),
								'message_type'           => 'error',
								'message_is_dismissible' => false,
							)
						);
						$msg->box();
					} else {
						$wp_nonce_csv_id = '' === $csv_id ? $result : $csv_id;
						$wp_nonce_action = "wpda-mapping-{$wp_nonce_csv_id}";
						$wp_nonce        = esc_attr( wp_create_nonce( $wp_nonce_action ) );
						?>
						<strong>Upload successful</strong>
						<br/><br/>
						<form method="post"
								action="?page=wpda&page_action=wpda_import_csv"
								style="display: inline-block; vertical-align: baseline;">
							<input type="hidden"
									name="csv_id"
									value="<?php echo '' === $csv_id ? esc_attr( $result ) : esc_attr( $csv_id ); ?>"
							>
							<input type="hidden"
									name="action"
									value="mapping"
							>
							<input type="submit"
									class="page-title-action"
									style="margin-left: 0;"
									value="<?php echo __( 'Column mapping' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"
							/>
							<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $wp_nonce ); ?>" />
						</form>&nbsp;
						<form method="post"
								action="?page=wpda&page_action=wpda_import_csv"
								style="display: inline-block; vertical-align: baseline;">
							<input type="submit"
									class="page-title-action"
									style="margin-left: 0;"
									value="<?php echo __( 'CSV file list' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"
							/>
						</form>
						<?php
					}
				}
			} else {
				// File upload failed: inform user.
				$msg = new WPDA_Message_Box(
					array(
						'message_text'           => __( 'File upload failed', 'wp-data-access' ),
						'message_type'           => 'error',
						'message_is_dismissible' => false,
					)
				);
				$msg->box();
			}
		}

	}

}
