<?php
/*
* Plugin Name:       Flagged Content
* Plugin URI:        https://wordpress.org/plugins/flagged-content/
* Description:       Allows visitors to flag posts and pages.
* Version:           1.0.2
* Author:            DivSpark
* Author URI:        https://profiles.wordpress.org/divspark/#content-plugins
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       flagged-content
*/

if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Main class which assembles and executes all other php files
 */
class Flagged_Content
{
    const VERSION = '1.0.2';

    /**
     * @var int This is the plugin debug mode setting.
     * 1: will output arrays and other variables in many locations throughout the plugin.
     * 0: suppresses all debugging information.
     */
    const DEBUG = 0;

    /** @var string Prefixed with wpdb prefix (e.g. wp_). Used throughout to refer to the plugin's table name */
	private $table_name;

    /** @var array Array of plugin settings found in the settings page. Retrieved from wp options */
	private $settings;


	public function __construct()
    {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'flaggedcontent';
        $this->init();
	}


    /**
     * Loads required php files and initializes objects. Filters request types to minimize what resources need to be loaded
     */
	public function init()
    {
        // all requests
        $this->check_plugin_version();

		// Public frontend
		if ( ! is_admin() )
		{
		    require_once ( plugin_dir_path( __FILE__ ) . 'flagged-content-public.php' );
			$load_public = new Flagged_Content_Public( $this );
		}

		// Ajax request
		elseif ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX )
        {
			require_once ( plugin_dir_path( __FILE__ ) . 'flagged-content-ajax.php' );
			$flagcontent_ajax = new Flagged_Content_AJAX( $this );
		}

