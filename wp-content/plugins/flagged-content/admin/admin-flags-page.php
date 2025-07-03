<?php
if ( ! defined( 'WPINC' ) ) { die; }

/*
 * Upkeep: Flagged_Content_WP_List_Table
 * If class-wp-list-table.php has to be changed, then:
 * - Change WP_List_Table class name to Flagged_Content_WP_List_Table
 * - Change all WP_List_Table references within the class to Flagged_Content_WP_List_Table
 */
class Flagged_Content_Admin_Flags_Page extends Flagged_Content_WP_List_Table
{
    public  $plugin;
    private $table_name;
    public  $flagged_content      = array();
    private $trash_url_post       = 'edit.php?post_status=trash&post_type=';
    private $sql_where            = '';
    private $sql_where_conditions = array();

    /**
     * Had to add the processing of actions into this constructor to keep the badge number in sync. The actions and setting of object properties 
     * have to be hooked into wp_loaded, otherwise undefined function errors will occur.
     */
    function __construct( $plugin )
    {
        $this->plugin = $plugin;
        $this->table_name = $this->plugin->get_table_name();

        add_action( 'wp_loaded', array( $this, 'process_actions' ) );
        add_action( 'wp_loaded', array( $this, 'set_flagged_content_property' ) );
    }

    /**
     * The parent constructor fires too early in the WP cycle and errors out with an unknown function error for convert_to_screen(). Instead, we use init() 
     * to run the parent constructor. Init() is called in the display_page() method which is called in the admin_menu hook (admin.php)
     *
     * Need to fire the parent constructor and pass it default config arguments in this method.
     */
    public function init()
    {
        //Set parent defaults
        parent::__construct(
            array(
                'singular'  => 'flag',    // singular name of the listed records
                'plural'    => 'flags',   // plural name of the listed records
                'ajax'      => false      // does this table support ajax?
            )
        );
    }

    /**
     * Queries the database to build an array of arrays containing flagged content information.
     *
     * Added to the plugins_loaded hook in __construct() 
     * 
     * sets flagged_content = associate array ( 
     *          [post_id] => array (information on the flagged content),
     *          [post_id] => array (information on the flagged content)
     *      );
     * 
     */  
    public function set_flagged_content_property()
    {
        global $wpdb;

        $sql =
            "SELECT   post_id, count(*) as 'flag_count'
             FROM     {$this->table_name}
             GROUP BY post_id
             ORDER BY post_id";

        $content_db = $wpdb->get_results( $sql, ARRAY_A );
        
        $content = array();

        foreach ( $content_db as $content_row )
        {
            $id = (int) $content_row['post_id'];

            $content[ $id ] = $content_row;

            // Get the status first
            $content[ $id ]['status'] = get_post_status( $id );

            // Invalid post if id = 0, there's always an ID
            if ( $id === 0 )
            {
                $content[ $id ]['status']       = 'invalid';
                $content[ $id ]['post_type']    = 'invalid';
                $content[ $id ]['title']        = 'Invalid';
                $content[ $id ]['title_link']   = 'Invalid';
                $content[ $id ]['title_icon']   = '<span class="dashicons dashicons-dismiss" title="Invalid content"></span>';
            }

            // Post no longer exists, i.e. has been permanently deleted
            elseif ( $content[ $id ]['status'] === FALSE )
            {
                $content[ $id ]['status']       = 'deleted';
                $content[ $id ]['post_type']    = 'deleted';
                $content[ $id ]['title']        = 'Deleted';
                $content[ $id ]['title_link']   = 'Deleted';
                $content[ $id ]['title_icon']   = '<span class="dashicons dashicons-dismiss" title="This has been permanently deleted"></span>';
            }

            // Post: In Trash
            elseif ( $content[ $id ]['status'] === 'trash' )
            {
                $content[ $id ]['post_type']          = get_post_type( $id );
                $content[ $id ]['label_singular']     = get_post_type_object( $content[ $id ]['post_type'] )->labels->singular_name;
                $content[ $id ]['label_plural']       = get_post_type_object( $content[ $id ]['post_type'] )->labels->name;
                $content[ $id ]['title']              = get_the_title( $id );
                $content[ $id ]['view_url']           = $this->trash_url_post . $content[ $id ]['post_type'];
                $content[ $id ]['title_link']         = sprintf( '<a href="%s" target="_blank">%s</a>', $content[ $id ]['view_url'], $content[ $id ]['title'] );
                $content[ $id ]['title_icon']         = '<span class="dashicons dashicons-trash" title="This is in the trash"></span>';
                $content[ $id ]['actions']['view']    = sprintf( '<a href="%1$s" target="_blank">View trashed %2$s</a>', $content[ $id ]['view_url'], $content[ $id ]['label_plural'] );
                $content[ $id ]['post_type_exists']   = true;
            }

            // Post: Active, no issues
            else
            {
                $content[ $id ]['post_type']          = get_post_type( $id );
                $content[ $id ]['label_singular']     = get_post_type_object( $content[ $id ]['post_type'] )->labels->singular_name;
                $content[ $id ]['label_plural']       = get_post_type_object( $content[ $id ]['post_type'] )->labels->name;
                $content[ $id ]['title']              = get_the_title( $id );
                $content[ $id ]['view_url']           = get_post_permalink( $id );
                $content[ $id ]['edit_url']           = get_edit_post_link( $id );
                $content[ $id ]['title_link']         = sprintf( '<a href="%s" target="_blank">%s</a>', $content[ $id ]['edit_url'], $content[ $id ]['title'] );
                $content[ $id ]['title_icon']         = '';
                $content[ $id ]['actions']['view']    = sprintf( '<a href="%s" target="_blank">View</a>', $content[ $id ]['view_url'] );
                $content[ $id ]['actions']['edit']    = sprintf( '<a href="%s" target="_blank">Edit</a>', $content[ $id ]['edit_url'] );
                $content[ $id ]['post_type_exists']   = true;
            }
        }

        $this->flagged_content = $content;
    }

