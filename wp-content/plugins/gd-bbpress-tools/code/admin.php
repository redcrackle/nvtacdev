<?php

if (!defined('ABSPATH')) {
    exit;
}

class GDBTOAdmin {
    private $page_ids = array();
    private $admin_plugin = false;

    function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));

        add_filter('plugin_action_links', array($this, 'plugin_actions'), 10, 2);
        add_filter('plugin_row_meta', array($this, 'plugin_links'), 10, 2);

        add_action('admin_enqueue_scripts', array($this, 'enqueue_files'));
    }

    public static function instance() {
        static $instance = false;

        if ($instance === false) {
            $instance = new GDBTOAdmin();
        }

        return $instance;
    }

    public function admin_init() {
        if (isset($_GET['page'])) {
            $this->admin_plugin = $_GET['page'] == 'gdbbpress_tools';
        }

        if (isset($_POST['gdbb-tweaks-submit'])) {
            check_admin_referer('gd-bbpress-tools');

            GDBTOCore::instance()->o['tweak_disable_breadcrumbs'] = isset($_POST['tweak_disable_breadcrumbs']) ? 1 : 0;
            GDBTOCore::instance()->o['tweak_tags_in_reply_for_authors_only'] = isset($_POST['tweak_tags_in_reply_for_authors_only']) ? 1 : 0;
            GDBTOCore::instance()->o['tweak_show_lead_topic'] = isset($_POST['tweak_show_lead_topic']) ? 1 : 0;
            GDBTOCore::instance()->o['tweak_remove_private_title_prefix'] = isset($_POST['tweak_remove_private_title_prefix']) ? 1 : 0;
            GDBTOCore::instance()->o['tweak_topic_load_search_for_all_topics'] = isset($_POST['tweak_topic_load_search_for_all_topics']) ? 1 : 0;
            GDBTOCore::instance()->o['tweak_forum_load_search_for_all_forums'] = isset($_POST['tweak_forum_load_search_for_all_forums']) ? 1 : 0;

            update_option('gd-bbpress-tools', GDBTOCore::instance()->o);
            wp_redirect(add_query_arg('settings-updated', 'true'));
            exit();
        }

        if (isset($_POST['gdbb-views-submit'])) {
            check_admin_referer('gd-bbpress-tools');

            GDBTOCore::instance()->o['view_mostreplies_active'] = isset($_POST['view_mostreplies_active']) ? 1 : 0;
            GDBTOCore::instance()->o['view_latesttopics_active'] = isset($_POST['view_latesttopics_active']) ? 1 : 0;
            GDBTOCore::instance()->o['view_topicsfreshness_active'] = isset($_POST['view_topicsfreshness_active']) ? 1 : 0;

            update_option('gd-bbpress-tools', GDBTOCore::instance()->o);
            wp_redirect(add_query_arg('settings-updated', 'true'));
            exit();
        }

        if (isset($_POST['gdbb-bbcode-submit'])) {
            check_admin_referer('gd-bbpress-tools');

            $all_bbcodes = array(
                'b', 'u', 'i', 's', 'center', 'right', 'left', 'justify', 'sub', 'sup', 'pre',
                'reverse', 'list', 'ol', 'ul', 'li', 'blockquote', 'div', 'area', 'border',
                'hr', 'size', 'color', 'quote', 'url', 'google', 'youtube', 'vimeo', 'img',
                'note');

            $deactivate = array();
            $active = isset($_POST['bbcode_activated']) ? (array)$_POST['bbcode_activated'] : array();

            foreach ($all_bbcodes as $bbc) {
                if (!in_array($bbc, $active)) {
                    $deactivate[] = $bbc;
                }
            }

            GDBTOCore::instance()->o['bbcodes_active'] = isset($_POST['bbcodes_active']) ? 1 : 0;
            GDBTOCore::instance()->o['bbcodes_notice'] = isset($_POST['bbcodes_notice']) ? 1 : 0;
            GDBTOCore::instance()->o['bbcodes_bbpress_only'] = isset($_POST['bbcodes_bbpress_only']) ? 1 : 0;
            GDBTOCore::instance()->o['bbcodes_special_super_admin'] = isset($_POST['bbcodes_special_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['bbcodes_special_roles'] = d4p_sanitize_basic_array((array)$_POST['bbcodes_special_roles']);
            GDBTOCore::instance()->o['bbcodes_special_action'] = d4p_sanitize_basic($_POST['bbcodes_special_action']);
            GDBTOCore::instance()->o['bbcodes_deactivated'] = $deactivate;

            update_option('gd-bbpress-tools', GDBTOCore::instance()->o);
            wp_redirect(add_query_arg('settings-updated', 'true'));
            exit();
        }

        if (isset($_POST['gdbb-tools-submit'])) {
            check_admin_referer('gd-bbpress-tools');

            GDBTOCore::instance()->o['include_always'] = isset($_POST['include_always']) ? 1 : 0;
            GDBTOCore::instance()->o['allowed_tags_div'] = isset($_POST['allowed_tags_div']) ? 1 : 0;
            GDBTOCore::instance()->o['kses_allowed_override'] = d4p_sanitize_basic($_POST['kses_allowed_override']);
            GDBTOCore::instance()->o['quote_active'] = isset($_POST['quote_active']) ? 1 : 0;
            GDBTOCore::instance()->o['quote_location'] = d4p_sanitize_basic($_POST['quote_location']);
            GDBTOCore::instance()->o['quote_method'] = d4p_sanitize_basic($_POST['quote_method']);
            GDBTOCore::instance()->o['quote_super_admin'] = isset($_POST['quote_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['quote_roles'] = d4p_sanitize_basic_array((array)$_POST['quote_roles']);
            GDBTOCore::instance()->o['toolbar_active'] = isset($_POST['toolbar_active']) ? 1 : 0;
            GDBTOCore::instance()->o['toolbar_super_admin'] = isset($_POST['toolbar_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['toolbar_roles'] = d4p_sanitize_basic_array((array)$_POST['toolbar_roles']);
            GDBTOCore::instance()->o['admin_disable_active'] = isset($_POST['admin_disable_active']) ? 1 : 0;
            GDBTOCore::instance()->o['admin_disable_super_admin'] = isset($_POST['admin_disable_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['admin_disable_roles'] = d4p_sanitize_basic_array((array)$_POST['admin_disable_roles']);
            GDBTOCore::instance()->o['signature_active'] = isset($_POST['signature_active']) ? 1 : 0;
            GDBTOCore::instance()->o['signature_length'] = intval($_POST['signature_length']);
            GDBTOCore::instance()->o['signature_super_admin'] = isset($_POST['signature_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['signature_roles'] = d4p_sanitize_basic_array((array)$_POST['signature_roles']);
            GDBTOCore::instance()->o['signature_method'] = d4p_sanitize_basic($_POST['signature_method']);
            GDBTOCore::instance()->o['signature_enhanced_super_admin'] = isset($_POST['signature_enhanced_super_admin']) ? 1 : 0;
            GDBTOCore::instance()->o['signature_enhanced_roles'] = d4p_sanitize_basic_array((array)$_POST['signature_enhanced_roles']);
            GDBTOCore::instance()->o['signature_buddypress_profile_group'] = d4p_sanitize_basic($_POST['signature_buddypress_profile_group']);

            update_option('gd-bbpress-tools', GDBTOCore::instance()->o);
            wp_redirect(add_query_arg('settings-updated', 'true'));
            exit();
        }
    }

    public function enqueue_files() {
        if ($this->admin_plugin) {
            wp_enqueue_style('gd-bbpress-tools', GDBBPRESSTOOLS_URL."css/admin.css", array(), GDBBPRESSTOOLS_VERSION);
        }
    }

    public function admin_menu() {
        $this->page_ids[] = add_submenu_page('edit.php?post_type=forum', 'GD bbPress Tools', __("Tools", "gd-bbpress-tools"), GDBBPRESSTOOLS_CAP, 'gdbbpress_tools', array($this, 'menu_tools'));

        $this->admin_load_hooks();
    }

    public function admin_load_hooks() {
        foreach ($this->page_ids as $id) {
            add_action('load-'.$id, array($this, 'load_admin_page'));
        }
    }

    public function plugin_actions($links, $file) {
        if ($file == 'gd-bbpress-tools/gd-bbpress-tools.php') {
            $settings_link = '<a href="edit.php?post_type=forum&page=gdbbpress_tools">'.__("Settings", "gd-bbpress-tools").'</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    function plugin_links($links, $file) {
        if ($file == 'gd-bbpress-tools/gd-bbpress-tools.php') {
            $links[] = '<a target="_blank" style="color: #cc0000; font-weight: bold;" href="https://plugins.dev4press.com/gd-bbpress-toolbox/">'.__("Upgrade to GD bbPress Toolbox Pro", "gd-bbpress-tools").'</a>';
        }

        return $links;
    }

    public function load_admin_page() {
        $screen = get_current_screen();

        $screen->set_help_sidebar('
            <p><strong>Dev4Press:</strong></p>
            <p><a target="_blank" href="https://www.dev4press.com/">'.__("Website", "gd-bbpress-tools").'</a></p>
            <p><a target="_blank" href="https://twitter.com/milangd">'.__("On Twitter", "gd-bbpress-tools").'</a></p>
            <p><a target="_blank" href="https://facebook.com/dev4press">'.__("On Facebook", "gd-bbpress-tools").'</a></p>');

        $screen->add_help_tab(array(
            "id" => "gdpt-screenhelp-help",
            "title" => __("Get Help", "gd-bbpress-tools"),
            "content" => '<h5>'.__("General Plugin Information", "gd-bbpress-tools").'</h5>
                <p><a href="https://plugins.dev4press.com/gd-bbpress-tools/" target="_blank">'.__("Home Page on Dev4Press.com", "gd-bbpress-tools").'</a> | 
                <a href="https://wordpress.org/plugins/gd-bbpress-tools/" target="_blank">'.__("Home Page on WordPress.org", "gd-bbpress-tools").'</a></p> 
                <h5>'.__("Getting Plugin Support", "gd-bbpress-tools").'</h5>
                <p><a href="https://support.dev4press.com/forums/forum/plugins-free/gd-bbpress-tools/" target="_blank">'.__("Support Forum on Dev4Press.com", "gd-bbpress-tools").'</a> | 
                <a href="https://wordpress.org/support/plugin/gd-bbpress-tools" target="_blank">'.__("Support Forum on WordPress.org", "gd-bbpress-tools").'</a></p>'));

        $screen->add_help_tab(array(
            "id" => "gdpt-screenhelp-website",
            "title" => "Dev4Press", "sfc",
            "content" => '<p>'.__("On Dev4Press website you can find many useful plugins, themes and tutorials, all for WordPress. Please, take a few minutes to browse some of these resources, you might find some of them very useful.", "gd-bbpress-tools").'</p>
                <p><a href="https://plugins.dev4press.com/" target="_blank"><strong>'.__("Plugins", "gd-bbpress-tools").'</strong></a> - '.__("We have more than 10 plugins available, some of them are commercial and some are available for free.", "gd-bbpress-tools").'</p>
                <p><a href="https://support.dev4press.com/kb/" target="_blank"><strong>'.__("Knowledge Base", "gd-bbpress-tools").'</strong></a> - '.__("Premium and free tutorials for our plugins themes, and many general and practical WordPress tutorials.", "gd-bbpress-tools").'</p>
                <p><a href="https://support.dev4press.com/forums/" target="_blank"><strong>'.__("Support Forums", "gd-bbpress-tools").'</strong></a> - '.__("Premium support forum for all with valid licenses to get help. Also, report bugs and leave suggestions.", "gd-bbpress-tools").'</p>'));
    }

    public function menu_tools() {
        $options = GDBTOCore::instance()->o;
        $_user_roles = d4p_bbpress_get_user_roles();

        include(GDBBPRESSTOOLS_PATH.'forms/panels.php');
    }
}