		// Admin pages (regular, non-ajax request)
		else
        {
            // Cannot use global $pagenow in multisite installations as it is unavailable at this point
            $current_page = basename( $_SERVER['PHP_SELF'] );

			$page_query_param 		= isset( $_GET['page'] ) ? $_GET['page'] : '';
            $admin_flags_page 		= null;
			$admin_settings_page 	= null;

			// main page
			if ( $current_page == 'admin.php' && $page_query_param == 'flagged_content_flags_page' )
			{
				require_once ( plugin_dir_path( __FILE__ ) . 'admin/class-wp-list-table.php' );
				require_once ( plugin_dir_path( __FILE__ ) . 'admin/admin-flags-page.php' );
				$admin_flags_page = new Flagged_Content_Admin_Flags_Page( $this );
			}

			// settings page
			if ( ( $current_page == 'admin.php' && $page_query_param == 'flagged_content_settings_page' ) || $current_page == 'options.php' )
			{
				require_once ( plugin_dir_path( __FILE__ ) . 'admin/admin-settings-page.php' );
				$admin_settings_page = new Flagged_Content_Admin_Settings_Page( $this );
			}

			// all admin pages
			require_once ( plugin_dir_path( __FILE__ ) . 'admin/admin-pages.php' );
			$admin_page = new Flagged_Content_Admin( $admin_flags_page, $admin_settings_page, $this );

            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
			register_activation_hook( __FILE__, array( $admin_page, 'install_plugin' ) );
		}
	}


	public function get_table_name()
    {
        return $this->table_name;
    }


    public function get_settings()
    {
        if ( isset( $this->settings ) )
        {
            return $this->settings;
        }
        else
        {
            $this->settings = get_option( 'flaggedcontent_settings', array() );

            if ( ! empty( $this->settings ) ) {
                $this->settings['reason_choose'] = $this->unpack_reason( $this->settings['reason_choose'] );
            }

            return $this->settings;
        }
    }


    /**
     * $packed_reason comes in as:
     * 	"reason 1\nreason 2\nreason 3"
     *
     * Reason choose needs to be unpacked as:
     * Array(
     * 		[0] => reason 1
     * 		[1] => reason 2
     * 		[2] => reason 3
     * )
     *
     * @param $packed_reason
     * @return array
     */
    public function unpack_reason( $packed_reason )
    {
        if ( empty( $packed_reason ) ) {
            return array();
        }
        else {
            return explode( "\n", $packed_reason );
        }
    }


    /**
     * Generates and returns an associative array of default values for a form
     * @param array $modified Override the default values
     * @return array associative array
     */
    public function get_default_settings( $modified = array() )
    {
        $default = array(
            'content'                   => 'post',
            'reveal_label' 				=> 'Report Issue',
            'reveal_success_label'      => 'Reported',
            'reveal_style' 				=> 'theme;theme',
            'reveal_location' 			=> 'content_before',
            'reveal_align'              => 'left',
            'name' 						=> 'optional',
            'email' 					=> 'optional',
            'reason' 					=> 'required',
            'reason_choose' 			=> "This post contains broken links\nPost has incorrect information\nPost has spam\nCopyright Issue\nOther",
            'description' 				=> 'optional',
            'submit_label' 				=> 'Submit',
            'submit_style' 				=> 'theme;theme',
            'message_instructions' 		=> '',
            'message_success' 			=> 'You have flagged this item.',
            'email_enabled' 	        => 1,
            'permission_flag_view'      => 'manage_options',
            'permission_flag_edit'      => 'manage_options',
            'honeypot' 		            => 1,
            'save_ip_address'           => 1
        );

        if ( is_array( $modified ) and ! empty( $modified ) ) {
            return array_merge( $default, $modified );
        }
        else {
            return $default;
        }
    }


    /**
     * Check if the user has the appropriate permissions.
     * @param string $action Pass in the action to check permissions for: 'view' or 'edit'.
     * @return bool|string Returns TRUE if the user has permission, FALSE otherwise. If an invalid action is passed then FALSE will be returned.
     */
    public function check_permissions( $action = 'edit' )
    {
        $settings = $this->get_settings();

        if ( $action == 'view' ) {
            $current_permissions = $settings['permission_flag_view'];
        }
        elseif ( $action == 'edit' ) {
            $current_permissions = $settings['permission_flag_edit'];
        }
        else {
            return false;
        }

        return current_user_can( $current_permissions );
    }


    /**
     * Adds a settings link to the plugin's actions under plugins.php
     * - filter plugin_action_links_ . plugin_basename( __FILE__ ) - __construct()
     * @param $links
     * @return array
     */
    public function add_plugin_action_links( $links )
    {
        $add_links = array();
        $add_links[] = '<a href="' . admin_url( 'admin.php?page=flagged_content_settings_page' ) . '">Settings</a>';
        return array_merge( $add_links, $links );
    }


    /**
     * Adds a view more link to the plugin's meta under plugins.php
     * - filter plugin_row_meta - __construct()
     * @param $links
     * @return array
     * @param $file
     */
    public function add_plugin_row_meta( $links, $file )
    {
        $plugin = plugin_basename( __FILE__ );
        $add_links = array();

        if ( $file == $plugin ) {
            $add_links[] = '<a href="https://codecanyon.net/item/flagged-content-let-visitors-report-and-flag-posts-comments-and-more-to-admin-wordpress-plugin/19748662">Get Pro</a>';
        }

        return array_merge( $links, $add_links );
    }


    /**
     * Compare plugin version number in user's database with this code's version number.
     * If they don't match then an update has happened.
     * @action plugins_loaded - __construct()
     * @return void
     */
    public function check_plugin_version()
    {
        // check if version stored in database matches the current plugin version
        if ( get_option( 'flaggedcontent_version' ) !== Flagged_Content::VERSION ) {
            $this->update_plugin();
        }
    }

    /**
     * Updates plugin if the user's version in db does not match this file version
     */
    public function update_plugin()
    {
        
        // sync global settings in db with any newly added options
        $settings = get_option( 'flaggedcontent_settings', array() );
        $updated_settings = array_merge( $this->get_default_settings(), $settings );
        update_option( 'flaggedcontent_settings', $updated_settings );

        if ( Flagged_Content::DEBUG )
        {
            echo '<strong>Update running</strong><br><pre>';
            echo '$settings (before update):<br>';
            print_r ( $settings );

            $check_settings = get_option( 'flaggedcontent_settings', array() );
            echo '<br>$settings (after update):<br>';
            print_r ( $check_settings );
            echo '</pre>';
        }
        
        update_option( 'flaggedcontent_version', Flagged_Content::VERSION );
    }

}
$flagged_content_plugin = new Flagged_Content();