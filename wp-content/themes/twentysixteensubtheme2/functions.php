<?php


add_action( 'wp_enqueue_scripts', 'twentysixteensubtheme2_enqueue_styles' );

function twentysixteensubtheme2_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_register_script(
                'react-map',
                get_stylesheet_directory_uri() . '/map/build/index.js',
                ['wp-element'],
                '0.01',
                'true'
            );
            wp_enqueue_script('react-map');
} 

function add_style_select_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

//add custom styles to the WordPress editor
function my_custom_styles( $init_array ) {  
 
    $style_formats = array(  
        // These are the custom styles
        array(  
            'title' => 'Pull Quote',  
            'block' => 'div',  
            'classes' => 'pquote',
            'wrapper' => true,
        ),  
        array(  
            'title' => 'Small Text',  
            'block' => 'div',  
            'classes' => 'smtxt',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Heading 1',  
            'block' => 'span',  
            'classes' => 'h1',
            'wrapper' => true,
        ),
        array(
            'title' => 'Heading 2',
            'block' => 'span',
            'classes' => 'h2',
            'wrapper' => true,
        ),
        array(
            'title' => 'Heading 3',
            'block' => 'span',
            'classes' => 'h3',
            'wrapper' => true,
        ),
        array(
            'title' => 'Heading 4',
            'block' => 'span',
            'classes' => 'h4',
            'wrapper' => true,
        ),
        array(
            'title' => 'Heading 5',
            'block' => 'span',
            'classes' => 'h5',
            'wrapper' => true,
        ),
        array(
            'title' => 'Heading 6',
            'block' => 'span',
            'classes' => 'h6',
            'wrapper' => true,
        ),
        array(
            'title' => 'Images',
            'block' => 'span',
            'classes' => 'entry-content img',
            'wrapper' => true,
        ),
        array(
            'title' => 'Images No Borders',
            'block' => 'span',
            'classes' => 'entry-content-no img',
            'wrapper' => true,
        ),
        array(
            'title' => 'Buttons',
            'block' => 'span',
            'classes' => 'readmorebtn',
            'wrapper' => true,
        ),
    );  
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );  
    
    return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_custom_styles' );

function custom_editor_styles() {
	add_editor_style('editor-style.css');
}
 
add_action('init', 'custom_editor_styles');

function wpb_widgets_init() {
 
    register_sidebar( array(
        'name'          => 'Custom Header Widget Area',
        'id'            => 'custom-header-widget',
        'before_widget' => '<div class="chw-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="chw-title">',
        'after_title'   => '</h2>',
    ) );
 
}
add_action( 'widgets_init', 'wpb_widgets_init' );

function mapplic_extend_pins($pins) {
	// New pin types
	$custom = array('yellow-push-pin', 'yellow-push-pin2');
	
	// Merging arrays
	$pins = array_merge($pins, $custom);

	return $pins;
}
add_filter('mapplic_pins', 'mapplic_extend_pins');

function my_custom_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/style-login.css' );
}
//This loads the function above on the login page
add_action( 'login_enqueue_scripts', 'my_custom_login_stylesheet' );

add_action( 'rest_api_init', function () {
  register_rest_route( 'grantee/v1', '/map', array(
    'methods' => 'GET',
    'callback' => 'get_all_grantees',
    'args' => array(
      'zipcode' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric( $param );
        }
      ),
      'county' => array(
        'validate_callback' => function($param, $request, $key) {
          return $param;
        }
      ),
      'state' => array(
        'validate_callback' => function($param, $request, $key) {
          return $param;
        }
      ),
    ),
  ) );

  register_rest_route( 'grantee/v1', '/zip', array(
    'methods' => 'GET',
    'callback' => 'get_all_zipcodes',
    'args' => array(
      'zipcode' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric( $param );
        }
      ),
      'county' => array(
        'validate_callback' => function($param, $request, $key) {
          return $param;
        }
      ),
      'state' => array(
        'validate_callback' => function($param, $request, $key) {
          return $param;
        }
      ),
    ),
  ) );
} );

function get_all_grantees(WP_REST_Request $request) {
  $zip = $request->get_param( 'zip' );
  $county = $request->get_param( 'county' );
  $state = $request->get_param( 'state' );
  global $wpdb;
  $state_condition = '';
  if ($state && $state != 'all') {
    $state_condition = "and A.service_delivery_state LIKE '%$state%'";
  }
  $county_condition = '';
  if ($county && $county != 'all') {
    $county = addslashes($county);
    $county_condition = "and A.service_delivery_area LIKE '%$county%'";
  }
  $zip_condition = '';
  if ($zip && $zip != 'all') {
    $result = $wpdb->get_results("SELECT A.* FROM  wp_grantee_awards A join wp_zipcodes z on A.service_delivery_state LIKE CONCAT('%',z.state,'%') and A.service_delivery_area LIKE CONCAT('%',REPLACE(z.county,' County',''),'%') and z.zip = '$zip' where 1 $state_condition $county_condition ");
    return json_encode($result);
  }
  $result = $wpdb->get_results("SELECT * FROM wp_grantee_awards A where 1 $state_condition $county_condition");
  return json_encode($result);
}

function get_all_zipcodes(WP_REST_Request $request) {
  $zip = $request->get_param( 'zip' );
  $county = $request->get_param( 'county' );
  $state = $request->get_param( 'state' );
  global $wpdb;
  $state_condition = '';
  if ($state && $state != 'all') {
    $state_condition = "and A.service_delivery_state LIKE '%$state%'";
  }
  $county_condition = '';
  if ($county && $county != 'all') {
    $county = addslashes($county);
    $county_condition = "and z.county LIKE '%$county%'";
  }
  $zip_condition = '';
  if ($zip && $zip != 'all') {
    $zip_condition = "and zip like '$zip%'";
  }
  $result = $wpdb->get_results("SELECT distinct(z.zip) FROM wp_zipcodes z join wp_grantee_awards A on A.service_delivery_state LIKE CONCAT('%',z.state,'%') and A.service_delivery_area LIKE CONCAT('%',REPLACE(z.county,' County',''),'%') where 1 $state_condition $county_condition");
  return json_encode($result);
}

