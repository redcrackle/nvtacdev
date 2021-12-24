<?php
/**
 * Init function
 */
if( !function_exists( 'otw_tsw_widgets_init' ) ){
	
	function otw_tsw_widgets_init(){
		
		global $otw_components, $wp_filesystem;
		
		if( isset( $otw_components['registered'] ) && isset( $otw_components['registered']['otw_shortcode'] ) ){
			
			$shortcode_components = $otw_components['registered']['otw_shortcode'];
			arsort( $shortcode_components );
			
			if( otw_init_filesystem() ){
				foreach( $shortcode_components as $shortcode ){
					if( $wp_filesystem->is_file( $shortcode['path'].'/widgets/otw_shortcode_widget.class.php' ) ){
						
						include_once( $shortcode['path'].'/widgets/otw_shortcode_widget.class.php' );
						break;
					}
				}
			}
		}
		register_widget( 'OTW_Shortcode_Widget' );
	}
}
/**
 * Init function
 */
if( !function_exists( 'otw_tsw_init' ) ){
	
	function otw_tsw_init(){
		
		global $otw_tsw_plugin_url, $otw_tsw_plugin_options, $otw_tsw_shortcode_component, $otw_tsw_shortcode_object, $otw_tsw_form_component, $otw_tsw_validator_component, $otw_tsw_form_object, $otw_tsw_skin, $wp_tsw_cs_items, $otw_tsw_factory_component, $otw_tsw_factory_object, $otw_tsw_plugin_id;
		
		if( is_admin() ){
			
			include_once( 'otw_tsw_process_actions.php' );
			
			add_action('admin_menu', 'otw_tsw_init_admin_menu' );
			
			add_action('admin_print_styles', 'otw_tsw_enqueue_admin_styles' );
			
			add_action('admin_enqueue_scripts', 'otw_tsw_enqueue_admin_scripts');
			
			add_filter('otwfcr_notice', 'otw_tsw_factory_message' );
			
		}
		otw_tsw_enqueue_styles();
		
		include_once( plugin_dir_path( __FILE__ ).'otw_tsw_dialog_info.php' );
		
		//shortcode component
		$otw_tsw_shortcode_component = otw_load_component( 'otw_shortcode' );
		$otw_tsw_shortcode_object = otw_get_component( $otw_tsw_shortcode_component );
		$otw_tsw_shortcode_object->editor_button_active_for['page'] = true;
		$otw_tsw_shortcode_object->editor_button_active_for['post'] = true;
		
		$otw_tsw_shortcode_object->add_default_external_lib( 'css', 'style', get_stylesheet_directory_uri().'/style.css', 'live_preview', 10 );
		
		$otw_tsw_shortcode_object->shortcodes['tabslayout'] = array( 'title' => esc_html__('Tabs Layout', 'otw_tsw'),'enabled' => true,'children' => false, 'parent' => false, 'order' => 7,'path' => dirname( __FILE__ ).'/otw_components/otw_shortcode/', 'url' => $otw_tsw_plugin_url.'include/otw_components/otw_shortcode/', 'dialog_text' => $otw_tsw_dialog_text  );

		include_once( plugin_dir_path( __FILE__ ).'otw_labels/otw_tsw_shortcode_object.labels.php' );
		$otw_tsw_shortcode_object->init();
		
		//form component
		$otw_tsw_form_component = otw_load_component( 'otw_form' );
		$otw_tsw_form_object = otw_get_component( $otw_tsw_form_component );
		include_once( plugin_dir_path( __FILE__ ).'otw_labels/otw_tsw_form_object.labels.php' );
		$otw_tsw_form_object->init();
		
		//validator component
		$otw_tsw_validator_component = otw_load_component( 'otw_validator' );
		$otw_tsw_validator_object = otw_get_component( $otw_tsw_validator_component );
		$otw_tsw_validator_object->init();
		
		$otw_tsw_factory_component = otw_load_component( 'otw_factory' );
		$otw_tsw_factory_object = otw_get_component( $otw_tsw_factory_component );
		$otw_tsw_factory_object->add_plugin( $otw_tsw_plugin_id, dirname( dirname( __FILE__ ) ).'/otw_content_manager.php', array( 'menu_parent' => 'otw-tsw-settings', 'lc_name' => esc_html__( 'License Manager', 'otw_tsw' ), 'menu_key' => 'otw-tsw' ) );
		
		include_once( plugin_dir_path( __FILE__ ).'otw_labels/otw_tsw_factory_object.labels.php' );
		$otw_tsw_factory_object->init();

	}
}

