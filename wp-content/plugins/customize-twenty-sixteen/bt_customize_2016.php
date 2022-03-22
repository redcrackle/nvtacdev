<?php

/**
 * Plugin Name: Customize Twenty Sixteen 
 * Description: Adds Google Fonts and templates without sidebar to Twenty Sixteen theme.
 * Version: 1.0.2
 * Author: BoldThemes
 * Author URI: http://bold-themes.com
 */

if ( ! function_exists( 'boldthemes_2016_enqueue' ) ) {
	function boldthemes_2016_enqueue() {
		wp_enqueue_style( 'bt_2016', plugins_url( 'style.css', __FILE__ ) );
		
		wp_enqueue_script( 'bt_2016', plugins_url( 'script.js', __FILE__ ) );
		
		$theme_options = get_option( 'boldthemes_theme_options' );
		
		if ( $theme_options ) {	
			if ( isset( $theme_options[ 'custom_css' ] ) && $theme_options[ 'custom_css' ] != '' ) {
				wp_add_inline_style( 'bt_2016', $theme_options[ 'custom_css' ] );
			}
			if ( isset( $theme_options[ 'custom_js' ] ) && $theme_options[ 'custom_js' ] != '' ) {
				wp_add_inline_script( 'bt_2016', $theme_options[ 'custom_js' ] );
			}		
		}
	}
}
add_action( 'wp_enqueue_scripts', 'boldthemes_2016_enqueue', 100 );

if ( ! function_exists( 'boldthemes_2016_body_classes' ) ) {
	function boldthemes_2016_body_classes( $classes ) {
		
		$theme_options = get_option( 'boldthemes_theme_options' );
		
		if ( $theme_options ) {	
			if ( isset( $theme_options[ 'remove_body_space' ] ) && $theme_options[ 'remove_body_space' ] == 'yes' ) {
				$classes[] = 'bt-remove-body-space';
			}
			if ( isset( $theme_options[ 'remove_header_space' ] ) && $theme_options[ 'remove_header_space' ] == 'yes' ) {
				$classes[] = 'bt-remove-header-space';
			}
			if ( isset( $theme_options[ 'remove_entry_header' ] ) && $theme_options[ 'remove_entry_header' ] == 'yes' ) {
				$classes[] = 'bt-remove-entry-header';
			}
			if ( isset( $theme_options[ 'menu_options' ] ) && $theme_options[ 'menu_options' ] != 'default' ) {
				if ( $theme_options[ 'menu_options' ] == 'left' ) {
					$classes[] = 'bt-menu-left';
				} else if ( $theme_options[ 'menu_options' ] == 'center-below' ) {
					$classes[] = 'bt-menu-center-below';
				}
			}
		}

		return $classes;
	}
}
add_filter( 'body_class', 'boldthemes_2016_body_classes' );

class BoldThemes {
	static $font_arr = array();
}

if ( ! function_exists( 'boldthemes_customize_register' ) ) {
	function boldthemes_customize_register( $wp_customize ) {
		
		$wp_customize->add_section( 'boldthemes_section' , array(
			'title'      => esc_html__( 'BoldThemes Settings', 'bt-twentysixteen-customization' ),
			'priority'   => 1000,
		));
		
		require_once( plugin_dir_path( __FILE__ ) . 'web_fonts.php' );
	}
}
add_action( 'customize_register', 'boldthemes_customize_register' );

