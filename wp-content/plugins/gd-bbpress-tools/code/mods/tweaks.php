<?php

if (!defined('ABSPATH')) {
    exit;
}

class GDBTOModTweaks {
    public function __construct() {
        if (d4p_bbt_o('tweak_tags_in_reply_for_authors_only') == 1) {
            add_action('bbp_theme_before_reply_form', array($this, 'theme_before_reply_form'));
        }

        if (d4p_bbt_o('tweak_show_lead_topic')) {
            add_filter('bbp_show_lead_topic', '__return_true', 10000);
        }

        if (d4p_bbt_o('tweak_disable_breadcrumbs')) {
            add_filter('bbp_no_breadcrumb', '__return_true');
        }

        if (d4p_bbt_o('tweak_topic_load_search_for_all_topics')) {
            add_action('bbp_template_before_single_topic', array($this, 'load_seach_form_template'));
        }

        if (d4p_bbt_o('tweak_forum_load_search_for_all_forums')) {
            add_action('bbp_template_before_single_forum', array($this, 'load_seach_form_template'));
        }

        if (!is_admin()) {
            if (d4p_bbt_o('tweak_remove_private_title_prefix')) {
                add_filter('private_title_format', array($this, 'private_title_format'), 10, 2);
            }
        }
    }

    public function theme_before_reply_form() {
        $topic_id = bbp_get_topic_id();

        if (get_current_user_id() != bbp_get_topic_author_id($topic_id)) {
            add_filter('bbp_allow_topic_tags', '__return_false', 10000);
        }
    }

    public function private_title_format($prefix, $post) {
        if (d4p_bbt_is_bbpress_post_type($post->post_type)) {
            $prefix = '%s';
        }

        return $prefix;
    }

    public function load_seach_form_template() {
        if (bbp_allow_search()) : ?>
            <div class="bbp-search-form">
                <?php bbp_get_template_part('form', 'search'); ?>
            </div>
        <?php endif;
    }
}