/**
 * include needed styles
 */
if( !function_exists( 'otw_tsw_enqueue_styles' ) ){
	function otw_tsw_enqueue_styles(){
		global $otw_tsw_plugin_url, $otw_tsw_css_version;
		
		
	}
}


/**
 * Admin styles
 */
if( !function_exists( 'otw_tsw_enqueue_admin_styles' ) ){
	
	function otw_tsw_enqueue_admin_styles(){
		
		global $otw_tsw_plugin_url, $otw_tsw_css_version;
		
		$currentScreen = get_current_screen();
		
		switch( $currentScreen->id ){
			
			case 'widgets':
			case 'page':
			case 'post':
					wp_enqueue_style( 'otw_tsw_admin', $otw_tsw_plugin_url.'/css/otw_tsw_admin.css', array( 'thickbox' ), $otw_tsw_css_version );
				break;
		}
	}
}


/**
 * Admin scripts
 */
if( !function_exists( 'otw_tsw_enqueue_admin_scripts' ) ){
	
	function otw_tsw_enqueue_admin_scripts( $requested_page ){
		
		global $otw_tsw_plugin_url, $otw_tsw_js_version;
		
		switch( $requested_page ){
			
			case 'widgets.php':
					wp_enqueue_script("otw_shotcode_widget_admin", $otw_tsw_plugin_url.'include/otw_components/otw_shortcode/js/otw_shortcode_widget_admin.js'  , array( 'jquery', 'thickbox' ), $otw_tsw_js_version );
					
					if(function_exists( 'wp_enqueue_media' )){
						wp_enqueue_media();
					}else{
						wp_enqueue_style('thickbox');
						wp_enqueue_script('media-upload');
						wp_enqueue_script('thickbox');
					}
				break;
		}
	}
	
}

/**
 * Init admin menu
 */
if( !function_exists( 'otw_tsw_init_admin_menu' ) ){
	
	function otw_tsw_init_admin_menu(){
		
		global $otw_tsw_plugin_url;
		
		add_menu_page(__('Tabs Shortcode And Widget', 'otw_tsw'), esc_html__('Tabs Shortcode And Widget', 'otw_tsw'), 'manage_options', 'otw-tsw-settings', 'otw_tsw_settings', $otw_tsw_plugin_url.'images/otw-sbm-icon.png');
		add_submenu_page( 'otw-tsw-settings', esc_html__('Settings', 'otw_tsw'), esc_html__('Settings', 'otw_tsw'), 'manage_options', 'otw-tsw-settings', 'otw_tsw_settings' );
	}
}

/**
 * Settings page
 */
if( !function_exists( 'otw_tsw_settings' ) ){
	
	function otw_tsw_settings(){
		require_once( 'otw_tsw_settings.php' );
	}
}

/**
 * factory messages
 */
if( !function_exists( 'otw_tsw_factory_message' ) ){
	
	function otw_tsw_factory_message( $params ){
		
		global $otw_tsw_plugin_id;
		
		if( isset( $params['plugin'] ) && $otw_tsw_plugin_id == $params['plugin'] ){
			
			//filter out some messages if need it
		}
		if( isset( $params['message'] ) )
		{
			return $params['message'];
		}
		return $params;
	}
}
?>