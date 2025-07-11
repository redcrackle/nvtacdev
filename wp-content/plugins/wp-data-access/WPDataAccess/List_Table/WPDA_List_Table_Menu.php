<?php

/**
 * Suppress "error - 0 - No summary was found for this file" on phpdoc generation
 *
 * @package WPDataAccess\List_Table
 */
namespace WPDataAccess\List_Table;

use WP_Data_Access_Admin;
use WPDataAccess\Connection\WPDADB;
use WPDataAccess\Data_Dictionary\WPDA_Dictionary_Access;
use WPDataAccess\Data_Dictionary\WPDA_Dictionary_Exist;
use WPDataAccess\Data_Dictionary\WPDA_Dictionary_Lists;
use WPDataAccess\Data_Dictionary\WPDA_List_Columns_Cache;
use WPDataAccess\Plugin_Table_Models\WPDA_Media_Model;
use WPDataAccess\Plugin_Table_Models\WPDA_Table_Settings_Model;
use WPDataAccess\Plugin_Table_Models\WPDA_User_Menus_Model;
use WPDataAccess\Utilities\WPDA_Favourites;
use WPDataAccess\Utilities\WPDA_Import_Multi;
use WPDataAccess\Utilities\WPDA_Message_Box;
use WPDataAccess\WPDA;
/**
 * Class WPDA_List_Table_Menu
 *
 * This class implements the Data Explorer shown in the plugin menu.
 *
 * WPDA_List_Table_Menu extends WPDA_List_Table. Although both list tables basically offer the same functionality
 * WPDA_List_Table_Menu selects data from MySQL view 'information_schema.tables', where WPDA_List_Table selects
 * data from tables that are located in the WordPress database schema. The view that serves as the 'base table'
 * for WPDA_List_Table_Menu is not updatable. A data entry form is therefore not available for WPDA_List_Table_Menu.
 *
 * Export functionality word WPDA_List_Table_Menu differs from WPDA_List_Table as well. WPDA_List_Table_Menu allows
 * to export single tables, as well as multiple tables at once as s bulk action.
 *
 * When the user clicks on 'view', a list table for the selected table of view is shown.
 *
 * @author  Peter Schulz
 * @since   1.0.0
 */
class WPDA_List_Table_Menu extends WPDA_List_Table {
    const LOADING = 'Loading...';

    const WPDACHK = 'wp-data-access-premium-data-services';

    /**
     * Holds tables marked as favourite
     *
     * @var array|null
     */
    protected $favourites = null;

    /**
     * Show, hide or empty
     *
     * @var string|null
     */
    protected $wpda_main_favourites = null;

    /**
     * Indicates whether innodb_file_per_table is enabled or disabled
     *
     * @var bool
     */
    protected $innodb_file_per_table = true;

    /**
     * Can user create database
     *
     * @var bool
     */
    protected $user_can_create_db = false;

    /**
     * Hint button create db
     *
     * @var string
     */
    protected $user_create_db_hint = '';

    /**
     * Can user drop database
     *
     * @var bool
     */
    protected $user_can_drop_db = false;

    /**
     * Hint button drop db
     *
     * @var string
     */
    protected $user_drop_db_hint = '';

    /**
     * Used to switch to another schema (after create/drop db)
     *
     * @var null
     */
    protected $switch_schema_name = null;

