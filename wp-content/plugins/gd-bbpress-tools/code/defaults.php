<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GDBTODefaults {
	var $default_options = array(
		'version'                                => '3.5.3',
		'date'                                   => '2024.08.19.',
		'build'                                  => 2450,
		'status'                                 => 'stable',
		'product_id'                             => 'gd-bbpress-tools',
		'edition'                                => 'free',
		'revision'                               => 0,
		'include_always'                         => 0,
		'toolbar_active'                         => 1,
		'toolbar_super_admin'                    => 1,
		'toolbar_roles'                          => null,
		'allowed_tags_div'                       => 1,
		'kses_allowed_override'                  => 'bbpress',
		'tweak_tags_in_reply_for_authors_only'   => 0,
		'tweak_show_lead_topic'                  => 0,
		'tweak_disable_breadcrumbs'              => 0,
		'tweak_remove_private_title_prefix'      => 0,
		'tweak_topic_load_search_for_all_topics' => 0,
		'tweak_forum_load_search_for_all_forums' => 0,
		'admin_disable_active'                   => 0,
		'admin_disable_super_admin'              => 1,
		'admin_disable_roles'                    => null,
		'quote_active'                           => 1,
		'quote_location'                         => 'header',
		'quote_method'                           => 'bbcode',
		'quote_super_admin'                      => 1,
		'quote_roles'                            => null,
		'bbcodes_active'                         => 1,
		'bbcodes_notice'                         => 1,
		'bbcodes_bbpress_only'                   => 1,
		'bbcodes_special_super_admin'            => 1,
		'bbcodes_special_roles'                  => null,
		'bbcodes_special_action'                 => 'info',
		'bbcodes_deactivated'                    => array(),
		'signature_active'                       => 1,
		'signature_length'                       => 512,
		'signature_super_admin'                  => 1,
		'signature_roles'                        => null,
		'signature_method'                       => 'bbcode',
		'signature_enhanced_super_admin'         => 1,
		'signature_enhanced_roles'               => null,
		'signature_buddypress_profile_group'     => 1,
		'view_mostreplies_active'                => 1,
		'view_latesttopics_active'               => 1,
		'view_topicsfreshness_active'            => 1,
	);

	var $capabilities = array(
		'd4p_bbpt_toolbar',
		'd4p_bbpt_admin_disable',
		'd4p_bbpt_quote',
		'd4p_bbpt_bbcodes_special',
		'd4p_bbpt_signature',
		'd4p_bbpt_signature_enhanced',
	);

	public function __construct() {
	}
}