    /**
     * This method is called when the parent class can't find a method
     * specifically build for a given column.
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     */
    function column_default( $item, $column_name )
    {
        switch( $column_name )
        {
            //case 'reason':
            //case 'description':
                //return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }


    /**
     * Required if displaying checkboxes or using bulk actions. The 'cb' column
     * is given special treatment when columns are processed. It always needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item
     * @return string Text to be placed inside the column <td>
     */
    function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  // Repurpose the table's singular label
            /*$2%s*/ $item['id']                // The value of the checkbox should be the record's id
        );
    }


    function column_status( $item )
    {
        return "<span class='dashicons dashicons-flag flagcontent-flag-icon'></span>";
    }


    function column_reason ( $item )
    {
        return "<p>{$item['reason']}</p><p class='flagcontent-sub-text-color'>{$item['description']}</p>";
    }


    function column_post_id ( $item )
    {
        $content       = $this->flagged_content;
        $id            = $item['post_id'];
        $icon          = $content[ $id ]['title_icon'];
        $label         = $content[ $id ]['label_singular'];
        $content_title = $content[ $id ]['title_link'];
        $title         = "{$icon}{$label}: {$content_title}";
        $actions       = array();

        // There are actions inside the content array
        if ( isset( $content[ $id ]['actions'] ) || array_key_exists( 'actions', $content[ $id ] ) )
        {
            foreach ( $content[ $id ]['actions'] as $action_key => $action_value )
            {
                $actions[ $action_key ] = $action_value;
            }
        }

        return sprintf( '%1$s %2$s', $title, $this->row_actions( $actions ) );
    }


