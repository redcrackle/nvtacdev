<?php
/**
 * Mapplic Admin
 * Version: 5.0.2
 */

if (!class_exists('MapplicAdmin')) :

class MapplicAdmin {

	public function __construct() {
		// Actions
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
		add_action('init', array($this, 'create_post_type'));
		add_action('manage_mapplic_map_posts_custom_column' , array($this, 'column_shortcode'), 10, 2);
		add_action('edit_form_after_editor', array($this, 'backend_map'));
		add_action('add_meta_boxes_mapplic_map', array($this, 'metaboxes'));
		add_action('in_admin_footer', array($this, 'mapplic_logo'));
		add_action('plugins_loaded', array($this, 'activation'));

		// Filters
		add_filter('upload_mimes', array($this, 'mime_types'));
		add_filter('manage_edit-mapplic_map_columns', array($this, 'add_column_shortcode'));
		add_filter('wp_insert_post_data', array($this, 'save_map'), 99, 2);

		// Includes
		include('maps.php');
		include('metaboxes.php');
	}

	public function mapplic_logo() {
		if (get_post_type() == 'mapplic_map') {
			echo '<a class="mapplic-logo" href="//www.mapplic.com" target="_blank"><img src="' . plugins_url('../images/logo.svg', __FILE__) . '"></a><br>';
		}
	}

	public function enqueue_scripts_styles() {
		if (get_post_type() == 'mapplic_map') {
			// Disable autosave
			wp_dequeue_script('autosave');

			// Media uploader
			wp_enqueue_media();

			// Admin style
			wp_register_style('mapplic-style', plugins_url('css/mapplic-admin.css', __FILE__), false, Mapplic::$version);
			wp_enqueue_style('mapplic-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css', array('mapplic-style'), null);
			wp_enqueue_style('wp-color-picker');

			// Admin scripts
			wp_register_script('mousewheel', plugins_url('../js/jquery.mousewheel.js', __FILE__), false, null);
			wp_enqueue_script('mapplic-admin', plugins_url('js/mapplic-admin.js', __FILE__), array('jquery', 'mousewheel'), null, true);
			wp_enqueue_script('mapplic-admin-script', plugins_url('js/admin-script.js', __FILE__), array('jquery', 'wp-color-picker'), null);
			$mapplic_localization = array(
				'add' => __('Add', 'mapplic'),
				'save' => __('Save', 'mapplic'),
				'search' => __('Search', 'mapplic'),
				'not_found' => __('Nothing found. Please try a different search.', 'mapplic'),
				'map' => __('Map', 'mapplic'),
				'raw' => __('Raw', 'mapplic'),
				'missing_id' => __('Landmark ID is required and must be unique!', 'mapplic')
			);
			wp_localize_script('mapplic-admin-script', 'mapplic_localization', $mapplic_localization);
		}
	}

	public function mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		$mimes['csv'] = 'text/csv';
		return $mimes;
	}

	public function create_post_type() {
		$labels = array(
			'name' => __('Maps', 'mapplic'),
			'singular_name' => __('Map', 'mapplic'),
			'add_new_item' => __('Add New Map', 'mapplic'),
			'new_item' => __('New Map', 'mapplic'),
			'edit_item' => __('Edit Map', 'mapplic')
		);

		register_post_type('mapplic_map',
			array(
				'labels' => $labels,
				'show_in_menu' => true,
				'show_ui' => true,
				'hierarchical' => false,
				'menu_position' => 25,
				'menu_icon' => 'dashicons-location-alt',
				'public' => false,
				'exclude_from_search' => true,
				'show_in_nav_menus' => false,
				'has_archive' => false,
				'rewrite' => array('slug' => 'map'),
				'supports' => array ('title')
			)
		);
	}

	public static function activation() {
		if (!get_option('mapplic-version')) {
			// First Activation
			mapplic_restore_old_maps();
			mapplic_add_example_maps();
			add_option('mapplic-version', Mapplic::$version);
		}
	}

	// Column Shortcode
	public function column_shortcode($column, $post_id) {
		if ($column == 'shortcode') echo '[mapplic id="' . $post_id . '"]';
	}

	public function add_column_shortcode($columns) {
		$new_columns = array();
		foreach ($columns as $key => $title) {
			if ($key == 'date') $new_columns['shortcode'] = __('Shortcode', 'mapplic');
			$new_columns[$key] = $title;
		}
		return $new_columns;
	}

	// Map edit
	public function backend_map($post) {
		if ($post->post_type == 'mapplic_map') {
			$mapdata = htmlentities($post->post_content, ENT_QUOTES, 'UTF-8');
			echo '<div class="mapplic-rawedit"><label class="right"><input type="checkbox" id="mapplic-indent"></input>' . __('Indent', 'mapplic') . '</label>';
			echo '<textarea name="mapplic-mapdata" id="mapplic-mapdata" rows="20" spellcheck="false">' . $mapdata . '</textarea></div>';
			$screen = get_current_screen();
			if ($screen->action != 'add') {
				echo '<div id="mapplic-admin-map" data-mapdata="' . $mapdata . '"></div>';
				submit_button();
				echo '<input type="button" id="mapplic-editmode" class="button" value="' . __('Raw', 'mapplic') .'">';
			}
			else mapplic_new_map_type();
		}
	}

	public function metaboxes($post) {
		$screen = get_current_screen();
		if ($screen->action != 'add') {
			add_meta_box('landmark', __('Location', 'mapplic'), 'mapplic_landmark_box', 'mapplic_map', 'side', 'core');
			add_meta_box('floors', __('Floors', 'mapplic'), 'mapplic_floors_box', 'mapplic_map', 'side', 'core');
			add_meta_box('categories', __('Groups', 'mapplic'), 'mapplic_categories_box', 'mapplic_map', 'side', 'core');
			add_meta_box('geoposition', __('Geoposition', 'mapplic'), 'mapplic_geoposition_box', 'mapplic_map', 'side', 'core');
			add_meta_box('settings', __('Settings', 'mapplic'), 'mapplic_settings_box', 'mapplic_map', 'normal', 'core');
			remove_meta_box('submitdiv', 'mapplic_map', 'side');
		}
	}

	public function save_map($data, $postarr) {
		if (!isset($postarr['ID']) || !$postarr['ID']) return $data;
		if (($data['post_type'] == 'mapplic_map') && ($data['post_status'] != 'trash')) {
			if (isset($_POST['new-map-type'])) $data['post_content'] = mapplic_map_type($_POST['new-map-type']); // New
			else if (isset($_POST['mapplic-mapdata'])) $data['post_content'] = $_POST['mapplic-mapdata'];
		}
		return $data;
	}
}

endif;
?>