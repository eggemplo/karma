<?php
/**
 * karma.php
 *
 * Copyright (c) 2011,2012 Antonio Blanco http://www.blancoleon.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco	
 * @package karma
 * @since karma 1.0
 *
 * Plugin Name: Karma
 * Plugin URI: http://www.eggemplo.com/plugins/karma
 * Description: Keep Karma and carry on.
 * Version: 1.0
 * Author: eggemplo
 * Author URI: http://www.eggemplo.com
 * License: GPLv3
 */

define( 'KARMA_DOMAIN', 'karma' );

define( 'KARMA_FILE', __FILE__ );
define( 'KARMA_PLUGIN_BASENAME', plugin_basename( KARMA_FILE ) );

if ( !defined( 'KARMA_CORE_DIR' ) ) {
	define( 'KARMA_CORE_DIR', WP_PLUGIN_DIR . '/karma' );
}
if ( !defined( 'KARMA_CORE_LIB' ) ) {
	define( 'KARMA_CORE_LIB', KARMA_CORE_DIR . '/lib/core' );
}

if ( !defined( 'KARMA_CORE_LIB_EXT' ) ) {
	define( 'KARMA_CORE_LIB_EXT', KARMA_CORE_DIR . '/lib/ext' );
}

define( 'KARMA_PLUGIN_URL', plugin_dir_url( KARMA_FILE ) );

define( 'KARMA_DEFAULT_KARMA_LABEL', 'karma' );

require_once ( KARMA_CORE_LIB . '/class-karma.php' );
require_once ( KARMA_CORE_LIB . '/class-karma-database.php' );
require_once ( KARMA_CORE_LIB . '/class-karma-shortcodes.php' );
require_once ( KARMA_CORE_LIB . '/class-karma-widget.php' );
require_once ( KARMA_CORE_LIB . '/class-karma-admin.php' );

require_once ( KARMA_CORE_LIB_EXT . '/class-karma-wordpress.php' );


class Karma_Plugin {
	
	private static $notices = array();
	
	public static function init() {
			
		load_plugin_textdomain( KARMA_DOMAIN, null, 'karma/languages' );
		
		register_activation_hook( KARMA_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( KARMA_FILE, array( __CLASS__, 'deactivate' ) );
		
		register_uninstall_hook( KARMA_FILE, array( __CLASS__, 'uninstall' ) );
		
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		
		add_action( 'widgets_init', array( __CLASS__,'karma_widgets_init' ) );
		
	}
	
	public static function wp_init() {
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'karma_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'karma_admin_enqueue_scripts' ) );
		
		Karma_Admin::init();
		
	}
	
	public static function karma_admin_enqueue_scripts() {
		wp_register_style( 'karma-admin-css', KARMA_PLUGIN_URL . 'css/karma-admin.css' );
		wp_enqueue_style ('karma-admin-css');
	
	}
	public static function karma_enqueue_scripts() {
		wp_register_style( 'karma-css', KARMA_PLUGIN_URL . 'css/karma.css' );
		wp_enqueue_style ('karma-css');
	
	}
	
	public static function karma_widgets_init() {
		register_widget( 'Karma_Widget' );
	}
	
	
	/**
	 * Plugin activation work.
	 * 
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		// create tables
		
		$karma_users_table = Karma_Database::karma_get_table("users");
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$karma_users_table'" ) != $karma_users_table ) {
			$queries[] = "CREATE TABLE $karma_users_table (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			karma   INT(11) DEFAULT 0,
			PRIMARY KEY   (user_id)
			) $charset_collate;";
		}
		if ( !empty( $queries ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $queries );
		}
	}
	
	/**
	 * Plugin deactivation.
	 *
	 */
	public static function deactivate() {
		
	}

	/**
	 * Plugin uninstall. Delete database table.
	 *
	 */
	public static function uninstall() {
	
	 	global $wpdb;
	
		$wpdb->query('DROP TABLE IF EXISTS ' . Karma_Database::karma_get_table("users") );
	
	}
	
	
}
Karma_Plugin::init();

