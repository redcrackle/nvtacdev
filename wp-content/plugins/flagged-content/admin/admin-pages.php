<?php
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Runs on all admin requests.
 * Controls admin menu output and contains several admin utility functions.
 */
class Flagged_Content_Admin
{
	public $admin_flags_page;
	public $admin_settings_page;
	public $plugin;
	public $settings;
	public $page_sections;

	public function __construct( $admin_flags_page = null, $admin_settings_page = null, $plugin )
    {
		$this->admin_flags_page    = $admin_flags_page;
		$this->admin_settings_page = $admin_settings_page;
		$this->plugin 			   = $plugin;
		$this->settings 		   = $this->plugin->get_settings();

        $this->init();
	}

	public function init()
    {
        // Cannot use global $pagenow in multisite installations as it is unavailable at this point
        $current_page = basename( $_SERVER['PHP_SELF'] );

		$page_query_param = ( isset( $_GET['page'] ) ) ? $_GET['page'] : '';

        add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

		// Enqueue this plugin's admin scripts only on its admin pages
		if ( ( $current_page == 'admin.php' && ( $page_query_param == 'flagged_content_flags_page' || $page_query_param == 'flagged_content_settings_page' ) ) || ( $current_page == 'options.php' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_files' ) );
		}

        add_action( 'delete_post', array( $this, 'post_delete_cleanup' ) );
	}

	public function add_admin_menus()
    {
	    global $wpdb;
	    $table_name = $this->plugin->get_table_name();
	    $menu_label = 'Flagged';
        $notify_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name};" );

	    if ( $notify_count > 0 && $this->plugin->check_permissions( 'view' ) )
	    {
	        $menu_label_title = $notify_count . ' flag' . ( $notify_count > 1 ? 's' : '' );
	        $menu_label .= " <span class='update-plugins count-{$notify_count}' title='{$menu_label_title}'><span class='update-count'>{$notify_count}</span></span>";
	    }

	    // Main page
		add_menu_page( 
			'Flagged Content', 									// $page_title
			$menu_label,  										// $menu_title
            $this->settings['permission_flag_view'], 			// $capability
			'flagged_content_flags_page', 					    // $menu_slug
			array( $this->admin_flags_page, 'display_page' ), 	// $function
			'dashicons-flag' 									// $icon_url
			  													// (int) $position
		);

		// Settings
		add_submenu_page( 
			'flagged_content_flags_page', 						    // $parent_slug
			'Flagged Content - Settings', 						    // $page_title
			'Settings', 										    // $menu_title
			'manage_options', 									    // $capability
			'flagged_content_settings_page', 					    // $menu-slug
			array( $this->admin_settings_page, 'display_page' ) 	// $function
		);

	}

	public function enqueue_admin_files( $hook )
    {
	    wp_enqueue_style(  'flagcontent-admin-style',  plugins_url('../css/admin-styles.css', __FILE__ ), array(),           Flagged_Content::VERSION );
        wp_enqueue_script( 'flagcontent-admin-script', plugins_url('../js/admin-script.js', __FILE__ ),   array( 'jquery' ), Flagged_Content::VERSION );
	}

	/**
	 * When a post is deleted, remove any flags under that post
	 */
    public function post_delete_cleanup( $post_id )
    {
	    global $wpdb;
	    $table_name = $this->plugin->get_table_name();
	    $sql        = $wpdb->prepare( "DELETE FROM {$table_name} WHERE post_id = %d", $post_id );

	    $wpdb->query( $sql );
	}

    /**
     * Builds a table, adds a default form and default settings
     */
    public function install_plugin()
    {
		global $wpdb;
		$table_name = $this->plugin->get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql =
            "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name_entered varchar(75),
			email varchar(255),
			reason varchar(255),
			description text,
			ip varbinary(16),
			user_id bigint(20) NOT NULL,
			date_notified datetime NOT NULL,
			post_id bigint(20) NOT NULL,
			PRIMARY KEY  (id)
		    ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

		add_option( 'flaggedcontent_version',  Flagged_Content::VERSION );
        add_option( 'flaggedcontent_settings', $this->plugin->get_default_settings() );
	}
}