    function column_flag_actions( $item )
    {
        $content        = $this->flagged_content;
        $id             = $item['post_id'];
        $post_type      = $content[ $id ]['post_type'];
        $actions_flag   = '';
        $multiple_flags = false;
        $actions        = array();

        /** If multiple flags exist for this post then construct the "View all flags" flag icon */

        // Multiple flags
        if ( $content[ $id ]['flag_count'] > 1 )
        {
            $multiple_flags = true;
            $view_all_alt = "There are multiple flags for this $post_type.";

            $actions_flag = sprintf(
                '<span class="dashicons dashicons-flag flagcontent-view-all-flags" title="%1$s"></span>',
                $view_all_alt       // title= %1$s
            );
        }

        /** Row actions for the Actions column */

        // Check if user has 'edit' permission to view delete this flag link
        if ( $this->plugin->check_permissions( 'edit' ) )
        {
            $delete_nonce = isset( $delete_nonce ) ? $delete_nonce : wp_create_nonce( 'flagcontent_row_delete_this_flag' );

            $actions['delete_flag'] = sprintf(
                '<a href="?page=%1$s&action=%2$s&delete_id=%3$d&_wpnonce=%4$s" class="%5$s">%6$s</a><br>',
                $_REQUEST['page'],                     // %1$s
                'delete_this_flag',                    // %2$s
                $item['id'],                           // %3$d
                $delete_nonce,                         // %4$s
                'flagcontent-delete-this-flag-link',   // %5$s
                'Delete this flag'                     // %6$s
            );
        }

        if ( $multiple_flags )
        {
            $delete_nonce = wp_create_nonce( 'flagcontent_row_delete_flags' );

            // Check if user has 'edit' permission to view delete all link
            if ( $this->plugin->check_permissions( 'edit' ) )
            {
                // $actions['delete_all_flags'] = sprintf
                $actions['delete_flag'] .= sprintf(
                    '<a href="?page=%1$s&action=%2$s&delete_id=%3$d&_wpnonce=%4$s" class="%5$s" data-flagcontent-post-type="%6$s" title="Delete all flags for this %6$s" >%7$s</a>',
                    $_REQUEST['page'],                      // %1$s
                    'delete_all_flags',                     // %2$s
                    $id,                                    // %3$d
                    $delete_nonce,                          // %4$s
                    'flagcontent-delete-all-flag-link',     // %5$s
                    $post_type,                             // %6$s
                    'Delete all flags'                      // %7$s
                );
            }
        }

        return sprintf( '%1$s %2$s', $actions_flag, $this->row_actions( $actions ) );
    }

    function column_user_id( $item )
    {
        $user_column_output = '';

        // Show username if this was a logged in user
        if ( $item['user_id'] > 0 )
        {
            $user = get_user_by( 'id', $item['user_id'] );
            $user_login = $user ? $user->user_login : '';

            if ( current_user_can( 'edit_users' ) )
            {
                $user_column_output .= sprintf(
                    '<span class="dashicons dashicons-admin-users flagcontent-list-table-icon" title="Flagged by a logged in user"></span><a href="%1$s" target="_blank">%2$s</a><br />',
                    get_edit_user_link( $item['user_id'] ) , // %1$s
                    $user_login                              // %2$s
                );
            }
            else
            {
                $user_column_output .= $user_login . '<br />';
            }
        }

        // Only show the flag submitters name and email if it was submitted by a non-logged in user, or the name or email differs from a logged in user's profile username or email
        if ( $item['user_id'] == 0 ||  $user->user_login != $item['name_entered'] || $user->user_email != $item['email'] )
        {
            // Show the entered user name
            if ( $item['name_entered'] !== '' && $item['name_entered'] !== NULL ) {
                $user_column_output .= $item['name_entered'] . '<br />';
            }

            // Show the email address
            if ( $item['email'] !== '' && $item['email'] !== NULL ) {
                $user_column_output .= $item['email'] . '<br />';
            }
        }

        // Convert and show the ip address
        $ip_readable = $this->ip_address_convert_to_readable( $item['ip'] );    

        $user_column_output .= $ip_readable;

        return "<div class='flagcontent-sub-text-color'>{$user_column_output}</div>";
    }

    /**
     * Save mySQL datetime into PHP Datetime object and
     * convert to WordPress format specified by user in Settings > General
     */
    function column_date_notified( $item )
    {
        
        $db_datetime = new DateTime( $item['date_notified'] );    

        $wp_formatted_datetime = $db_datetime->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ); 

