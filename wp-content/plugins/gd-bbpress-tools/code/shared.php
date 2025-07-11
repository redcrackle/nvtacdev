<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'gdbbp_Error' ) ) {
	class gdbbp_Error {
		var $errors = array();

		function __construct() {
		}

		function add( $code, $message, $data ) {
			$this->errors[ $code ][] = array( $message, $data );
		}
	}
}

if ( ! function_exists( 'd4p_bbpress_get_user_roles' ) ) {
	/**
	 * Get valid roles for forums based on bbPress version
	 *
	 * @return array list of roles
	 */
	function d4p_bbpress_get_user_roles() : array {
		$roles = array();

		if ( d4p_bbpress_version() < 22 ) {
			global $wp_roles;

			foreach ( $wp_roles->role_names as $role => $title ) {
				$roles[ $role ] = $title;
			}
		} else {
			$dynamic_roles = bbp_get_dynamic_roles();

			foreach ( $dynamic_roles as $role => $obj ) {
				$roles[ $role ] = $obj['name'];
			}
		}

		return $roles;
	}
}

if ( ! function_exists( 'd4p_has_bbpress' ) ) {
	function d4p_has_bbpress() : bool {
		if ( function_exists( 'bbp_version' ) ) {
			$version = bbp_get_version();
			$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

			return $version > 22;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'd4p_bbpress_version' ) ) {
	/**
	 * Get version of the bbPress.
	 *
	 * @param string $ret what version format to return: code or version
	 *
	 * @return mixed version value
	 */
	function d4p_bbpress_version( $ret = 'code' ) {
		if ( ! d4p_has_bbpress() ) {
			return null;
		}

		$version = bbp_get_version();

		if ( isset( $version ) ) {
			if ( $ret == 'code' ) {
				return substr( str_replace( '.', '', $version ), 0, 2 );
			} else {
				return $version;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'd4p_is_bbpress' ) ) {
	/**
	 * Check if the current page is forum, topic or other bbPress page.
	 *
	 * @return bool true if the current page is the forum related
	 */
	function d4p_is_bbpress() : bool {
		$is = d4p_has_bbpress() && is_bbpress();

		return apply_filters( 'd4p_is_bbpress', $is );
	}
}

if ( ! function_exists( 'd4p_is_user_moderator' ) ) {
	/**
	 * Checks to see if the currently logged user is moderator.
	 *
	 * @return bool is user moderator or not
	 */
	function d4p_is_user_moderator() : bool {
		global $current_user;

		if ( is_array( $current_user->roles ) ) {
			return in_array( 'bbp_moderator', $current_user->roles );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'd4p_is_user_admin' ) ) {
	/**
	 * Checks to see if the currently logged user is administrator.
	 *
	 * @return bool is user administrator or not
	 */
	function d4p_is_user_admin() : bool {
		global $current_user;

		if ( is_array( $current_user->roles ) ) {
			return in_array( 'administrator', $current_user->roles );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'd4p_bbp_is_role' ) ) {
	function d4p_bbp_is_role( $setting_name ) : bool {
		$allowed = false;

		if ( current_user_can( 'd4p_bbpt_' . $setting_name ) ) {
			$allowed = true;
		} else if ( is_super_admin() ) {
			$allowed = GDBTOCore::instance()->o[ $setting_name . '_super_admin' ] == 1;
		} else if ( is_user_logged_in() ) {
			$roles = isset( GDBTOCore::instance()->o[ $setting_name . '_roles' ] ) ? GDBTOCore::instance()->o[ $setting_name . '_roles' ] : null;

			if ( is_null( $roles ) ) {
				$allowed = true;
			} else if ( is_array( $roles ) ) {
				global $current_user;

				if ( is_array( $current_user->roles ) ) {
					$matched = array_intersect( $current_user->roles, $roles );
					$allowed = ! empty( $matched );
				}
			}
		}

		return $allowed;
	}
}

if ( ! function_exists( 'd4p_url_campaign_tracking' ) ) {
	function d4p_url_campaign_tracking( $url, $campaign = '', $medium = '', $content = '', $term = '', $source = null ) {
		if ( ! empty( $campaign ) ) {
			$url = add_query_arg( 'utm_campaign', $campaign, $url );
		}

		if ( ! empty( $medium ) ) {
			$url = add_query_arg( 'utm_medium', $medium, $url );
		}

		if ( ! empty( $content ) ) {
			$url = add_query_arg( 'utm_content', $content, $url );
		}

		if ( ! empty( $term ) ) {
			$url = add_query_arg( 'utm_term', $term, $url );
		}

		if ( is_null( $source ) ) {
			$source = parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );
		}

		if ( ! empty( $source ) ) {
			$url = add_query_arg( 'utm_source', $source, $url );
		}

		return $url;
	}
}

if ( ! function_exists( 'd4p_bbp_update_shorthand_bbcodes' ) ) {
	function d4p_bbp_update_shorthand_bbcodes( $content ) {
		$bbcodes = array( 'quote', 'url', 'size', 'color', 'area', 'anchor', 'img', 'youtube', 'vimeo' );

		foreach ( $bbcodes as $bbc ) {
			if ( strpos( $content, '[' . $bbc . '=' ) !== false ) {
				$content = str_replace( '[' . $bbc . '=', '[' . $bbc . ' ' . $bbc . '=', $content );
			}
		}

		return $content;
	}
}

function d4p_bbt_o( $name ) {
	return GDBTOCore::instance()->o[ $name ];
}

function d4p_bbt_is_bbpress_post_type( $post_type ) : bool {
	if (
		in_array( $post_type, array(
			bbp_get_forum_post_type(),
			bbp_get_topic_post_type(),
			bbp_get_reply_post_type(),
		) ) ) {
		return true;
	} else {
		return false;
	}
}
