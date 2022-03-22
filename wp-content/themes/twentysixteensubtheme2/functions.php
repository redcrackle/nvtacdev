<?php


add_action( 'wp_enqueue_scripts', 'twentysixteensubtheme2_enqueue_styles' );

function twentysixteensubtheme2_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
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


//Feature Custom Post Type
function create_county_post_type()
{
  register_post_type(
    'county',
    array(
      'labels' => array(
        'name' => __('Countys'),
        'singular_name' => __('County'),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New County'),
        'edit_item' => __('Edit County'),
        'new_item' => __('New County'),
        'view_item' => __('View County'),
        'search_items' => __('Search Countys'),
        'not_found' => __('No County found'),
        'not_found_in_trash' => __('No County found in Trash'),
        'parent_item_colon' => __('Parent County:')
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'county'),
      'menu_icon' => 'dashicons-star-filled',
      'supports' => array('title', 'editor', 'revisions'),
    )
  );
}
add_action('init', 'create_county_post_type');
?>