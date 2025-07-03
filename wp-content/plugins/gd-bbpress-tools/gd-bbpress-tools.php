<?php

/**
 * Plugin Name:       GD bbPress Tools
 * Plugin URI:        https://www.dev4press.com/plugins/gd-bbpress-tools/
 * Description:       Adds different expansions and tools to the bbPress plugin powered forums: BBCode support, signatures, various tweaks, custom views, quote...
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       gd-bbpress-tools
 * Version:           3.5.3
 * Requires at least: 5.9
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * Requires Plugins:  bbpress
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

if ( ! defined( 'GDBBPRESSTOOLS_CAP' ) ) {
	define( 'GDBBPRESSTOOLS_CAP', 'activate_plugins' );
}

require_once dirname( __FILE__ ) . '/code/defaults.php';
require_once dirname( __FILE__ ) . '/code/shared.php';
require_once dirname( __FILE__ ) . '/code/sanitize.php';

require_once dirname( __FILE__ ) . '/code/class.php';

GDBTOCore::instance();