// BODY FONT
if ( ! function_exists( 'boldthemes_customize_body_font' ) ) {
	function boldthemes_customize_body_font( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[body_font]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'body_font', array(
			'label'     => esc_html__( 'Body font', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[body_font]',
			'priority'  => 10,
			'type'      => 'select',
			'choices'   => BoldThemes::$font_arr
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_body_font' );

// TITLE FONT
if ( ! function_exists( 'boldthemes_customize_title_font' ) ) {
	function boldthemes_customize_title_font( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[title_font]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'title_font', array(
			'label'     => esc_html__( 'Title font', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[title_font]',
			'priority'  => 20,
			'type'      => 'select',
			'choices'   => BoldThemes::$font_arr
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_title_font' );

// HEADING FONT
if ( ! function_exists( 'boldthemes_customize_heading_font' ) ) {
	function boldthemes_customize_heading_font( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[heading_font]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'heading_font', array(
			'label'     => esc_html__( 'Heading font', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[heading_font]',
			'priority'  => 20,
			'type'      => 'select',
			'choices'   => BoldThemes::$font_arr
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_heading_font' );

// MENU FONT
if ( ! function_exists( 'boldthemes_customize_heading_menu_font' ) ) {
	function boldthemes_customize_heading_menu_font( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[menu_font]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'menu_font', array(
			'label'     => esc_html__( 'Menu font', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[menu_font]',
			'priority'  => 30,
			'type'      => 'select',
			'choices'   => BoldThemes::$font_arr
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_heading_menu_font' );

// REMOVE BODY SPACE
if ( ! function_exists( 'boldthemes_customize_remove_body_space' ) ) {
	function boldthemes_customize_remove_body_space( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[remove_body_space]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'no'
		));
		$wp_customize->add_control( 'remove_body_space', array(
			'label'     => esc_html__( 'Remove space around pages', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[remove_body_space]',
			'priority'  => 40,
			'type'      => 'select',
			'choices'   => array( 'no' => 'No', 'yes' => 'Yes' )
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_remove_body_space' );

// REMOVE HEADER SPACE
if ( ! function_exists( 'boldthemes_customize_remove_header_space' ) ) {
	function boldthemes_customize_remove_header_space( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[remove_header_space]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'no'
		));
		$wp_customize->add_control( 'remove_header_space', array(
			'label'     => esc_html__( 'Remove space around header', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[remove_header_space]',
			'priority'  => 50,
			'type'      => 'select',
			'choices'   => array( 'no' => 'No', 'yes' => 'Yes' )
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_remove_header_space' );

// REMOVE ENTRY HEADER
if ( ! function_exists( 'boldthemes_customize_remove_entry_header' ) ) {
	function boldthemes_customize_remove_entry_header( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[remove_entry_header]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'no'
		));
		$wp_customize->add_control( 'remove_entry_header', array(
			'label'     => esc_html__( 'Remove entry header', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[remove_entry_header]',
			'priority'  => 60,
			'type'      => 'select',
			'choices'   => array( 'no' => 'No', 'yes' => 'Yes' )
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_remove_entry_header' );

// MENU OPTIONS
if ( ! function_exists( 'boldthemes_customize_menu_options' ) ) {
	function boldthemes_customize_menu_options( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[menu_options]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'no'
		));
		$wp_customize->add_control( 'menu_options', array(
			'label'     => esc_html__( 'Menu position', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[menu_options]',
			'priority'  => 60,
			'type'      => 'select',
			'choices'   => array( 'default' => 'Default (right, in line with logo)', 'left' => 'Left, in line with logo', 'center-below' => 'Centered, below logo' )
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_menu_options' );

// CUSTOM CSS
if ( ! function_exists( 'boldthemes_customize_custom_css' ) ) {
	function boldthemes_customize_custom_css( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[custom_css]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options'
		));
		$wp_customize->add_control( 'custom_css', array(
			'label'     => esc_html__( 'Custom CSS', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[custom_css]',
			'priority'  => 70,
			'type'      => 'textarea'
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_custom_css' );

// CUSTOM JS
if ( ! function_exists( 'boldthemes_customize_custom_js' ) ) {
	function boldthemes_customize_custom_js( $wp_customize ) {
		
		$wp_customize->add_setting( 'boldthemes_theme_options[custom_js]', array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options'
		));
		$wp_customize->add_control( 'custom_js', array(
			'label'     => esc_html__( 'Custom JS', 'bt-twentysixteen-customization' ),
			'section'   => 'boldthemes_section',
			'settings'  => 'boldthemes_theme_options[custom_js]',
			'priority'  => 80,
			'type'      => 'textarea'
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_custom_js' );

$theme_options = get_option( 'boldthemes_theme_options' );
if ( $theme_options ) {
	if ( ! function_exists( 'boldthemes_custom_fonts' ) ) {
		function boldthemes_custom_fonts() {
			$theme_options = get_option( 'boldthemes_theme_options' );
			echo '<style>';
				if ( isset( $theme_options[ 'body_font' ] ) && $theme_options[ 'body_font' ] != '' ) {
					echo 'body, button, input, select, textarea, .required { font-family: "' . urldecode( $theme_options[ 'body_font' ] ) . '" } ';
				}				
				
				if ( isset( $theme_options[ 'title_font' ] ) && $theme_options[ 'title_font' ] != '' ) {
					echo 'input::-webkit-input-placeholder { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
					echo 'input::-moz-placeholder { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
					echo 'input:-ms-input-placeholder { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
					echo 'input::placeholder { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
					
					echo 'button,
					button[disabled]:hover,
					button[disabled]:focus,
					input[type="button"],
					input[type="button"][disabled]:hover,
					input[type="button"][disabled]:focus,
					input[type="reset"],
					input[type="reset"][disabled]:hover,
					input[type="reset"][disabled]:focus,
					input[type="submit"],
					input[type="submit"][disabled]:hover,
					input[type="submit"][disabled]:focus { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
					
					echo '.post-password-form label,
					.main-navigation,
					.post-navigation,
					.post-navigation .post-title,
					.pagination,
					.image-navigation,
					.comment-navigation,
					.site .skip-link,
					.widget .widget-title,
					.widget_recent_entries .post-date,
					.widget_rss .rss-date,
					.widget_rss cite,
					.tagcloud a,
					.site-title,
					.entry-title,
					.entry-footer,
					.sticky-post,
					.page-title,
					.page-links,
					.comments-title,
					.comment-reply-title,
					.comment-metadata,
					.pingback .edit-link,
					.comment-reply-link,
					.comment-form label,
					.no-comments,
					.site-footer .site-title:after,
					.widecolumn label,
					.widecolumn .mu_register label { font-family: "' . urldecode( $theme_options[ 'title_font' ] ) . '"; }';
				}
				
				if ( isset( $theme_options[ 'heading_font' ] ) && $theme_options[ 'heading_font' ] != '' ) {
					echo 'h1, h2, h3, h4, h5, h6 { font-family: "' . urldecode( $theme_options[ 'heading_font' ] ) . '" } ';
				}				
				
				if ( isset( $theme_options[ 'menu_font' ] ) && $theme_options[ 'menu_font' ] != '' ) {
					echo '.primary-menu { font-family: "' . urldecode( $theme_options[ 'menu_font' ] ) . '"; } ';
				}			
			echo '</style>';
		}
	}
	add_action( 'wp_head', 'boldthemes_custom_fonts' );
	
	if ( ! function_exists( 'boldthemes_load_fonts' ) ) {
		function boldthemes_load_fonts() {
			
			$theme_options = get_option( 'boldthemes_theme_options' );
			
			$font_families = array();
			
			if ( isset( $theme_options[ 'body_font' ] ) && $theme_options[ 'body_font' ] != '' ) {
				$font_families[] = urldecode( $theme_options[ 'body_font' ] ) . ':100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic';
			}
			
			if ( isset( $theme_options[ 'title_font' ] ) && $theme_options[ 'title_font' ] != '' ) {
				$font_families[] = urldecode( $theme_options[ 'title_font' ] ) . ':100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic';
			}
			
			if ( isset( $theme_options[ 'heading_font' ] ) && $theme_options[ 'heading_font' ] != '' ) {
				$font_families[] = urldecode( $theme_options[ 'heading_font' ] ) . ':100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic';
			}			
			
			if ( isset( $theme_options[ 'menu_font' ] ) && $theme_options[ 'menu_font' ] != '' ) {
				$font_families[] = urldecode( $theme_options[ 'menu_font' ] ) . ':100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic';
			}

			if ( count( $font_families ) > 0 ) {
				$query_args = array(
					'family' => urlencode( implode( '|', $font_families ) ),
					'subset' => urlencode( 'latin,latin-ext' ),
				);
				$font_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
				wp_enqueue_style( 'boldthemes_fonts', $font_url, array(), '1.0.0' );
			}
		}
	}
	add_action( 'wp_enqueue_scripts', 'boldthemes_load_fonts' );	
}

if ( ! function_exists( 'boldthemes_post_thumbnail_sizes_attr' ) ) {
	function boldthemes_post_thumbnail_sizes_attr( $size ) {
		$template = get_page_template_slug();
		if ( $template == 'bt-no-sidebar-wide.php' ) {
			return 'full';
		} else {
			return $size;
		}
	}
}
add_filter( 'post_thumbnail_size', 'boldthemes_post_thumbnail_sizes_attr' );	

if ( ! function_exists( 'boldthemes_custom_header_args' ) ) {
	function boldthemes_custom_header_args( $arr ) {
		return array(
			'default-text-color'     => $arr['default-text-color'],
			'width'                  => 0,
			'height'                 => 280,
			'flex-height'            => true,
			'wp-head-callback'       => 'twentysixteen_header_style'
		);
	}
}
add_filter( 'twentysixteen_custom_header_args', 'boldthemes_custom_header_args' );

function boldthemes_custom_footer_credits(  ) {
    echo '<span class="bt-footer-info"><a href="http://bold-themes.com?ref=Customize2016" target="_blank">Customized by BoldThemes</a></span>';
}
 
add_action( 'twentysixteen_credits', 'boldthemes_custom_footer_credits' );

class BTPageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new BTPageTemplater();
		} 

		return self::$instance;

	} 

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		add_filter(
			'page_attributes_dropdown_pages_args',
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the save post to inject out template into the page cache
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) { // 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
			array( $this, 'register_project_templates' )
			);
		} else { // Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);
		}

		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);


		// Add your templates to this array.
		$this->templates = array(
			'bt-no-sidebar-boxed.php' => esc_html__( 'No Sidebar Boxed', 'bt-twentysixteen-customization' ),
			'bt-no-sidebar-wide.php' => esc_html__( 'No Sidebar Wide', 'bt-twentysixteen-customization' ),
			'bt-no-sidebar-bb.php' => esc_html__( 'No Sidebar Fullscreen (use with Bold Builder)', 'bt-twentysixteen-customization' )
		);
			
	} 
	
	/**
	* Adds our template to the page dropdown for v4.7+
	*
	*/
	 
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}


	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 */

	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( !isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path(__FILE__). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

} 
add_action( 'plugins_loaded', array( 'BTPageTemplater', 'get_instance' ) );