    /**
     * WPDA_List_Table_Menu constructor
     *
     * Constructor calls constructor of WPDA_List_Table. Before calling constructor of WPDA_List_Table the
     * table name is set to {@see WPDA_List_Table::LIST_BASE_TABLE} which gives us the base table for the data
     * explorer.
     *
     * Column headers  and columns queried are defined hardcoded as this class handles only one base table and it's
     * columns (table specific implementation).
     *
     * @param array $args See {@see WPDA_List_Table::__construct()}.
     *
     * @see WPDA_List_Table
     *
     * @since   1.0.0
     */
    public function __construct( $args = array() ) {
        global $wpdb;
        $args['table_name'] = WPDA_List_Table::LIST_BASE_TABLE;
        // Add column labels.
        $args['column_headers'] = self::column_headers_labels();
        $args['title'] = 'Data Explorer';
        $args['subtitle'] = '';
        // Define an empty subtitle to prevent WPDA_List_Table from adding one.
        if ( isset( $_REQUEST['action'] ) ) {
            // Process create, drop and edit table actions before calling parent constructor to allow to redirect to
            // another database.
            if ( 'create_db' === $_REQUEST['action'] ) {
                $this->process_bulk_action_create_db();
            } elseif ( 'drop_db' === $_REQUEST['action'] ) {
                $this->process_bulk_action_drop_db();
            } elseif ( 'edit_db' === $_REQUEST['action'] ) {
                $this->process_bulk_action_edit_db();
            }
        }
        parent::__construct( $args );
        $this->set_columns_queried( array(
            'table_name AS table_name',
            'if (find_in_set(table_name,\'' . implode( ',', WPDA::get_wp_tables() ) . '\')
						, \'' . WPDA::get_table_type_text( WPDA::TABLE_TYPE_WP ) . '\'
						, if (find_in_set(table_name,\'' . implode( ',', WPDA::get_wpda_tables() ) . '\')
							, \'' . WPDA::get_table_type_text( WPDA::TABLE_TYPE_WPDA ) . '\'
							, lower(table_type)
						)
					) AS table_type',
            'create_time AS create_time',
            'table_rows AS table_rows',
            'auto_increment AS auto_increment',
            'engine AS engine',
            'data_length+index_length AS total_size',
            'data_length AS data_size',
            'index_length AS index_size',
            'data_free AS overhead',
            'table_collation AS table_collation',
            'table_type AS table_type_db'
        ) );
        $this->favourites = get_option( WPDA_Favourites::FAVOURITES_OPTION_NAME );
        if ( null === $this->switch_schema_name ) {
            $this->schema_name = $this->get_schema_name();
        } else {
            $this->schema_name = $this->switch_schema_name;
        }
        $this->wpda_main_favourites = $this->get_favourites();
        // Instantiate WPDA_Import.
        $this->wpda_import = new WPDA_Import_Multi("?page={$this->page}", $this->schema_name);
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        $result = $wpdadb->get_row( "show session variables like 'innodb_file_per_table'" );
        if ( !empty( $result ) ) {
            $this->innodb_file_per_table = 'ON' === $result->Value;
        }
        // Can user create/drop databases?
        $this->user_can_create_db = WPDA_Dictionary_Access::can_create_db();
        if ( $this->user_can_create_db ) {
            $this->user_create_db_hint = __( 'Create local database/add remote database connection', 'wp-data-access' );
        } else {
            $this->user_create_db_hint = __( 'Add remote database connection (not authorized to create local database)', 'wp-data-access' );
        }
        $this->user_can_drop_db = WPDA_Dictionary_Access::can_drop_db();
        if ( $this->user_can_drop_db ) {
            if ( $wpdb->dbname === $this->schema_name ) {
                $this->user_can_drop_db = false;
                $this->user_drop_db_hint = __( 'Cannot drop WordPress database', 'wp-data-access' );
            } elseif ( 'sys' === $this->schema_name || 'mysql' === $this->schema_name || 'information_schema' === $this->schema_name || 'performance_schema' === $this->schema_name || '' === $this->schema_name ) {
                $this->user_can_drop_db = false;
                $this->user_drop_db_hint = __( 'Cannot drop MySQL database', 'wp-data-access' );
            } else {
                if ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ) {
                    $this->user_drop_db_hint = __( "Delete remote database connection.\nDoes not drop the database! Just deletes the connection definition.", 'wp-data-access' );
                } else {
                    $this->user_drop_db_hint = __( 'Drop selected database', 'wp-data-access' );
                }
            }
        } else {
            if ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ) {
                $this->user_can_drop_db = true;
                $this->user_drop_db_hint = __( "Delete remote database connection.\nDoes not drop the database! Just deletes the connection definition.", 'wp-data-access' );
            } else {
                $this->user_drop_db_hint = __( 'Cannot drop selected database [not authorized]', 'wp-data-access' );
            }
        }
    }

    /**
     * Overwrite method to add structure to the row below.
     *
     * @param object $item Item info.
     *
     * @since   1.5.0
     */
    public function single_row( $item ) {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
        //phpcs:ignore - 8.1 proof
        $columns_not_shown = count( (array) $hidden );
        //phpcs:ignore - 8.1 proof just to be sure
        echo '<tr id="rownum_' . esc_attr( self::$list_number ) . '">';
        $this->single_row_columns( $item );
        echo '</tr>';
        if ( $this->bulk_actions_enabled ) {
            echo '<tr style="height:0 !important; padding: 0 !important;"><td id="rownum_' . esc_attr( self::$list_number ) . '_x1" colspan="' . esc_attr( 13 - $columns_not_shown ) . '" style="padding:0 !important;"></tr>';
            // Fake! Maintain odd/even colors.
            echo '<tr style="padding: 0 !important;"><td style="padding: 0 !important;"></td><td id="rownum_' . esc_attr( self::$list_number ) . '_x2" colspan="' . esc_attr( 12 - $columns_not_shown ) . '" style="padding:0 10px 10px 0 !important;">';
        } else {
            echo '<tr style="height:0 !important; padding: 0 !important;"><td id="rownum_' . esc_attr( self::$list_number ) . '_x1" colspan="' . esc_attr( 12 - $columns_not_shown ) . '" style="padding:0 !important;"></tr>';
            // Fake! Maintain odd/even colors.
            echo '<tr style="padding: 0 !important;"><td id="rownum_' . esc_attr( self::$list_number ) . '_x2" colspan="' . esc_attr( 12 - $columns_not_shown ) . '" style="padding:0 10px 10px 10px !important;">';
        }
        echo '<div id="wpda_admin_menu_actions_' . esc_attr( self::$list_number - 1 ) . '" style="width:100%;display:none;padding-right:20px !important;">' . esc_attr( self::LOADING ) . '</div>';
        echo '</td></tr>';
    }

    /**
     * Override column_default
     *
     * We need to override this method as our table is in fact a view and has no real columns:
     * $this->wpda_list_columns->get_table_columns() returns no results
     *
     * We'll use jquery to write html forms to a container to make them accessible inside our list table.
     * See WPDA_List_Table->column_default for further explanation.
     *
     * @param array  $item Item info.
     * @param string $column_name Column name.
     *
     * @return mixed Actions for the current row.
     * @see WPDA_List_Table::column_default()
     *
     * @since   1.0.0
     */
    public function column_default( $item, $column_name ) {
        if ( 'icons' === $column_name ) {
            // Dummy column where icons are shown (favourites and row admin menu).
            $table_name = $item['table_name'];
            $favourite_class = 'dashicons-star-empty';
            $favourite_title = 'Add to favourites';
            if ( null !== $this->favourites && false !== $this->favourites ) {
                $favourites_array = $this->favourites;
                if ( isset( $favourites_array[$table_name] ) ) {
                    $favourite_class = 'dashicons-star-filled';
                    $favourite_title = 'Remove from favourites';
                }
            }
            $favourites_menu = "<a href=\"javascript:void( 0 )\" class=\"wpda_tooltip\" title=\"{$favourite_title}\" onclick=\"wpda_list_table_favourite( '{$this->schema_name}', '{$table_name}' )\">\n\t\t\t\t\t\t<span id=\"span_favourites_{$table_name}\" class=\"dashicons {$favourite_class}\"></span>\n\t\t\t\t\t</a>";
            return $favourites_menu;
        }
        if ( 'table_rows' === $column_name ) {
            if ( is_scalar( $item['engine'] ) && is_scalar( $item['table_type'] ) && ('federated' === strtolower( $item['engine'] ) || 'connect' === strtolower( $item['engine'] ) || 'innodb' === strtolower( $item['engine'] ) || stripos( strtolower( $item['table_type'] ), 'view' ) !== false) ) {
                $settings_db = WPDA_Table_Settings_Model::query( $item['table_name'], $this->schema_name );
                if ( isset( $settings_db[0]['wpda_table_settings'] ) && '' !== $settings_db[0]['wpda_table_settings'] ) {
                    $settings = json_decode( $settings_db[0]['wpda_table_settings'] );
                } else {
                    $settings = (object) null;
                }
                $row_count_estimate = WPDA::get_row_count_estimate( $this->schema_name, $item['table_name'], $settings );
                if ( !$row_count_estimate['do_real_count'] ) {
                    if ( $row_count_estimate['is_estimate'] ) {
                        return stripslashes( '~' . $row_count_estimate['row_count'] );
                    } else {
                        return stripslashes( $row_count_estimate['row_count'] );
                    }
                } else {
                    return stripslashes( $this->count_rows( $item['table_name'] ) );
                }
            } else {
                return stripslashes( (string) $item[$column_name] );
            }
        }
        if ( 'total_size' === $column_name || 'data_size' === $column_name || 'index_size' === $column_name ) {
            if ( '' === stripslashes( (string) $item[$column_name] ) ) {
                return stripslashes( (string) $item[$column_name] );
            } else {
                if ( $item[$column_name] / (1024 * 1024) > 1 ) {
                    return number_format(
                        stripslashes( $item[$column_name] / (1024 * 1024) ),
                        2,
                        '.',
                        ','
                    ) . ' MB';
                } elseif ( $item[$column_name] / 1024 > 1 ) {
                    return number_format(
                        $item[$column_name] / 1024,
                        2,
                        '.',
                        ','
                    ) . ' KB';
                }
                return number_format(
                    stripslashes( $item[$column_name] ),
                    2,
                    '.',
                    ','
                ) . ' bytes';
            }
        }
        if ( 'overhead' === $column_name ) {
            if ( 'InnoDB' === $item['engine'] && !$this->innodb_file_per_table ) {
                return '-';
            } else {
                if ( '' === stripslashes( (string) $item[$column_name] ) ) {
                    return '';
                } else {
                    if ( $item[$column_name] > 0 && $item['data_size'] > 0 && $item[$column_name] / $item['data_size'] > 0.2 ) {
                        $msg = __( "Fragmentation for this table is high.\nConsider: Manage>Actions>Optimize", 'wp-data-access' );
                        $approx = " <span style='font-size:15px;' class='dashicons dashicons-warning wpda_tooltip' title='{$msg}' style'cursor:pointer;'></span>";
                    } else {
                        $approx = '';
                    }
                    if ( $item[$column_name] / (1024 * 1024) > 1 ) {
                        return number_format(
                            stripslashes( $item[$column_name] / (1024 * 1024) ),
                            2,
                            '.',
                            ','
                        ) . ' MB' . $approx;
                    } elseif ( $item[$column_name] / 1024 > 1 ) {
                        return number_format(
                            stripslashes( $item[$column_name] / 1024 ),
                            2,
                            '.',
                            ','
                        ) . ' KB' . $approx;
                    }
                    return number_format(
                        stripslashes( $item[$column_name] ),
                        2,
                        '.',
                        ','
                    ) . ' bytes' . $approx;
                }
            }
        }
        if ( 'table_name' === $column_name ) {
            // Get table name of the current row (= key).
            $table_name = $item[$column_name];
            $admin_actions = array();
            // Array containing admin actions.
            if ( WPDA::can_manage() ) {
                // Add manage table/view line.
                $wp_nonce_action_table_actions = "wpda-actions-{$table_name}";
                $wp_nonce_table_actions = wp_create_nonce( $wp_nonce_action_table_actions );
                $table_actions_title = sprintf( __( 'Table %s settings', 'wp-data-access' ), $table_name );
                $actions['wpda_manage'] = "<a href=\"javascript:void( 0 )\" class='wpda_tooltip' title='{$table_actions_title}' onclick=\"wpda_show_table_actions( '{$this->schema_name}', '{$table_name}', '" . self::$list_number . "', '{$wp_nonce_table_actions}', '{$item['table_type_db']}', '" . self::LOADING . "' ); this.blur();\">" . '<i class="fas fa-gears wpda_icon_on_button"></i> ' . __( 'Manage', 'wp-data-access' ) . '</a>';
            }
            self::$list_number++;
            // Prepare type checking for editing.
            $check_view_access = 'true';
            if ( 'on' === WPDA::get_option( WPDA::OPTION_BE_CONFIRM_VIEW ) ) {
                if ( 'VIEW' !== strtoupper( $item['table_type_db'] ) && 'SYSTEM VIEW' !== strtoupper( $item['table_type_db'] ) ) {
                    if ( strtolower( WPDA::TABLE_TYPE_WP ) === strtolower( $item['table_type'] ) ) {
                        // WordPress table.
                        $msg = __( 'You are about to edit a WordPress table! Changing this table might result in corrupting the WordPress database. Are you sure you want to continue?', 'wp-data-access' );
                    } elseif ( strtolower( WPDA::TABLE_TYPE_WPDA ) === strtolower( $item['table_type'] ) ) {
                        // Plugin table.
                        $msg = __( 'You are about to edit a plugin table! Changing this table might result in corrupting the WP Data Access database. Are you sure you want to continue?', 'wp-data-access' );
                    } else {
                        // User table (other than WordPress or plugin).
                        $msg = __( 'You are about to edit a table of an external application! Changing this table might result in corrupting the external database. Are you sure you want to continue?', 'wp-data-access' );
                    }
                    $check_view_access = 'confirm(\'' . $msg . '\')';
                }
            }
            $esc_attr = 'esc_attr';
            $form_name = 'explore_' . self::$list_number;
            $url = "?page={$this->page}";
            $form = <<<EOT
\t\t\t\t<form id='{$esc_attr( $form_name )}' action='{$esc_attr( $url )}' method='post'>
\t\t\t\t\t<input type='hidden' name='wpdaschema_name' value='{$esc_attr( $this->schema_name )}' />
\t\t\t\t\t<input type='hidden' name='table_name' value='{$esc_attr( $table_name )}' />
\t\t\t\t\t<input type='hidden' name='action' value='listtable' />
\t\t\t\t</form>
EOT;
            $explore = str_replace( array("\n", "\r"), '', $form );
            ?>

				<script type='text/javascript'>
					jQuery("#wpda_invisible_container").append("<?php 
            echo $explore;
            // phpcs:ignore WordPress.Security.EscapeOutput
            ?>");
				</script>

				<?php 
            $action_view = ' <i class="fas fa-table-list wpda_icon_on_button"></i> ' . __( 'Explore', 'wp-data-access' );
            $table_view_title = sprintf( __( 'Explore %s table', 'wp-data-access' ), $table_name );
            $actions['wpda_listtable'] = sprintf(
                '<a href="javascript:void(0)"
						       title="%s"
                               class="view wpda_tooltip"
                               onclick="if (%s) jQuery(\'#%s\').submit()">
                               %s
                            </a>',
                $table_view_title,
                $check_view_access,
                $form_name,
                $action_view
            );
            if ( strtolower( WPDA::TABLE_TYPE_WP ) === strtolower( $item['table_type'] ) ) {
                ?>
					<script type='text/javascript'>
						wpda_bulk.push('<?php 
                echo esc_attr( $table_name );
                ?>');
					</script>
					<?php 
            }
            if ( strtolower( WPDA::TABLE_TYPE_WP ) !== strtolower( $item['table_type'] ) && strtolower( WPDA::TABLE_TYPE_WPDA ) !== strtolower( $item['table_type'] ) ) {
                // Validate schema, table and column names
                $warning = WPDA::validate_names( $this->schema_name, $table_name );
            } else {
                $warning = '';
            }
            return sprintf(
                '%1$s %2$s %3$s',
                $table_name . $warning,
                "<span class=\"nobr\">{$this->row_actions( $actions )}</span>",
                "<span id=\"span_admin_menu_{$table_name}\" class=\"nobr\" style=\"display:none;width:auto;float:clear;\">{$this->row_actions( $admin_actions )}</span>"
            );
        }
        if ( 'create_time' === $column_name ) {
            if ( null !== $item[$column_name] ) {
                return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item[$column_name] ) );
            } else {
                return '';
            }
        }
        return stripslashes( (string) $item[$column_name] );
    }

    /**
     * Count the number of rows in a table.
     *
     * This method is used to get the number of rows in an InnoDB table as the table_rows column of
     * information_schema.tables only return an estimate.
     *
     * @param string $table_name Database table name.
     *
     * @return integer|null Number of rows in $table_name.
     * @since   1.5.1
     */
    protected function count_rows( $table_name ) {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        if ( '' === $this->schema_name ) {
            $query = "\n\t\t\t\t\tselect count(*)\n\t\t\t\t\tfrom `{$table_name}`\n\t\t\t\t";
        } else {
            $query = "\n\t\t\t\t\tselect count(*)\n\t\t\t\t\tfrom `{$wpdadb->dbname}`.`{$table_name}`\n\t\t\t\t";
        }
        $suppress = $wpdadb->suppress_errors( true );
        $count = @$wpdadb->get_var( $query );
        // phpcs:ignore Standard.Category.SniffName.ErrorCode
        if ( !is_numeric( $count ) ) {
            $count = '<span class="dashicons dashicons-flag wpda_tooltip" style="color:red;padding-left:5px" title="' . str_replace( '"', "'", $wpdadb->last_error ) . '"></span>';
        }
        $wpdadb->suppress_errors( $suppress );
        return $count;
    }

    /**
     * Override get_columns
     *
     * @return array
     * @see WPDA_List_Table::get_columns()
     *
     * @since   1.0.0
     */
    public function get_columns() {
        $columns = array();
        if ( $this->bulk_actions_enabled ) {
            $columns = array(
                'cb' => '<input type="checkbox" />',
            );
        }
        return array_merge( $columns, $this->column_headers );
        //phpcs:ignore - 8.1 proof
    }

    /**
     * Override get_sortable_columns()
     *
     * Type is not sortable as it is not in the view.
     *
     * @return array
     * @see WPDA_List_Table::get_sortable_columns()
     *
     * @since   1.0.0
     */
    public function get_sortable_columns() {
        $columns = array();
        $columns['table_name'] = array('table_name', false);
        $columns['table_type'] = array('table_type', false);
        $columns['engine'] = array('engine', false);
        $columns['create_time'] = array('create_time', false);
        $columns['table_rows'] = array('table_rows', false);
        $columns['auto_increment'] = array('auto_increment', false);
        $columns['total_size'] = array('total_size', false);
        $columns['data_size'] = array('data_size', false);
        $columns['index_size'] = array('index_size', false);
        $columns['overhead'] = array('overhead', false);
        $columns['table_collation'] = array('table_collation', false);
        return $columns;
    }

    /**
     * Override column_cb
     *
     * @param array $item Column info.
     *
     * @return string
     * @since   1.0.0
     *
     * @see WPDA_List_Table::column_cb()
     */
    public function column_cb( $item ) {
        if ( !$this->bulk_actions_enabled ) {
            // Bulk actions disabled.
            return '';
        }
        if ( 'VIEW' === $item['table_type_db'] ) {
            $title = 'title="Performs only drop actions on views"';
            $class = 'class="wpda_tooltip"';
        } else {
            $title = '';
            $class = '';
        }
        return "<input type='checkbox' name='bulk-selected[]' value='{$item['table_name']}' {$title} {$class} />";
    }

    /**
     * Override get_bulk_actions
     *
     * @return array
     * @see WPDA_List_Table::get_bulk_actions()
     *
     * @since   1.0.0
     */
    public function get_bulk_actions() {
        if ( !$this->bulk_actions_enabled ) {
            // Bulk actions disabled.
            return '';
        }
        $actions = array();
        $actions['bulk-export'] = __( 'Export Table(s)', 'wp-data-access' );
        $actions['bulk-drop'] = __( 'Drop Table(s)/View(s) (does not drop WordPress tables)', 'wp-data-access' );
        $actions['bulk-truncate'] = __( 'Truncate Table(s) (does not truncate WordPress tables)', 'wp-data-access' );
        return $actions;
    }

    /**
     * Override process_bulk_action()
     *
     * @since   1.0.0
     *
     * @see WPDA_List_Table::process_bulk_action()
     */
    public function process_bulk_action() {
        switch ( $this->current_action() ) {
            case 'bulk-export':
                $this->process_bulk_action_bulk_export();
                break;
            case 'bulk-drop':
                $this->process_bulk_action_bulk_drop();
                break;
            case 'bulk-truncate':
                $this->process_bulk_action_bulk_truncate();
                break;
            case 'drop':
                $this->process_bulk_action_drop();
                break;
            case 'truncate':
                $this->process_bulk_action_truncate();
                break;
            case 'rename-table':
                $this->process_bulk_action_rename_table();
                break;
            case 'copy-table':
                $this->process_bulk_action_copy_table();
                break;
            case 'optimize-table':
                $this->process_bulk_action_optimize_table();
                break;
            case 'refresh-table':
                $this->process_bulk_action_refresh_table();
                break;
            case 'c2wp-table':
                $this->process_bulk_action_c2wp_table();
                break;
        }
    }

    protected function process_bulk_action_refresh_table() {
    }

    protected function process_bulk_action_drop_db() {
        if ( !$this->process_bulk_action_check_wpnonce( 'wpda-drop-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnoncedropdb' ) ) {
            return;
        }
        if ( !isset( $_REQUEST['database'] ) ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => sprintf( __( 'Cannot drop database [missing argument]', 'wp-data-access' ) ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return;
        }
        global $wpdb;
        $database = str_replace( '`', '', sanitize_text_field( wp_unslash( $_REQUEST['database'] ) ) );
        // input var okay.
        if ( 'rdb:' === substr( $database, 0, 4 ) ) {
            // Delete remote database
            if ( false === WPDADB::get_remote_database( $database ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot delete remote database connection `%s` [remote database connection not found]', 'wp-data-access' ), $database ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
            } else {
                if ( false === WPDADB::del_remote_database( $database ) ) {
                    $msg = new WPDA_Message_Box(array(
                        'message_text'           => sprintf( __( 'Cannot delete remote database connection `%s`', 'wp-data-access' ), $database ),
                        'message_type'           => 'error',
                        'message_is_dismissible' => false,
                    ));
                    $msg->box();
                } else {
                    $msg = new WPDA_Message_Box(array(
                        'message_text' => sprintf( __( 'Remove database `%s` deleted', 'wp-data-access' ), $database ),
                    ));
                    $msg->box();
                    $this->switch_schema_name = $wpdb->dbname;
                }
            }
        } else {
            // Drop local database
            if ( $wpdb->dbname === $database ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => __( 'Cannot drop WordPress database', 'wp-data-access' ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            if ( 'sys' === $database || 'mysql' === $database || 'information_schema' === $database || 'performance_schema' === $database || '' === $database ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => __( 'Cannot drop MySQL database', 'wp-data-access' ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            if ( false === $wpdb->query( $wpdb->prepare( 
                'drop database `%1s`',
                // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
                array(WPDA::remove_backticks( $database ))
             ) ) ) {
                // db call ok; no-cache ok.
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Error dropping database `%s`', 'wp-data-access' ), $database ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
            } else {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => sprintf( __( 'Database `%s` dropped', 'wp-data-access' ), $database ),
                ));
                $msg->box();
                $this->switch_schema_name = $wpdb->dbname;
            }
        }
    }

    protected function process_bulk_action_edit_db() {
        if ( !$this->process_bulk_action_check_wpnonce( 'wpda-edit-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnonceeditdb' ) ) {
            return;
        }
        if ( !isset( $_REQUEST['edit_remote_database'] ) ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => sprintf( __( 'Cannot update remote database connection [missing argument]', 'wp-data-access' ) ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return;
        }
        $database = sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_database'] ) );
        // input var okay.
        $database_old = sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_database_old'] ) );
        // input var okay.
        if ( $database !== $database_old ) {
            // Update database connection name
            if ( false === WPDADB::get_remote_database( $database_old ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot update remote database connection [remote database connection not found]', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
        } else {
            // Update database connection information
            if ( false === WPDADB::get_remote_database( $database ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot update remote database connection [remote database connection not found]', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
        }
        $host = ( isset( $_REQUEST['edit_remote_host'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_host'] ) ) : '' );
        // input var okay.
        $user = ( isset( $_REQUEST['edit_remote_user'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_user'] ) ) : '' );
        // input var okay.
        $passwd = ( isset( $_REQUEST['edit_remote_passwd'] ) ? wp_unslash( $_REQUEST['edit_remote_passwd'] ) : '' );
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $port = ( isset( $_REQUEST['edit_remote_port'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_port'] ) ) : '' );
        // input var okay.
        $schema = ( isset( $_REQUEST['edit_remote_schema'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_schema'] ) ) : '' );
        // input var okay.
        $ssl = ( isset( $_REQUEST['edit_remote_ssl'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_ssl'] ) ) : 'off' );
        // input var okay.
        $ssl_key = ( isset( $_REQUEST['edit_remote_client_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_client_key'] ) ) : '' );
        // input var okay.
        $ssl_cert = ( isset( $_REQUEST['edit_remote_client_certificate'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_client_certificate'] ) ) : '' );
        // input var okay.
        $ssl_ca = ( isset( $_REQUEST['edit_remote_ca_certificate'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_ca_certificate'] ) ) : '' );
        // input var okay.
        $ssl_path = ( isset( $_REQUEST['edit_remote_certificate_path'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_certificate_path'] ) ) : '' );
        // input var okay.
        $ssl_cipher = ( isset( $_REQUEST['edit_remote_specified_cipher'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['edit_remote_specified_cipher'] ) ) : '' );
        // input var okay.
        if ( '' === $database || '' === $host || '' === $user || '' === $schema ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => sprintf( __( 'Cannot edit remote database connection [missing arguments]', 'wp-data-access' ) ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return;
        }
        if ( !WPDADB::upd_remote_database(
            $database,
            $host,
            $user,
            $passwd,
            $port,
            $schema,
            false,
            $database_old,
            $ssl,
            $ssl_key,
            $ssl_cert,
            $ssl_ca,
            $ssl_path,
            $ssl_cipher
        ) ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => sprintf( __( 'Cannot update remote database connection `%s`', 'wp-data-access' ), $database ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
        } else {
            $msg = new WPDA_Message_Box(array(
                'message_text' => sprintf( __( 'Remote database connection `%s` updated', 'wp-data-access' ), $database ),
            ));
            $msg->box();
            if ( $database !== $database_old ) {
                $this->switch_schema_name = $database;
            }
        }
    }

    protected function process_bulk_action_create_db() {
        if ( !$this->process_bulk_action_check_wpnonce( 'wpda-create-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnoncecreatedb' ) ) {
            return;
        }
        if ( isset( $_REQUEST['database_location'] ) && 'local' === $_REQUEST['database_location'] ) {
            // Add local database
            if ( !isset( $_REQUEST['local_database'] ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot create database [missing argument]', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            $database = str_replace( '`', '', sanitize_text_field( wp_unslash( $_REQUEST['local_database'] ) ) );
            // input var okay.
            global $wpdb;
            if ( false === $wpdb->query( $wpdb->prepare( 
                'create database `%1s`',
                // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
                array(WPDA::remove_backticks( $database ))
             ) ) ) {
                // db call ok; no-cache ok.
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Error creating database `%s`', 'wp-data-access' ), $database ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
            } else {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => sprintf( __( 'Database `%s` created', 'wp-data-access' ), $database ),
                ));
                $msg->box();
                $this->switch_schema_name = $database;
            }
        } else {
            // Add remote database
            $database = ( isset( $_REQUEST['remote_database'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_database'] ) ) : '' );
            // input var okay.
            if ( false !== WPDADB::get_remote_database( $database ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Remote database connection already exists', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            $host = ( isset( $_REQUEST['remote_host'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_host'] ) ) : '' );
            // input var okay.
            $user = ( isset( $_REQUEST['remote_user'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_user'] ) ) : '' );
            // input var okay.
            $passwd = ( isset( $_REQUEST['remote_passwd'] ) ? wp_unslash( $_REQUEST['remote_passwd'] ) : '' );
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $port = ( isset( $_REQUEST['remote_port'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_port'] ) ) : '' );
            // input var okay.
            $schema = ( isset( $_REQUEST['remote_schema'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_schema'] ) ) : '' );
            // input var okay.
            $ssl = ( isset( $_REQUEST['remote_ssl'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_ssl'] ) ) : 'off' );
            // input var okay.
            $ssl_key = ( isset( $_REQUEST['remote_client_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_client_key'] ) ) : '' );
            // input var okay.
            $ssl_cert = ( isset( $_REQUEST['remote_client_certificate'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_client_certificate'] ) ) : '' );
            // input var okay.
            $ssl_ca = ( isset( $_REQUEST['remote_ca_certificate'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_ca_certificate'] ) ) : '' );
            // input var okay.
            $ssl_path = ( isset( $_REQUEST['remote_certificate_path'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_certificate_path'] ) ) : '' );
            // input var okay.
            $ssl_cipher = ( isset( $_REQUEST['remote_specified_cipher'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['remote_specified_cipher'] ) ) : '' );
            // input var okay.
            if ( '' === $database || '' === $host || '' === $user || '' === $schema ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot add remote database connection [missing argument]', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            if ( 'rdb:' === $database ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Invalid database name [enter a valid database name, for example rdb:remotedb]', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
                return;
            }
            if ( !WPDADB::add_remote_database(
                $database,
                $host,
                $user,
                $passwd,
                $port,
                $schema,
                $ssl,
                $ssl_key,
                $ssl_cert,
                $ssl_ca,
                $ssl_path,
                $ssl_cipher
            ) ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text'           => sprintf( __( 'Cannot add remote database connection', 'wp-data-access' ) ),
                    'message_type'           => 'error',
                    'message_is_dismissible' => false,
                ));
                $msg->box();
            } else {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => sprintf( __( 'Remote database connection `%s` added', 'wp-data-access' ), $database ),
                ));
                $msg->box();
                $this->switch_schema_name = $database;
            }
        }
    }

    /**
     * Performs optimize table.
     */
    protected function process_bulk_action_optimize_table() {
        if ( isset( $_REQUEST['optimize_table_name'] ) ) {
            $optimize_table_name = str_replace( '`', '', sanitize_text_field( wp_unslash( $_REQUEST['optimize_table_name'] ) ) );
            // input var okay.
            if ( $this->process_bulk_action_check_wpnonce( "wpda-optimize-{$optimize_table_name}", '_wpnonce' ) ) {
                $dbo_type = $this->get_dbo_type( $optimize_table_name );
                if ( false === $dbo_type || 'BASE TABLE' !== $dbo_type ) {
                    $msg = new WPDA_Message_Box(array(
                        'message_text'           => sprintf( __( 'Cannot optimize `%s`', 'wp-data-access' ), $optimize_table_name ),
                        'message_type'           => 'error',
                        'message_is_dismissible' => false,
                    ));
                    $msg->box();
                } else {
                    // Optimize table.
                    $wpdadb = WPDADB::get_db_connection( $this->schema_name );
                    if ( null === $wpdadb ) {
                        wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
                    }
                    $wpdadb->query( "optimize table `{$optimize_table_name}`" );
                    // db call ok; no-cache ok.
                    $msg = new WPDA_Message_Box(array(
                        'message_text' => sprintf( __( 'Table `%s` optimized', 'wp-data-access' ), $optimize_table_name ),
                    ));
                    $msg->box();
                }
            }
        }
    }

    /**
     * Performs rename table/view.
     *
     * @since   1.6.6
     */
    protected function process_bulk_action_rename_table() {
        // Check arguments.
        if ( !$this->process_bulk_action_check_action( 'rename_table_name_old', __( 'Missing old table name', 'wp-data-access' ) ) ) {
            return;
        }
        if ( $this->process_bulk_action_check_action( 'rename_table_name_new', __( 'Missing new table name', 'wp-data-access' ) ) ) {
            // Rename table is not allowed for WordPress tables (double check).
            $rename_table_name_old = sanitize_text_field( wp_unslash( $_REQUEST['rename_table_name_old'] ) );
            // input var okay.
            $rename_table_name_new = sanitize_text_field( wp_unslash( $_REQUEST['rename_table_name_new'] ) );
            // input var okay.
            $err_txt = ' ' . sprintf( __( '[cannot rename WordPress table `%s`]', 'wp-data-access' ), $rename_table_name_old );
            if ( '' === $rename_table_name_old ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing old table name value', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            if ( '' === $rename_table_name_new ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing new table name value', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            if ( $this->process_bulk_action_check_is_wp_table( $rename_table_name_old, $err_txt ) ) {
                // Check if table exists.
                if ( $this->process_bulk_action_check_table_exists( $rename_table_name_old ) ) {
                    // Check if rename is allowed.
                    if ( $this->process_bulk_action_check_wpnonce( "wpda-rename-{$rename_table_name_old}", '_wpnonce' ) ) {
                        $dbo_type = $this->get_dbo_type( $rename_table_name_old );
                        if ( false === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                            $msg = new WPDA_Message_Box(array(
                                'message_text'           => sprintf( __( 'Cannot rename `%s`', 'wp-data-access' ), $rename_table_name_old ),
                                'message_type'           => 'error',
                                'message_is_dismissible' => false,
                            ));
                            $msg->box();
                        } else {
                            // Rename table/view.
                            if ( false === $this->rename_table( $rename_table_name_old, $rename_table_name_new ) ) {
                                $msg = new WPDA_Message_Box(array(
                                    'message_text'           => __( 'Cannot rename', 'wp-data-access' ) . ' ' . strtolower( $dbo_type ) . ' `' . $rename_table_name_old . '`',
                                    'message_type'           => 'error',
                                    'message_is_dismissible' => false,
                                ));
                                $msg->box();
                            } else {
                                $msg = new WPDA_Message_Box(array(
                                    'message_text' => strtoupper( substr( $dbo_type, 0, 1 ) ) . strtolower( substr( $dbo_type, 1 ) ) . ' `' . $rename_table_name_old . '` ' . __( 'renamed to', 'wp-data-access' ) . ' `' . $rename_table_name_new . '` ',
                                ));
                                $msg->box();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Rename table/view.
     *
     * @param string $rename_table_name_old Old table name.
     * @param string $rename_table_name_new New table name.
     *
     * @return false|int
     * @since   1.6.6
     */
    protected function rename_table( $rename_table_name_old, $rename_table_name_new ) {
        if ( WPDA::is_wp_table( $rename_table_name_old ) ) {
            // Never ever allow renaming a WordPress table!
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized [cannot rename WordPress tables]', 'wp-data-access' ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        return $wpdadb->query( 'rename table `' . str_replace( '`', '', $rename_table_name_old ) . '` to `' . str_replace( '`', '', $rename_table_name_new ) . '`' );
        // db call ok; no-cache ok.
    }

    /**
     * Performs copying a table.
     *
     * @since   1.6.6
     */
    protected function process_bulk_action_copy_table() {
        // Check arguments.
        if ( !$this->process_bulk_action_check_action( 'copy_table_name_src', __( 'Missing source table name', 'wp-data-access' ) ) ) {
            return;
        }
        if ( $this->process_bulk_action_check_action( 'copy_table_name_dst', __( 'Missing destination table name', 'wp-data-access' ) ) ) {
            // copy table is not allowed for WordPress tables (double check).
            $copy_schema_name_src = sanitize_text_field( wp_unslash( $_REQUEST['copy_schema_name_src'] ) );
            // input var okay.
            $copy_table_name_src = sanitize_text_field( wp_unslash( $_REQUEST['copy_table_name_src'] ) );
            // input var okay.
            $copy_schema_name_dst = sanitize_text_field( wp_unslash( $_REQUEST['copy_schema_name_dst'] ) );
            // input var okay.
            $copy_table_name_dst = sanitize_text_field( wp_unslash( $_REQUEST['copy_table_name_dst'] ) );
            // input var okay.
            if ( '' === $copy_schema_name_src ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing source schema name', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            if ( '' === $copy_table_name_src ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing source table name', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            if ( '' === $copy_schema_name_dst ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing destination schema name', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            if ( '' === $copy_table_name_dst ) {
                $msg = new WPDA_Message_Box(array(
                    'message_text' => __( 'Missing destination table name', 'wp-data-access' ),
                ));
                $msg->box();
                return;
            }
            // Check if table exists.
            if ( $this->process_bulk_action_check_table_exists( $copy_table_name_src ) ) {
                // Check if copy is allowed.
                if ( $this->process_bulk_action_check_wpnonce( "wpda-copy-{$copy_table_name_src}", '_wpnonce' ) ) {
                    $dbo_type = $this->get_dbo_type( $copy_table_name_src );
                    if ( false === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                        $msg = new WPDA_Message_Box(array(
                            'message_text'           => sprintf( __( 'Cannot copy `%s`', 'wp-data-access' ), $copy_table_name_src ),
                            'message_type'           => 'error',
                            'message_is_dismissible' => false,
                        ));
                        $msg->box();
                    } else {
                        $include_data = ( isset( $_REQUEST['copy-table-data'] ) ? 'on' : 'off' );
                        // Copy table/view.
                        if ( false === $this->copy_table(
                            $copy_schema_name_src,
                            $copy_table_name_src,
                            $copy_schema_name_dst,
                            $copy_table_name_dst,
                            $include_data
                        ) ) {
                            $msg = new WPDA_Message_Box(array(
                                'message_text'           => __( 'Cannot copy', 'wp-data-access' ) . ' ' . strtolower( $dbo_type ) . ' ' . $copy_table_name_src,
                                'message_type'           => 'error',
                                'message_is_dismissible' => false,
                            ));
                            $msg->box();
                        } else {
                            $msg = new WPDA_Message_Box(array(
                                'message_text' => strtoupper( substr( $dbo_type, 0, 1 ) ) . strtolower( substr( $dbo_type, 1 ) ) . " `{$copy_schema_name_src}`.`{$copy_table_name_src}` " . __( 'copied to', 'wp-data-access' ) . " `{$copy_schema_name_dst}`.`{$copy_table_name_dst}` ",
                            ));
                            $msg->box();
                        }
                    }
                }
            }
        }
    }

    /**
     * Copy table.
     *
     * @param string $copy_schema_name_src Source schema name.
     * @param string $copy_table_name_src Source table name.
     * @param string $copy_schema_name_dst Destination schema name.
     * @param string $copy_table_name_dst Destination table name.
     * @param string $include_data 'on' = include data.
     *
     * @return false|int
     * @since   1.6.6
     */
    protected function copy_table(
        $copy_schema_name_src,
        $copy_table_name_src,
        $copy_schema_name_dst,
        $copy_table_name_dst,
        $include_data
    ) {
        $copy_schema_name_src = WPDA::remove_backticks( $copy_schema_name_src );
        $copy_table_name_src = WPDA::remove_backticks( $copy_table_name_src );
        $copy_schema_name_dst = WPDA::remove_backticks( $copy_schema_name_dst );
        $copy_table_name_dst = WPDA::remove_backticks( $copy_table_name_dst );
        $wpdadb_src = WPDADB::get_db_connection( $copy_schema_name_src );
        if ( null === $wpdadb_src ) {
            return false;
        }
        $wpdadb_dst = WPDADB::get_db_connection( $copy_schema_name_dst );
        if ( null === $wpdadb_dst ) {
            return false;
        }
        $suppress_wpdadb_src = $wpdadb_src->suppress_errors;
        $wpdadb_src->suppress_errors = true;
        $suppress_wpdadb_dst = $wpdadb_dst->suppress_errors;
        $wpdadb_dst->suppress_errors = true;
        // Get create table statement from $wpdadb_src.
        // NO_TABLE_OPTIONS is deprecated in V8
        // $wpdadb_src->query( "SET sql_mode = 'NO_TABLE_OPTIONS'" );
        $query = "show create table `{$copy_table_name_src}`";
        $ctcmd = $wpdadb_src->get_results( $query, 'ARRAY_A' );
        // phpcs:ignore Standard.Category.SniffName.ErrorCode
        if ( '' !== $wpdadb_src->last_error || !isset( $ctcmd[0]['Create Table'] ) ) {
            $wpdadb_src->suppress_errors( $suppress_wpdadb_src );
            $wpdadb_dst->suppress_errors( $suppress_wpdadb_dst );
            return false;
        }
        $create_table_statement = $ctcmd[0]['Create Table'];
        if ( $copy_table_name_src !== $copy_table_name_dst ) {
            // Modify create table statement
            $pos = strpos( $create_table_statement, $copy_table_name_src );
            if ( $pos !== false ) {
                $create_table_statement = substr_replace(
                    $create_table_statement,
                    $copy_table_name_dst,
                    $pos,
                    strlen( $copy_table_name_src )
                );
            }
        }
        // Create new table in $wpdadb_dst
        $wpdadb_dst->query( $create_table_statement );
        if ( '' !== $wpdadb_dst->last_error ) {
            $wpdadb_src->suppress_errors( $suppress_wpdadb_src );
            $wpdadb_dst->suppress_errors( $suppress_wpdadb_dst );
            return false;
        }
        if ( 'on' !== $include_data ) {
            return true;
        }
        if ( $copy_schema_name_src === $copy_schema_name_dst ) {
            // No need for buffering if source database === destination database
            $result = $wpdadb_dst->query( "insert `{$copy_table_name_dst}` select * from `{$copy_table_name_src}`" );
            // db call ok; no-cache ok.
            $wpdadb_src->suppress_errors( $suppress_wpdadb_src );
            $wpdadb_dst->suppress_errors( $suppress_wpdadb_dst );
            return $result;
        }
        // Check if buffering is needed for this table
        $settings_db = WPDA_Table_Settings_Model::query( $copy_table_name_src, $copy_schema_name_src );
        if ( isset( $settings_db[0]['wpda_table_settings'] ) ) {
            $settings_db_custom = json_decode( $settings_db[0]['wpda_table_settings'] );
            if ( isset( $settings_db_custom->table_settings->query_buffer_size ) ) {
                $query_buffer_size = $settings_db_custom->table_settings->query_buffer_size;
            } else {
                $query_buffer_size = 0;
            }
        } else {
            $query_buffer_size = 0;
        }
        // Copy table data from $wpdadb to $wpdb
        $query = "select * from `{$copy_table_name_src}`";
        if ( is_numeric( $query_buffer_size ) && $query_buffer_size > 0 ) {
            set_time_limit( 0 );
            ?>
				<p class="wpda_pds_msg_create">
					Copy table `<?php 
            echo esc_attr( $copy_schema_name_src );
            ?>`.`<?php 
            echo esc_attr( $copy_table_name_src );
            ?>`
				</p>
				<p class="wpda_pds_msg_create">
					To `<?php 
            echo esc_attr( $copy_schema_name_dst );
            ?>`.`<?php 
            echo esc_attr( $copy_table_name_dst );
            ?>`
				</p>
				<p class="wpda_pds_msg_create">
					Hang on...
				</p>
				<p class="wpda_pds_msg_create">
					Table `<?php 
            echo esc_attr( $copy_table_name_dst );
            ?>` created...
				</p>
				<p class="wpda_pds_msg_create">
					Copying data...
				</p>
				<?php 
            ob_flush();
            flush();
            // Create array for fast column_name based access.
            $wpda_list_columns = WPDA_List_Columns_Cache::get_list_columns( $copy_schema_name_src, $copy_table_name_src );
            $table_columns = $wpda_list_columns->get_table_columns();
            $column_data_types = array();
            foreach ( $table_columns as $column_value ) {
                $column_data_types[$column_value['column_name']] = $column_value['data_type'];
            }
            $i = 0;
            $total = 0;
            $sql = $query . ' limit ' . $query_buffer_size;
            $rows = $wpdadb_src->get_results( $sql, 'ARRAY_A' );
            // phpcs:ignore Standard.Category.SniffName.ErrorCode
            while ( $wpdadb_src->num_rows > 0 ) {
                $total += $wpdadb_src->num_rows;
                foreach ( $rows as $row ) {
                    $wpdadb_dst->insert( $copy_table_name_dst, $row );
                    $wpdadb_dst->flush();
                    $wpdadb_dst->queries = null;
                }
                echo '<script>jQuery("p.wpda_pds_msg").remove();</script>';
                echo '<p class="wpda_pds_msg">Copied ' . number_format( $total ) . ' rows...</p>';
                ob_flush();
                flush();
                $i++;
                $wpdadb_src->flush();
                $wpdadb_src->queries = null;
                $sql = $query . ' limit ' . $query_buffer_size . ' offset ' . $i * $query_buffer_size;
                $rows = $wpdadb_src->get_results( $sql, 'ARRAY_A' );
                // phpcs:ignore Standard.Category.SniffName.ErrorCode
            }
            echo '<script>jQuery("p.wpda_pds_msg_create, p.wpda_pds_msg").css("display", "none");</script>';
        } else {
            $rows = $wpdadb_src->get_results( $query, 'ARRAY_A' );
            // phpcs:ignore Standard.Category.SniffName.ErrorCode
            foreach ( $rows as $row ) {
                $wpdadb_dst->insert( $copy_table_name_dst, $row );
            }
        }
        $wpdadb_src->suppress_errors( $suppress_wpdadb_src );
        $wpdadb_dst->suppress_errors( $suppress_wpdadb_dst );
        return true;
    }

    /**
     * Performs bulk export.
     *
     * @since   1.5.0
     */
    protected function process_bulk_action_bulk_export() {
        // Check is there is anything to export.
        if ( $this->process_bulk_action_check_action( 'bulk-selected', __( 'Empty bulk selected', 'wp-data-access' ) ) ) {
            // Check if export is allowed.
            if ( $this->process_bulk_action_check_wpnonce( 'wpda-export-' . json_encode( $this->table_name ), '_wpnonce' ) ) {
                // Get arguments.
                $bulk_tabs = ( isset( $_REQUEST['bulk-selected'] ) ? $_REQUEST['bulk-selected'] : '' );
                // input var okay; sanitization okay.
                $wp_nonce = wp_create_nonce( 'wpda-export-' . WPDA::get_current_user_login() );
                $cnt = 0;
                $selected_tables = array();
                foreach ( $bulk_tabs as $table_name ) {
                    $export_table_name = sanitize_text_field( wp_unslash( $table_name ) );
                    // input var okay.
                    $err_txt = ' ' . sprintf( __( '[table `%s`]', 'wp-data-access' ), $export_table_name );
                    if ( $this->process_bulk_action_check_table_exists( $export_table_name, $err_txt ) ) {
                        $dbo_type = $this->get_dbo_type( $export_table_name );
                        if ( false === $dbo_type || 'VIEW' === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                            $msg = new WPDA_Message_Box(array(
                                'message_text'           => sprintf( __( 'Cannot export `%s`', 'wp-data-access' ), $export_table_name ),
                                'message_type'           => 'error',
                                'message_is_dismissible' => false,
                            ));
                            $msg->box();
                        } else {
                            $selected_tables[] = $export_table_name;
                            $cnt++;
                        }
                    }
                }
                if ( 0 < $cnt ) {
                    ?>
						<script type="application/javascript">
							jQuery(function() {
								wpda_main_export(
									'<?php 
                    echo esc_attr( $this->schema_name );
                    ?>',
									'<?php 
                    echo esc_attr( $wp_nonce );
                    ?>',
									'<?php 
                    echo json_encode( $selected_tables );
                    ?>'
								);
							});
						</script>
						<?php 
                }
            }
        }
    }

    /**
     * Checks request argument needed for (bulk) action to be performed.
     *
     * @param string $argument_name Request argument name.
     * @param string $msg Message on failure.
     *
     * @return bool TRUE = argument found in request.
     * @since   1.5.0
     */
    protected function process_bulk_action_check_action( $argument_name, $msg ) {
        if ( !isset( $_REQUEST[$argument_name] ) ) {
            // Nothing export.
            $msg = new WPDA_Message_Box(array(
                'message_text' => $msg,
            ));
            $msg->box();
            return false;
        }
        return true;
    }

    /**
     * Checks wpnonce against a specific action.
     *
     * @param string $wp_nonce_action Nonce action.
     * @param string $wp_nonce_arg Nonce argument.
     *
     * @return bool TRUE = action allowed.
     * @since   1.5.0
     */
    protected function process_bulk_action_check_wpnonce( $wp_nonce_action, $wp_nonce_arg ) {
        $wp_nonce = ( isset( $_REQUEST[$wp_nonce_arg] ) ? sanitize_text_field( wp_unslash( $_REQUEST[$wp_nonce_arg] ) ) : '' );
        // input var okay.
        if ( !wp_verify_nonce( $wp_nonce, $wp_nonce_action ) ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized', 'wp-data-access' ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        return true;
    }

    /**
     * Checks if table exists.
     *
     * @param string $table_name Database table name.
     * @param string $err_txt Additional error text/info.
     *
     * @return bool TRUE = table exists.
     * @since   1.5.0
     */
    protected function process_bulk_action_check_table_exists( $table_name, $err_txt = '' ) {
        $wpda_dictionary = new WPDA_Dictionary_Exist($this->schema_name, $table_name);
        if ( !$wpda_dictionary->table_exists() ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized [table not found]', 'wp-data-access' ) . $err_txt,
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        return true;
    }

    /**
     * Get database object type (VIEW, BASE_TABLE, SYSTEM VIEW).
     *
     * @param string $dbo_name Table or view name.
     *
     * @return string|boolean Database object type or false.
     * @since   1.5.0
     */
    protected function get_dbo_type( $dbo_name ) {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            return false;
        }
        $query = $wpdadb->prepare( '
							SELECT table_type AS table_type
							  FROM information_schema.tables
							 WHERE table_schema = %s
							   AND table_name   = %s
						', array($wpdadb->dbname, $dbo_name) );
        // db call ok; no-cache ok.
        $result = $wpdadb->get_results( $query, 'ARRAY_A' );
        // phpcs:ignore Standard.Category.SniffName.ErrorCode
        if ( 1 === $wpdadb->num_rows ) {
            return $result[0]['table_type'];
        } else {
            return false;
        }
    }

    /**
     * Performs a drop table or view
     *
     * Method does not drop WordPress tables.
     */
    protected function process_bulk_action_bulk_drop() {
        // Check is there is anything to drop.
        if ( $this->process_bulk_action_check_action( 'bulk-selected', __( 'Empty bulk selected', 'wp-data-access' ) ) ) {
            // Check if drop is allowed.
            if ( $this->process_bulk_action_check_wpnonce( 'wpda-drop-' . WPDA::get_current_user_login(), '_wpnonce3' ) ) {
                $bulk_tabs = ( isset( $_REQUEST['bulk-selected'] ) ? $_REQUEST['bulk-selected'] : '' );
                // input var okay; sanitization okay.
                foreach ( $bulk_tabs as $table_name ) {
                    // Drop table is not allowed for WordPress tables (double check).
                    $drop_table_name = sanitize_text_field( wp_unslash( $table_name ) );
                    // input var okay.
                    $err_txt = ' ' . sprintf( __( '[cannot drop WordPress table `%s`]', 'wp-data-access' ), $drop_table_name );
                    if ( $this->process_bulk_action_check_is_wp_table( $drop_table_name, $err_txt ) ) {
                        // Check if table exists.
                        $err_txt = ' ' . sprintf( __( '[table `%s`]', 'wp-data-access' ), $drop_table_name );
                        if ( $this->process_bulk_action_check_table_exists( $drop_table_name, $err_txt ) ) {
                            $dbo_type = $this->get_dbo_type( $drop_table_name );
                            if ( false === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                                $msg = new WPDA_Message_Box(array(
                                    'message_text'           => sprintf( __( 'Cannot drop `%s`', 'wp-data-access' ), $drop_table_name ),
                                    'message_type'           => 'error',
                                    'message_is_dismissible' => false,
                                ));
                                $msg->box();
                            } else {
                                if ( 'VIEW' === $dbo_type ) {
                                    // Drop view.
                                    if ( $this->drop_view( $drop_table_name ) ) {
                                        $msg = new WPDA_Message_Box(array(
                                            'message_text' => sprintf( __( 'View `%s` dropped', 'wp-data-access' ), $drop_table_name ),
                                        ));
                                        $msg->box();
                                    }
                                } else {
                                    // Drop table.
                                    if ( $this->drop_table( $drop_table_name ) ) {
                                        $msg = new WPDA_Message_Box(array(
                                            'message_text' => sprintf( __( 'Table `%s` dropped', 'wp-data-access' ), $drop_table_name ),
                                        ));
                                        $msg->box();
                                        $this->post_drop_table( $drop_table_name );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Silently delete table related info from repository (called after drop table)
     *
     * @param string $drop_table_name Database table name
     */
    protected function post_drop_table( $drop_table_name ) {
        global $wpdb;
        $suppress = $wpdb->suppress_errors( true );
        // Table settings...
        $wpdb->query( $wpdb->prepare( 
            'delete from `%1s` where wpda_schema_name = %s and wpda_table_name = %s ',
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
            array(WPDA::remove_backticks( WPDA_Table_Settings_Model::get_base_table_name() ), $this->schema_name, $drop_table_name)
         ) );
        // WordPress media library columns...
        $wpdb->query( $wpdb->prepare( 
            'delete from `%1s` where media_schema_name = %s and media_table_name = %s ',
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
            array(WPDA::remove_backticks( WPDA_Media_Model::get_base_table_name() ), $this->schema_name, $drop_table_name)
         ) );
        // Data menus...
        $wpdb->query( $wpdb->prepare( 
            'delete from `%1s` where menu_schema_name = %s and menu_table_name = %s ',
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
            array(WPDA::remove_backticks( WPDA_User_Menus_Model::get_base_table_name() ), $this->schema_name, $drop_table_name)
         ) );
        $wpdb->suppress_errors( $suppress );
    }

    /**
     * Checks if table is a WordPress table.
     *
     * @param string $table_name Database table name.
     * @param string $err_txt Additional error text/info.
     *
     * @return bool TRUE = table is WordPress table.
     * @since   1.5.0
     */
    protected function process_bulk_action_check_is_wp_table( $table_name, $err_txt = '' ) {
        if ( WPDA::is_wp_table( $table_name ) ) {
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized', 'wp-data-access' ) . $err_txt,
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        return true;
    }

    /**
     * Performs drop view.
     *
     * @param string $view_name Database view name.
     *
     * @return bool TRUE = view dropped.
     * @since   1.5.0
     */
    protected function drop_view( $view_name ) {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        return $wpdadb->query( 'drop view `' . str_replace( '`', '', $view_name ) . '`' );
        // db call ok; no-cache ok.
    }

    /**
     * Performs drop table.
     *
     * @param string $table_name Database table name.
     *
     * @return bool TRUE = table dropped.
     * @since   1.5.0
     */
    protected function drop_table( $table_name ) {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        if ( WPDA::is_wp_table( $table_name ) ) {
            // Never ever allow dropping a WordPress table!
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized [cannot drop WordPress tables]', 'wp-data-access' ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        return $wpdadb->query( 'drop table `' . str_replace( '`', '', $table_name ) . '`' );
        // db call ok; no-cache ok.
    }

    /**
     * Performs a truncate table
     *
     * Method does not truncate WordPress tables.
     */
    protected function process_bulk_action_bulk_truncate() {
        // Check is there is anything to truncate.
        if ( $this->process_bulk_action_check_action( 'bulk-selected', __( 'No table defined', 'wp-data-access' ) ) ) {
            // Check if truncate is allowed.
            if ( $this->process_bulk_action_check_wpnonce( 'wpda-truncate-' . WPDA::get_current_user_login(), '_wpnonce4' ) ) {
                $bulk_tabs = ( isset( $_REQUEST['bulk-selected'] ) ? $_REQUEST['bulk-selected'] : '' );
                // input var okay; sanitization okay.
                foreach ( $bulk_tabs as $table_name ) {
                    // Truncate table is not allowed for WordPress tables (double check).
                    $truncate_table_name = sanitize_text_field( wp_unslash( $table_name ) );
                    // input var okay.
                    $err_txt = ' ' . sprintf( __( '[cannot truncate WordPress table `%s`]', 'wp-data-access' ), $truncate_table_name );
                    if ( $this->process_bulk_action_check_is_wp_table( $truncate_table_name, $err_txt ) ) {
                        // Check if table exists.
                        $err_txt = ' ' . sprintf( __( '[table `%s`]', 'wp-data-access' ), $truncate_table_name );
                        if ( $this->process_bulk_action_check_table_exists( $truncate_table_name, $err_txt ) ) {
                            $dbo_type = $this->get_dbo_type( $truncate_table_name );
                            if ( false === $dbo_type || 'VIEW' === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                                $msg = new WPDA_Message_Box(array(
                                    'message_text'           => sprintf( __( 'Cannot truncate `%s`', 'wp-data-access' ), $truncate_table_name ),
                                    'message_type'           => 'error',
                                    'message_is_dismissible' => false,
                                ));
                                $msg->box();
                            } else {
                                // Truncate table.
                                if ( $this->truncate_table( $truncate_table_name ) ) {
                                    $msg = new WPDA_Message_Box(array(
                                        'message_text' => sprintf( __( 'Table `%s` truncated', 'wp-data-access' ), $truncate_table_name ),
                                    ));
                                    $msg->box();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Performs truncate table.
     *
     * @param string $table_name Database table name.
     *
     * @return bool TRUE = table truncated.
     * @since   1.5.0
     */
    protected function truncate_table( $table_name ) {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        if ( WPDA::is_wp_table( $table_name ) ) {
            // Never ever allow truncating a WordPress table!
            $msg = new WPDA_Message_Box(array(
                'message_text'           => __( 'Not authorized [cannot truncate WordPress tables]', 'wp-data-access' ),
                'message_type'           => 'error',
                'message_is_dismissible' => false,
            ));
            $msg->box();
            return false;
        }
        return $wpdadb->query( $wpdadb->prepare( 
            'truncate table `%1s`',
            // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders
            [WPDA::remove_backticks( $table_name )]
         ) );
        // db call ok; no-cache ok.
    }

    /**
     * Processes drop table request.
     *
     * @since   1.5.0
     */
    protected function process_bulk_action_drop() {
        // Check is there is anything to drop.
        if ( $this->process_bulk_action_check_action( 'drop_table_name', __( 'No table defined', 'wp-data-access' ) ) ) {
            // Drop table is not allowed for WordPress tables (double check).
            $drop_table_name = sanitize_text_field( wp_unslash( $_REQUEST['drop_table_name'] ) );
            // input var okay.
            $err_txt = ' ' . sprintf( __( '[cannot drop WordPress table `%s`]', 'wp-data-access' ), $drop_table_name );
            if ( $this->process_bulk_action_check_is_wp_table( $drop_table_name, $err_txt ) ) {
                // Check if table exists.
                if ( $this->process_bulk_action_check_table_exists( $drop_table_name ) ) {
                    // Check if drop is allowed.
                    if ( $this->process_bulk_action_check_wpnonce( "wpda-drop-{$drop_table_name}", '_wpnonce' ) ) {
                        $dbo_type = $this->get_dbo_type( $drop_table_name );
                        if ( false === $dbo_type || 'SYSTEM VIEW' === $dbo_type ) {
                            $msg = new WPDA_Message_Box(array(
                                'message_text'           => sprintf( __( 'Cannot drop `%s`', 'wp-data-access' ), $drop_table_name ),
                                'message_type'           => 'error',
                                'message_is_dismissible' => false,
                            ));
                            $msg->box();
                        } else {
                            if ( 'VIEW' === $dbo_type ) {
                                // Drop view.
                                if ( $this->drop_view( $drop_table_name ) ) {
                                    $msg = new WPDA_Message_Box(array(
                                        'message_text' => sprintf( __( 'View `%s` dropped', 'wp-data-access' ), $drop_table_name ),
                                    ));
                                    $msg->box();
                                }
                            } else {
                                // Drop table.
                                if ( $this->drop_table( $drop_table_name ) ) {
                                    $msg = new WPDA_Message_Box(array(
                                        'message_text' => sprintf( __( 'Table `%s` dropped', 'wp-data-access' ), $drop_table_name ),
                                    ));
                                    $msg->box();
                                    $this->post_drop_table( $drop_table_name );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Processes truncate table request.
     *
     * @since   1.5.0
     */
    protected function process_bulk_action_truncate() {
        // Check is there is anything to truncate.
        if ( $this->process_bulk_action_check_action( 'truncate_table_name', __( 'No table defined', 'wp-data-access' ) ) ) {
            // Truncate table is not allowed for WordPress tables (double check).
            $truncate_table_name = sanitize_text_field( wp_unslash( $_REQUEST['truncate_table_name'] ) );
            // input var okay.
            $err_txt = ' ' . sprintf( __( '[cannot truncate WordPress table `%s`]', 'wp-data-access' ), $truncate_table_name );
            if ( $this->process_bulk_action_check_is_wp_table( $truncate_table_name, $err_txt ) ) {
                // Check if table exists.
                if ( $this->process_bulk_action_check_table_exists( $truncate_table_name ) ) {
                    // Check if truncate is allowed.
                    if ( $this->process_bulk_action_check_wpnonce( "wpda-truncate-{$truncate_table_name}", '_wpnonce' ) ) {
                        // Truncate table.
                        if ( $this->truncate_table( $truncate_table_name ) ) {
                            $msg = new WPDA_Message_Box(array(
                                'message_text' => sprintf( __( 'Table `%s` truncated', 'wp-data-access' ), $truncate_table_name ),
                            ));
                            $msg->box();
                        }
                    }
                }
            }
        }
    }

    protected function add_header_actions() {
        $this->add_db_containers();
    }

    /**
     * Overwrite method: add bulk array to check ddl allowed
     */
    public function show() {
        ?>
			<script type='text/javascript'>
				var wpda_bulk = [];

				function wpda_bulk_valid() {
					var wpda_bulk_selected_valid = true;
					jQuery("input[name='bulk-selected[]']:checked").each(function () {
						var wpda_bulk_selected = jQuery(this).val();
						wpda_bulk.every(function (item) {
							if (item === wpda_bulk_selected) {
								alert("Action not allowed on WordPress tables!");
								wpda_bulk_selected_valid = false;
							}
						});
						if (wpda_bulk_selected_valid === false) {
							return false;
						}
					});
					return wpda_bulk_selected_valid;
				}

				function wpda_check_bulk() {
					action = jQuery("select[name='action']").val();
					action2 = jQuery("select[name='action2']").val();
					if (action === '-1') {
						if (action2 === 'bulk-drop' || action2 === 'bulk-truncate') {
							return wpda_bulk_valid();
						} else {
							return true;
						}
					} else {
						if (action === 'bulk-drop' || action === 'bulk-truncate') {
							return wpda_bulk_valid();
						} else {
							return true;
						}
					}
				}
			</script>
			<?php 
        parent::show();
        ?>
			<script type='text/javascript'>
				function show_hide_column(show) {
					for (i = 0; i <<?php 
        echo esc_attr( self::$list_number );
        ?>; i++) {
						if (show) {
							jQuery('#rownum_' + i + '_x1').attr('colspan', parseInt(jQuery('#rownum_' + i + '_x1').attr('colspan')) + 1);
							jQuery('#rownum_' + i + '_x2').attr('colspan', parseInt(jQuery('#rownum_' + i + '_x2').attr('colspan')) + 1);
						} else {
							jQuery('#rownum_' + i + '_x1').attr('colspan', parseInt(jQuery('#rownum_' + i + '_x1').attr('colspan')) - 1);
							jQuery('#rownum_' + i + '_x2').attr('colspan', parseInt(jQuery('#rownum_' + i + '_x2').attr('colspan')) - 1);
						}
					}
					jQuery('.wp-list-table').removeClass('fixed');
				}

				jQuery(function () {
					jQuery("#doaction").on("click", function (e) {
						return wpda_check_bulk();
					});
					jQuery("#doaction2").on("click", function (e) {
						return wpda_check_bulk();
					});
					jQuery('#table_name-hide').on("click", function (e) {
						show_hide_column(jQuery('#table_name-hide').is(":checked"));
					});
					jQuery('#table_type-hide').on("click", function (e) {
						show_hide_column(jQuery('#table_type-hide').is(":checked"));
					});
					jQuery('#create_time-hide').on("click", function (e) {
						show_hide_column(jQuery('#create_time-hide').is(":checked"));
					});
					jQuery('#table_rows-hide').on("click", function (e) {
						show_hide_column(jQuery('#table_rows-hide').is(":checked"));
					});
					jQuery('#auto_increment-hide').on("click", function (e) {
						show_hide_column(jQuery('#auto_increment-hide').is(":checked"));
					});
					jQuery('#engine-hide').on("click", function (e) {
						show_hide_column(jQuery('#engine-hide').is(":checked"));
					});
					jQuery('#total_size-hide').on("click", function (e) {
						show_hide_column(jQuery('#total_size-hide').is(":checked"));
					});
					jQuery('#data_size-hide').on("click", function (e) {
						show_hide_column(jQuery('#data_size-hide').is(":checked"));
					});
					jQuery('#index_size-hide').on("click", function (e) {
						show_hide_column(jQuery('#index_size-hide').is(":checked"));
					});
					jQuery('#overhead-hide').on("click", function (e) {
						show_hide_column(jQuery('#overhead-hide').is(":checked"));
					});
					jQuery('#table_collation-hide').on("click", function (e) {
						show_hide_column(jQuery('#table_collation-hide').is(":checked"));
					});
				});
			</script>
			<?php 
    }

    /**
     * Overwrite construct_where_clause()
     *
     * @since   1.0.0
     *
     * @see WPDA_List_Table::construct_where_clause()
     */
    protected function construct_where_clause() {
        $wpdadb = WPDADB::get_db_connection( $this->schema_name );
        if ( null === $wpdadb ) {
            wp_die( sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $this->schema_name ) ) );
        }
        global $wpdb;
        // Make sure we're selecting only tables that are in the WordPress database.
        $where_or_and = ( '' === $this->where ? ' where ' : ' and ' );
        $this->where .= $wpdadb->prepare( " {$where_or_and} table_schema = %s ", $wpdadb->dbname );
        // Since we are using a view, the default behaviour of the parent will not work for us. We have to
        // define our where clause manually.
        $cols = array(array(
            'column_name'    => 'table_name',
            'data_type'      => 'varchar',
            'extra'          => '',
            'column_type'    => 'varchar(64)',
            'is_nullable'    => 'NO',
            'column_default' => null,
        ));
        $where = WPDA::construct_where_clause(
            $this->schema_name,
            $this->table_name,
            $cols,
            $this->search_value
        );
        if ( '' !== $where ) {
            $this->where .= ' and ' . $where;
        }
        if ( $wpdb->dbname === $this->schema_name || '' === $this->schema_name ) {
            $table_access = WPDA::get_option( WPDA::OPTION_BE_TABLE_ACCESS );
        } else {
            $table_access = get_option( WPDA::BACKEND_OPTIONNAME_DATABASE_ACCESS . $this->schema_name );
            if ( false === $table_access ) {
                $table_access = 'show';
            }
        }
        if ( 'hide' === $table_access ) {
            // No access to WordPress tables: filter WordPress table.
            $this->where .= " and table_name not in ('" . implode( "','", $wpdb->tables( 'all', true ) ) . "')";
        } elseif ( 'select' === $table_access ) {
            if ( $wpdb->dbname === $this->schema_name || '' === $this->schema_name ) {
                $option = WPDA::get_option( WPDA::OPTION_BE_TABLE_ACCESS_SELECTED );
            } else {
                $option = get_option( WPDA::BACKEND_OPTIONNAME_DATABASE_SELECTED . $this->schema_name );
                if ( false === $option ) {
                    $option = '';
                }
            }
            if ( '' !== $option ) {
                // Allow only access to selected tables.
                $this->where .= " and table_name in ('" . implode( "','", $option ) . "')";
            } else {
                // No tables selected: no access.
                $this->where .= ' and 1=2';
            }
        }
        if ( null != $this->wpda_main_favourites ) {
            if ( false === $this->favourites && 'show' === $this->wpda_main_favourites ) {
                $where_or_and = ( '' === $this->where ? ' where ' : ' and ' );
                $this->where .= " {$where_or_and} 1=2 ";
            } elseif ( is_array( $this->favourites ) ) {
                if ( 0 < count( $this->favourites ) ) {
                    //phpcs:ignore - 8.1 proof
                    $where_or_and = ( '' === $this->where ? ' where ' : ' and ' );
                    $in_or_not_in = ( 'show' === $this->wpda_main_favourites ? 'in' : 'not in' );
                    $this->where .= " {$where_or_and} table_name {$in_or_not_in} ('" . implode( "','", $this->favourites ) . "') ";
                }
            }
        }
    }

    protected function get_order_by() {
        $orderby = parent::get_order_by();
        if ( '' !== $orderby ) {
            return $orderby;
        } else {
            return ' order by table_name asc ';
        }
    }

    /**
     * Overwrite method: add button to design a table
     */
    protected function add_header_button() {
        ?>
			<form
					method="post"
					action="?page=<?php 
        echo esc_attr( \WP_Data_Access_Admin::PAGE_DESIGNER );
        ?>"
					style="display: inline-block; vertical-align: baseline;"
			>
				<div>
					<input type="hidden" name="action" value="create_table">
					<input type="hidden" name="caller" value="dataexplorer">
					<button type="submit"
						   class="page-title-action wpda_tooltip"
						   title="Create a new table design"
					>
						<i class="fas fa-plus-circle wpda_icon_on_button"></i>
						<?php 
        echo __( 'Design new table', 'wp-data-access' );
        ?>
					</button>
				</div>
			</form>
			<form id="wpda_linkto_backup" style="display: none" method="post" action="?page=<?php 
        echo esc_attr( WP_Data_Access_Admin::PAGE_MAIN );
        ?>&page_action=wpda_backup">
				<input type="hidden" name="wpdaschema_name" value="<?php 
        echo esc_attr( $this->schema_name );
        ?>">
			</form>
			<?php 
        $this->wpda_import->add_button();
    }

    /**
     * Add container to create new database
     */
    protected function add_db_containers() {
        if ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ) {
            $rdb = WPDADB::get_remote_database( $this->schema_name );
        }
        $host = ( isset( $rdb['host'] ) ? $rdb['host'] : '' );
        $user = ( isset( $rdb['username'] ) ? $rdb['username'] : '' );
        $passwd = ( isset( $rdb['password'] ) ? $rdb['password'] : '' );
        $port = ( isset( $rdb['port'] ) ? $rdb['port'] : '' );
        $schema = ( isset( $rdb['database'] ) ? $rdb['database'] : '' );
        $ssl = ( isset( $rdb['ssl'] ) ? $rdb['ssl'] : '' );
        $ssl_key = ( isset( $rdb['ssl_key'] ) ? $rdb['ssl_key'] : '' );
        $ssl_cert = ( isset( $rdb['ssl_cert'] ) ? $rdb['ssl_cert'] : '' );
        $ssl_ca = ( isset( $rdb['ssl_ca'] ) ? $rdb['ssl_ca'] : '' );
        $ssl_path = ( isset( $rdb['ssl_path'] ) ? $rdb['ssl_path'] : '' );
        $ssl_cipher = ( isset( $rdb['ssl_cipher'] ) ? $rdb['ssl_cipher'] : '' );
        $readonly = '';
        ?>
			<div id="wpda_db_edit" style="display:none;padding-top:10px;">
				<div class="wpda_upload">
					<form method="post" action="?page=<?php 
        echo esc_attr( $this->page );
        ?>" onsubmit="return editdb_validate_form();">
						<div style="height:10px;"></div>
						<strong><?php 
        echo __( 'Edit Remote Database Connection', 'wp-data-access' );
        ?></strong>
						<div style="height:10px;"></div>
						<div>
							<label for="edit_remote_database" style="vertical-align:baseline;"
								   class="database_item_label">Database name:</label>
							<input type="text" name="edit_remote_database" id="edit_remote_database" value="<?php 
        echo esc_attr( $this->schema_name );
        ?>" <?php 
        echo esc_attr( $readonly );
        ?>>
							<input type="hidden" name="edit_remote_database_old" value="<?php 
        echo esc_attr( $this->schema_name );
        ?>">
							<div style="height:10px;"></div>
							<label for="edit_remote_host" style="vertical-align:baseline;" class="database_item_label">MySQL host:</label>
							<input type="text" name="edit_remote_host" id="edit_remote_host" value="<?php 
        echo esc_attr( $host );
        ?>">
							<br/>
							<label for="edit_remote_user" style="vertical-align:baseline;" class="database_item_label">MySQL username:</label>
							<input type="text" name="edit_remote_user" id="edit_remote_user" value="<?php 
        echo esc_attr( $user );
        ?>">
							<br/>
							<label for="edit_remote_passwd" style="vertical-align:baseline;" class="database_item_label" >MySQL password:</label>
							<input type="password" name="edit_remote_passwd" id="edit_remote_passwd" value="<?php 
        echo esc_attr( $passwd );
        ?>" autocomplete="new-password">
							<i class="fas fa-eye" onclick="wpda_toggle_password('edit_remote_passwd', event)" style="cursor:pointer"></i>
							<br/>
							<label for="edit_remote_port" style="vertical-align:baseline;" class="database_item_label">MySQL port:</label>
							<input type="text" name="edit_remote_port" id="edit_remote_port" value="<?php 
        echo esc_attr( $port );
        ?>">
							<br/>
							<label for="edit_remote_schema" style="vertical-align:baseline;" class="database_item_label">MySQL schema:</label>
							<input type="text" name="edit_remote_schema" id="edit_remote_schema" value="<?php 
        echo esc_attr( $schema );
        ?>">

							<br/>
							<label style="line-height:30px;" for="edit_remote_ssl" style="vertical-align:baseline;" class="database_item_label">SSL:</label>
							<input type="checkbox" name="edit_remote_ssl" id="edit_remote_ssl" <?php 
        echo ( 'on' === $ssl ? 'checked' : 'unchecked' );
        ?> onclick="jQuery('#edit_remote_database_block_ssl').toggle()">
							<div id="edit_remote_database_block_ssl" <?php 
        echo ( 'on' === $ssl ? '' : 'style="display:none;"' );
        ?>>
								<label for="edit_remote_client_key" style="vertical-align:baseline;" class="database_item_label">Client key:</label>
								<input type="text" name="edit_remote_client_key" id="edit_remote_client_key" value="<?php 
        echo esc_attr( $ssl_key );
        ?>">
								<br/>
								<label for="edit_remote_client_certificate" style="vertical-align:baseline;" class="database_item_label">Client certificate:</label>
								<input type="text" name="edit_remote_client_certificate" id="edit_remote_client_certificate" value="<?php 
        echo esc_attr( $ssl_cert );
        ?>">
								<br/>
								<label for="edit_remote_ca_certificate" style="vertical-align:baseline;" class="database_item_label">CA certificate:</label>
								<input type="text" name="edit_remote_ca_certificate" id="edit_remote_ca_certificate" value="<?php 
        echo esc_attr( $ssl_ca );
        ?>">
								<br/>
								<label for="edit_remote_certificate_path" style="vertical-align:baseline;" class="database_item_label">Certificate path:</label>
								<input type="text" name="edit_remote_certificate_path" id="edit_remote_certificate_path" value="<?php 
        echo esc_attr( $ssl_path );
        ?>">
								<br/>
								<label for="edit_remote_specified_cipher" style="vertical-align:baseline;" class="database_item_label">Specified Cipher:</label>
								<input type="text" name="edit_remote_specified_cipher" id="edit_remote_specified_cipher" value="<?php 
        echo esc_attr( $ssl_cipher );
        ?>">
							</div>

							<div style="height:10px;"></div>
							<label class="database_item_label"></label>
							<input type="button" value="Test" onclick="test_remote_connection('edit_'); return false;"
								   id="edit_remote_test_button" class="button">
							<input type="button" value="Clear" onclick="test_remote_clear('edit_'); return false;"
								   id="edit_remote_clear_button" class="button" style="display:none;">
							<div style="height:10px;"></div>
						</div>
						<div id="edit_remote_database_block_test" style="display:none;">
							<div id="edit_remote_database_block_test_content"
								 class="remote_database_block_test_content"></div>
							<div style="height:10px;"></div>
						</div>

						<input type="submit" class="button button-primary" value="<?php 
        echo __( 'Save', 'wp-data-access' );
        ?>">
						<a href="javascript:void(0)"
						   onclick="jQuery('#wpda_db_edit').hide()"
						   class="button button-secondary"><i class="fas fa-times-circle wpda_icon_on_button"></i> <?php 
        echo __( 'Cancel', 'wp-data-access' );
        ?></a>
						<input type="hidden" name="action" value="edit_db">
						<?php 
        wp_nonce_field( 'wpda-edit-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnonceeditdb', false );
        ?>
					</form>
				</div>
			</div>

			<div id="wpda_db_container" style="display:none;padding-top:10px;">
				<div class="wpda_upload">
					<form method="post" action="?page=<?php 
        echo esc_attr( $this->page );
        ?>" onsubmit="return createdb_validate_form();">
						<div style="height:10px;"></div>

						<?php 
        if ( $this->user_can_create_db ) {
            ?>
							<select name="database_location" id="database_location">
								<option value="local"
										selected><?php 
            echo __( 'Create local database', 'wp-data-access' );
            ?></option>
								<option value="remote"><?php 
            echo __( 'Add remote database connection', 'wp-data-access' );
            ?></option>
							</select>

							<div style="height:10px;"></div>

							<div id="local_database_block">
								<label for="local_database" style="vertical-align:baseline;"
									   class="database_item_label">Database name:</label>
								<input type="text" name="local_database" id="local_database">

								<div style="height:10px;"></div>
							</div>
							<?php 
        } else {
            ?>
							<strong><?php 
            echo __( 'Add remote database connection', 'wp-data-access' );
            ?></strong>

							<div style="height:10px;"></div>
							<?php 
        }
        ?>

						<div id="remote_database_block"<?php 
        echo ( $this->user_can_create_db ? 'style="display:none;"' : '' );
        ?>>
							<div>
								<label for="remote_database" style="vertical-align:baseline;"
									   class="database_item_label">Database name:</label>
								<input type="text" name="remote_database" id="remote_database" value="rdb:">
								<div style="height:10px;"></div>
								<label for="remote_host" style="vertical-align:baseline;" class="database_item_label">MySQL host:</label>
								<input type="text" name="remote_host" id="remote_host">
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
								<label style="line-height:30px;" for="remote_ssl" style="vertical-align:baseline;" class="database_item_label">SSL:</label>
								<input type="checkbox" name="remote_ssl" id="remote_ssl" unchecked onclick="jQuery('#remote_database_block_ssl').toggle()">
								<div id="remote_database_block_ssl" style="display:none;">
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
						</div>

						<a href="javascript:void(0)"
						   onclick="jQuery(this).closest('form').submit()"
						   class="button button-primary"><i class="fas fa-cloud-upload wpda_icon_on_button"></i> <?php 
        echo __( 'Save', 'wp-data-access' );
        ?></a>
						<a href="javascript:void(0)"
						   onclick="jQuery('#wpda_db_container').hide()"
						   class="button button-secondary"><i class="fas fa-times-circle wpda_icon_on_button"></i> <?php 
        echo __( 'Cancel', 'wp-data-access' );
        ?></a>
						<input type="hidden" name="action" value="create_db">
						<?php 
        wp_nonce_field( 'wpda-create-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnoncecreatedb', false );
        ?>
					</form>
				</div>
			</div>

			<div style="display:none;">
				<form id="wpda_form_drop_db" method="post" action="?page=<?php 
        echo esc_attr( $this->page );
        ?>">
					<input type="text" name="database" id="drop_database">
					<input type="hidden" name="action" value="drop_db">
					<?php 
        wp_nonce_field( 'wpda-drop-db-from-data-explorer-' . WPDA::get_current_user_login(), '_wpnoncedropdb', false );
        ?>
				</form>
			</div>

			<script type='text/javascript'>
				function editdb_validate_form() {
					if (jQuery('#edit_remote_database').val()==='' || jQuery('#edit_remote_database').val()==='rdb:') {
						alert('Database name must be entered');
						return false;
					}
					if (jQuery('#edit_remote_host').val()==='') {
						alert('MySQL host must be entered');
						return false;
					}
					if (jQuery('#edit_remote_user').val()==='') {
						alert('MySQL username must be entered');
						return false;
					}
					if (jQuery('#edit_remote_schema').val()==='') {
						alert('MySQL schema must be entered');
						return false;
					}
					return true;
				}

				function createdb_validate_form() {
					if (jQuery('#database_location').val() === 'remote') {
						if (jQuery('#remote_database').val()==='' || jQuery('#remote_database').val()==='rdb:') {
							alert('Database name must be entered');
							return false;
						}
						if (jQuery('#remote_host').val()==='') {
							alert('MySQL host must be entered');
							return false;
						}
						if (jQuery('#remote_user').val()==='') {
							alert('MySQL username must be entered');
							return false;
						}
						if (jQuery('#remote_schema').val()==='') {
							alert('MySQL schema must be entered');
							return false;
						}
					} else {

						if (jQuery('#local_database').val()==='') {
							alert('Database name must be entered');
							return false;
						}
					}
					return true;
				}

				function test_remote_clear(mode = '') {
					jQuery('#' + mode + 'remote_database_block_test_content').html('');
					jQuery('#' + mode + 'remote_database_block_test').hide();
					jQuery('#' + mode + 'remote_clear_button').hide();
				}

				function test_remote_connection(mode = '') {
					host = jQuery('#' + mode + 'remote_host').val();
					user = jQuery('#' + mode + 'remote_user').val();
					pass = jQuery('#' + mode + 'remote_passwd').val();
					port = jQuery('#' + mode + 'remote_port').val();
					dbs = jQuery('#' + mode + 'remote_schema').val();
					ssl = jQuery('#' + mode + 'remote_ssl').val();
					ssl_key = jQuery('#' + mode + 'remote_client_key').val();
					ssl_cert = jQuery('#' + mode + 'remote_client_certificate').val();
					ssl_ca = jQuery('#' + mode + 'remote_ca_certificate').val();
					ssl_path = jQuery('#' + mode + 'remote_certificate_path').val();
					ssl_cipher = jQuery('#' + mode + 'remote_specified_cipher').val();

					url = '//' + window.location.host + window.location.pathname +
						'?action=wpda_check_remote_database_connection';

					jQuery('#' + mode + 'remote_test_button').val('Testing...');

					jQuery.ajax({
						method: 'POST',
						url: url,
						data: {
							host: host,
							user: user,
							passwd: pass,
							port: port,
							schema: dbs,
							ssl: ssl,
							ssl_key: ssl_key,
							ssl_cert: ssl_cert,
							ssl_ca: ssl_ca,
							ssl_path: ssl_path,
							ssl_cipher: ssl_cipher
						}
					}).done(
						function (msg) {
							jQuery('#' + mode + 'remote_database_block_test_content').html(msg);
							jQuery('#' + mode + 'remote_database_block_test').show();
						}
					).fail(
						function () {
							jQuery('#' + mode + 'remote_database_block_test_content').html('Preparing connection...<br/>Establishing connection...<br/><br/><strong>Remote database connection invalid</strong>');
							jQuery('#' + mode + 'remote_database_block_test').show();
						}
					).always(
						function () {
							jQuery('#' + mode + 'remote_test_button').val('Test');
							jQuery('#' + mode + 'remote_clear_button').show();
						}
					);
				}

				jQuery(function () {
					jQuery('#database_location').on('change', function () {
						if (jQuery(this).val() === 'remote') {
							jQuery('#local_database_block').hide();
							jQuery('#remote_database_block').show();
						} else {
							jQuery('#remote_database_block').hide();
							jQuery('#local_database_block').show();
						}
					});
					jQuery('#remote_database, #edit_remote_database').keydown(function(e) {
						var field = this;
						setTimeout(function () {
							if (field.value.indexOf('rdb:') !== 0) {
								jQuery(field).val('rdb:');
							}
						}, 1);
					});
				});
			</script>
			<?php 
    }

    /**
     * Display the search box
     *
     * @param string $text The 'submit' button label.
     * @param string $input_id ID attribute value for the search input field.
     *
     * @since   1.6.0
     */
    public function search_box( $text, $input_id ) {
        global $wpdb;
        $input_id = $input_id . '-search-input';
        $schemas = WPDA_Dictionary_Lists::get_db_schemas();
        ?>
			<div style="padding-top:10px;padding-bottom:0;">
				<span style="font-weight: bold;"><?php 
        echo __( 'Database', 'wp-data-access' );
        ?>:</span>
				<select id="wpda_main_db_schema_list">
					<?php 
        foreach ( $schemas as $schema ) {
            if ( $this->schema_name === $schema['schema_name'] ) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            if ( $wpdb->dbname === $schema['schema_name'] ) {
                $printed_schema_name = "WordPress database ({$schema['schema_name']})";
            } else {
                $printed_schema_name = $schema['schema_name'];
            }
            ?>
						<option value="<?php 
            echo esc_attr( $schema['schema_name'] );
            ?>" <?php 
            echo esc_attr( $selected );
            ?>>
							<?php 
            echo esc_attr( $printed_schema_name );
            ?>
						</option>
						<?php 
        }
        ?>
				</select>
				<a class="dashicons dashicons-edit wpda_tooltip" href="javascript:void(0)"
				   <?php 
        if ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ) {
            ?>
					   onclick="jQuery('#wpda_db_edit').show();"
						<?php 
        }
        ?>
				   style="<?php 
        echo ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ? '' : 'color:grey;cursor:default;' );
        ?>vertical-align:middle;"
				   title="<?php 
        echo ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ? __( 'Edit remote database connection', 'wp-data-access' ) : __( 'Not available for local database', 'wp-data-access' ) );
        ?>">&nbsp;</a>
				<a class="dashicons dashicons-plus-alt wpda_tooltip" href="javascript:void(0)"
				   onclick="jQuery('#wpda_db_container').show(); jQuery('#local_database').focus();"
				   style="vertical-align:middle;"
				   title="<?php 
        echo esc_attr( $this->user_create_db_hint );
        ?>">&nbsp;</a>
				<a class="dashicons dashicons-dismiss wpda_tooltip" id="wpda_drop_database" href="javascript:void(0)"
					<?php 
        if ( $this->user_can_drop_db ) {
            if ( 'rdb:' === substr( $this->schema_name, 0, 4 ) ) {
                $msg = __( 'Delete remote database connection?\\nDoes not drop the database! Only deletes the remote database connection definition.', 'wp-data-access' );
            } else {
                $msg = __( 'Drop selected database?', 'wp-data-access' );
            }
            ?>
						onclick="if (confirm('<?php 
            echo $msg;
            // phpcs:ignore WordPress.Security.EscapeOutput
            ?>')) { jQuery('#drop_database').val(jQuery('#wpda_main_db_schema_list').val()); jQuery('#wpda_form_drop_db').submit(); }"
						<?php 
        }
        ?>
				   style="<?php 
        echo ( $this->user_can_drop_db ? 'color:#a00;' : 'color:grey;cursor:default;' );
        ?>vertical-align:middle;"
				   title="<?php 
        echo esc_attr( $this->user_drop_db_hint );
        ?>">&nbsp;</a>
				<a class="dashicons dashicons-admin-plugins wpda_tooltip"
				   href="javascript:void(0)"
				   onclick="wpda_dbinit_admin( '<?php 
        echo esc_attr( $this->schema_name );
        ?>', '<?php 
        echo wp_create_nonce( 'wpda_dbinit_admin_' . WPDA::get_current_user_login() );
        ?>' )"
				   style="vertical-align:middle;"
				   title="<?php 
        echo __( "Create function wpda_get_wp_user_id() to access the WordPress user ID from database views", 'wp-data-access' );
        ?>">&nbsp;</a>
				&nbsp;<span style="font-weight: bold;"><?php 
        echo __( 'Favourites', 'wp-data-access' );
        // phpcs:ignore WordPress.Security.EscapeOutput
        ?>:</span>
				<select id="wpda_main_favourites_list">
					<option value="" <?php 
        echo ( '' === $this->wpda_main_favourites ? 'selected' : '' );
        ?>>Show all
					</option>
					<option value="show" <?php 
        echo ( 'show' === $this->wpda_main_favourites ? 'selected' : '' );
        ?>>Show
						favourites only
					</option>
					<option value="hide" <?php 
        echo ( 'hide' === $this->wpda_main_favourites ? 'selected' : '' );
        ?>>Hide
						favourites
					</option>
				</select>
				<?php 
        if ( !(null === $this->search_value && !$this->has_items()) ) {
            ?>
					<p class="search-box">
						<input type="search" id="<?php 
            echo esc_attr( $input_id );
            ?>"
							   name="<?php 
            echo esc_attr( $this->search_item_name );
            ?>"
							   value="<?php 
            echo esc_attr( $this->search_value );
            ?>"/>
						<?php 
            submit_button(
                $text,
                '',
                '',
                false,
                array(
                    'id' => 'search-submit',
                )
            );
            ?>
						<input type="hidden" name="<?php 
            echo esc_attr( $this->search_item_name );
            ?>_old_value"
							   value="<?php 
            echo esc_attr( $this->search_value );
            ?>"/>
					</p>
					<?php 
        }
        ?>
			</div>
			<script type='text/javascript'>
				jQuery(function () {
					jQuery("#wpda_main_db_schema_list").on("change", function () {
						jQuery("#wpda_main_db_schema").val(jQuery(this).val());
						jQuery("#wpda_main_form :input[name='action']").val('-1');
						jQuery("#wpda_main_form :input[name='action2']").val('-1');
						jQuery("#wpda_main_form").submit();
					});
					jQuery("#wpda_main_favourites_list").on("change", function () {
						jQuery("#wpda_main_favourites").val(jQuery(this).val());
						jQuery("#wpda_main_form :input[name='action']").val('-1');
						jQuery("#wpda_main_form :input[name='action2']").val('-1');
						jQuery("#wpda_main_form").submit();
					});
				});
			</script>
			<?php 
    }

    /**
     * Get schema name from cookie or list
     *
     * @return null|string
     * @since   1.6.0
     */
    protected function get_schema_name() {
        $cookie_name = $this->page . '_schema_name';
        $wpda_main_db_schema = null;
        if ( isset( $_REQUEST['wpda_main_db_schema'] ) && '' !== $_REQUEST['wpda_main_db_schema'] ) {
            $wpda_main_db_schema = sanitize_text_field( wp_unslash( $_REQUEST['wpda_main_db_schema'] ) );
            // input var okay.
        } elseif ( isset( $_COOKIE[$cookie_name] ) ) {
            $wpda_main_db_schema = sanitize_text_field( wp_unslash( $_COOKIE[$cookie_name] ) );
            // input var okay.
        }
        if ( null !== $wpda_main_db_schema ) {
            if ( '' !== $wpda_main_db_schema && 'rdb:' !== substr( $wpda_main_db_schema, 0, 4 ) ) {
                if ( !WPDA_Dictionary_Exist::schema_exists( $wpda_main_db_schema ) ) {
                    return WPDA::get_user_default_scheme();
                }
            }
            if ( WPDA::schema_disabled( $wpda_main_db_schema ) ) {
                return WPDA::get_user_default_scheme();
            }
            if ( WPDA::schema_exists( $wpda_main_db_schema ) ) {
                return $wpda_main_db_schema;
            }
            echo '<p><strong>' . sprintf( __( 'ERROR - Remote database %s not available', 'wp-data-access' ), esc_attr( $wpda_main_db_schema ) ) . '</strong></p>';
        }
        return WPDA::get_user_default_scheme();
    }

    /**
     * Get favourite selection from cookie or list
     *
     * @return null|string
     * @since   1.6.0
     */
    protected function get_favourites() {
        $cookie_name = $this->page . '_favourites';
        if ( isset( $_REQUEST['wpda_main_favourites'] ) ) {
            return sanitize_text_field( wp_unslash( $_REQUEST['wpda_main_favourites'] ) );
            // input var okay.
        } elseif ( isset( $_COOKIE[$cookie_name] ) ) {
            return sanitize_text_field( wp_unslash( $_COOKIE[$cookie_name] ) );
        } else {
            return null;
        }
    }

    /**
     * Add labels to static function to make it available to class WPDA_List _View. Allowing to hide columns in
     * Data Explorer main page.
     *
     * @return array
     * @since    2.0.3
     */
    public static function column_headers_labels() {
        return array(
            'table_name'      => __( 'Name', 'wp-data-access' ),
            'icons'           => '',
            'table_type'      => __( 'Type', 'wp-data-access' ),
            'create_time'     => __( 'Creation Date', 'wp-data-access' ),
            'table_rows'      => __( '#Rows', 'wp-data-access' ),
            'auto_increment'  => __( 'Increment', 'wp-data-access' ),
            'engine'          => __( 'Engine', 'wp-data-access' ),
            'total_size'      => __( 'Size', 'wp-data-access' ),
            'data_size'       => __( 'Data Size', 'wp-data-access' ),
            'index_size'      => __( 'Index Size', 'wp-data-access' ),
            'overhead'        => __( 'Overhead', 'wp-data-access' ),
            'table_collation' => __( 'Collation', 'wp-data-access' ),
        );
    }

}