        return "<div class='flagcontent-sub-text-color'>{$wp_formatted_datetime}</div>";
    }


    /**
     * Required. This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'status'        => 'Flag',
            'reason'        => '',
            'post_id'       => 'For',
            'flag_actions'  => 'Actions',
            'user_id'       => 'Submitted By',
            'date_notified' => 'Submitted On'
        );
        
        return $columns;
    }


    /**
     * Optional. Return an array where the key is the column that needs to be sortable,
     * and the value is db column to sort by.
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'post_id'       => array( 'post_id', false ),
            'date_notified' => array( 'date_notified', false ) // date_notified is the default sorting, in descending order. true means it's already sorted
        );

        return $sortable_columns;
    }

    /**
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     */
    function get_bulk_actions()
    {
        if ( ! $this->plugin->check_permissions( 'edit' ) ) {
            return array();
        }

        $actions = array(
            'bulk_delete'    => 'Delete'
        );
        
        return $actions;
    }


    /**
     * Handles bulk and row actions, the redirects back to same page.
     * - $this->current_action(), found in class-wp-list-table.php, checks $_REQUEST['filter_action'], $_REQUEST['action'], and $_REQUEST['action2'].
     *
     * @action wp_loaded __construct()
     */
    function process_actions()
    {
        if ( ! $this->plugin->check_permissions( 'edit' ) ) {
            return;
        }

        // Exit function if no action has been submitted
        if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
            return;
        }

        global $wpdb;

        /**
         * Process bulk actions
         */
        if ( $this->current_action() === 'bulk_delete' )
        {
            if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-flags' ) ) { die; }

            $id_items = join( ',', $_GET['flag'] );
            $sql = "DELETE FROM {$this->table_name} WHERE id IN ({$id_items})";
            $wpdb->query($sql);
        }

        /**
         * Process row actions
         */
        elseif ( $this->current_action() === 'delete_all_flags' )
        {
            if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'flagcontent_row_delete_flags' ) ) { die; }

            $post_id = absint( $_GET['delete_id'] );

            if ( $post_id > 0 )
            {
                global $wpdb;
                $sql = "DELETE FROM {$this->table_name} WHERE post_id = {$post_id}";
                $wpdb->query( $sql );
            }
        }

        elseif ( $this->current_action() === 'delete_this_flag' )
        {
            if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'flagcontent_row_delete_this_flag' ) ) { die; }

            $flag_id = absint( $_GET['delete_id'] );

            if ( $flag_id > 0 )
            {
                global $wpdb;
                $sql = "DELETE FROM {$this->table_name} WHERE id = {$flag_id}";
                $wpdb->query( $sql );
            }
        }

        /**
         * Removes all extraneous query arguments from the URL and redirects back to the same page
         */
        $redirect_to = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2', 'flag', 'delete_id' ), wp_get_referer() );
        $redirect_to = add_query_arg( 'paged', $this->get_pagenum(), $redirect_to );
        $redirect_to = esc_url_raw( $redirect_to );
        wp_safe_redirect( $redirect_to );
        exit();
    }


    /**
     * Prepare data for display.
     */
    function prepare_items()
    {
        global $wpdb; 

        /** records per page to show */
        $per_page = 20;

        /**
         * Required. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * Required. Build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array( $columns, $hidden, $sortable );

        /** Required for pagination. Gets the current page. */
        $current_page = $this->get_pagenum();

        global $wpdb;

        $sql_select = "SELECT * FROM {$this->table_name}";
        $conditions = array();
        $sql_orderby = '';

        /**
         * Order By &orderby=
         */
        if ( ! empty( $_REQUEST['orderby'] ) )
        {
            $sql_orderby .= ' ORDER BY ' . $_REQUEST['orderby'];
            $sql_orderby .= ! empty( $_REQUEST['order'] ) ? ' ' . $_REQUEST['order'] : ' ASC';
        }
        else
        {
            $sql_orderby .= ' ORDER BY date_notified DESC' ;
        }

        $this->sql_where_conditions = $conditions;
        $sql = $this->create_prepared_sql_statement( $sql_select, $conditions, $sql_orderby );

        // If no conditions, then do not use the prepare statement. Otherwise, there will be no
        // placeholders in the prepared SQL and wpdb::prepare will trigger a notice.
        if ( empty( $conditions ) ) {
            $data = $wpdb->get_results( $sql, 'ARRAY_A' );
        }
        else {
            $data = $wpdb->get_results( $wpdb->prepare( $sql, $conditions ), 'ARRAY_A' );
        }

        /** Required for pagination. Total number of items in your database */
        $total_items = count( $data );
        
        /**
         * The WP_List_Table class does not handle pagination. Need to ensure the data
         * is trimmed to only the current page.
         */
        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        
        /**
         * Required. Add *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        /** Required. Register pagination options & calculations. */
        $this->set_pagination_args(
            array(
                'total_items' => $total_items,                      // WE have to calculate the total number of items
                'per_page'    => $per_page,                         // WE have to determine how many items to show on a page
                'total_pages' => ceil( $total_items / $per_page )   // WE have to calculate the total number of pages
            )
        );
    }

    /**
     * Text displayed when no data is available
     */
    public function no_items()
    {
        echo '<span class="dashicons dashicons-yes"></span> There are no flags';
    }


    public function ip_address_convert_to_readable( $ip_binary )
    {
        // Avoid PHP Warnings
        if ( $ip_binary === NULL ) {
            $ip_readable = '';
        }
        else {
            $ip_readable = inet_ntop( $ip_binary ); // Convert from binary to readable IP
        }

        return $ip_readable;
    }


    /**
     * Utility function to create a SQL string from an array of $conditions
     * e.g. $conditions['post_id'] = 123 becomes ... post_id = 123 AND ...
     * @param string $sql_select
     * @param array $conditions
     * @param string $sql_orderby
     * @return string
     */
    public function create_prepared_sql_statement( $sql_select, $conditions, $sql_orderby = '' )
    {
        if ( empty( $conditions ) ) {
            return $sql_select . $sql_orderby;
        }

        $where_placeholder = array();

        foreach ( array_keys( $conditions ) as $condition ) {
            $where_placeholder[] = "$condition = %d";
        }

        $sql_where = ' WHERE ' . implode( ' AND ', $where_placeholder );

        $sql = $sql_select . $sql_where . $sql_orderby;

        return $sql;
    }


    /**
     * Called by add_admin_menus() -> add_menu_page() in admin-pages.php
     */
    public function display_page()
    {
        $this->init();
        $this->prepare_items();

        echo '<div class="wrap">';
            echo '<h1>Flagged Content - Flags</h1>';

            echo '<div class="flagcontent-list-table">';

                echo '<form method="get">';

                    // For plugins, ensure that the form posts back to the current page
                    echo "<input type='hidden' name='page' value='{$_REQUEST['page']}' />";

                    $this->views();
                    
                    // Render the completed list table
                    $this->display();

                echo '</form>';
            echo '</div>';

            // Debug
            if ( Flagged_Content::DEBUG )
            {
                echo '<pre>';
                $redirect_to = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2', 'flag' ), wp_get_referer() );
                $redirect_to = add_query_arg( 'paged', $this->get_pagenum(), $redirect_to );

                echo '<br>$this->sql_where';
                var_dump( $this->sql_where );

                echo '<br>wp_get_referer: ' . wp_get_referer() . '<br>';
                echo 'this->get_pagenum: ' . $this->get_pagenum() . '<br>';
                echo 'redirect_to: ' . $redirect_to . '<br>';
                echo 'esc_url_raw (redirect_to): ' . esc_url_raw($redirect_to) . '<br>';

                echo 'absint(blank): ' . absint('p') . '<br>';

                echo 'Flagged_content: ';
                print_r ( $this->flagged_content );

                echo '<br>Items: ';
                print_r ( $this->items );

                echo '<br>$_GET: ';
                print_r ( $_GET );

                echo '<br>$_POST: ';
                print_r ( $_POST );

                echo '</pre>';
            }

        echo '</div>';
    }
}