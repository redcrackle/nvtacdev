<?php

if (!defined('ABSPATH')) {
    exit;
}

class GDBTOFront {
    function __construct() {
        $this->register_scripts_and_styles();

        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
    }

    public static function instance() {
        static $instance = false;

        if ($instance === false) {
            $instance = new GDBTOFront();
        }

        return $instance;
    }

    public function register_scripts_and_styles() {
        $debug = defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG : false;
        $files = 'front'.($debug ? '' : '.min');

        wp_register_style('gdbto-front', GDBBPRESSTOOLS_URL.'css/'.$files.'.css', array(), GDBBPRESSTOOLS_VERSION);
        wp_register_script('gdbto-front', GDBBPRESSTOOLS_URL.'js/'.$files.'.js', array('jquery'), GDBBPRESSTOOLS_VERSION, true);
    }

    public function include_scripts_and_styles() {
        wp_enqueue_style('gdbto-front');
        wp_enqueue_script('gdbto-front');

        wp_localize_script('gdbto-front', 'gdbbPressToolsInit', array(
            'quote_method' => d4p_bbt_o('quote_method'),
            'quote_wrote' => _x("wrote", "Username quote suffix", "gd-bbpress-tools"),
            'wp_editor' => d4p_bbpress_version() > 20 ? (bbp_use_wp_editor() ? 1 : 0) : 0
        ));
    }

    public function wp_enqueue_scripts() {
        if (d4p_bbt_o('include_always') == 1 || d4p_is_bbpress()) {
            $this->include_scripts_and_styles();
        }
    }
}
