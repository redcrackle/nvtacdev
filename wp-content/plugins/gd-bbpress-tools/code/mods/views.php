<?php

if (!defined('ABSPATH')) {
    exit;
}

class GDBTOModViews {
    public $views;

    function __construct($views) {
        $this->views = $views;

        add_action('bbtoolbox_core', array($this, 'register_views'));
    }

    public function register_views() {
        foreach ($this->views as $view => $args) {
            if ($args['active'] == 1) {
                $fnc = '_view_'.$view;
                $this->$fnc($args);
            }
        }
    }

    private function _view_mostreplies($args) {
        bbp_register_view(
            'most-replies',
            __("Topics with most replies", "gd-bbpress-tools"),
            array('meta_key' => '_bbp_reply_count', 'orderby' => 'meta_value_num'),
            false);
    }

    private function _view_latesttopics($args) {
        bbp_register_view(
            'latest-topics',
            __("Latest topics", "gd-bbpress-tools"),
            array('orderby' => 'post_date'),
            false);
    }

    private function _view_topicsfreshness($args) {
        bbp_register_view(
            'topics-freshness',
            __("Topics Freshness", "gd-bbpress-tools"),
            array('orderby' => 'meta_value',
                'order' => 'DESC',
                'meta_key' => '_bbp_last_active_time',
                'post_status' => array(bbp_get_public_status_id(), bbp_get_closed_status_id()),
                'post_type' => bbp_get_topic_post_type()),
            false);
    }
}
