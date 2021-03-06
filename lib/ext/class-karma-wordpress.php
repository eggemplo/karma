<?php
/**
* class-karma-shortcodes.php
*
* Copyright (c) 2010-2012 "eggemplo" Antonio Blanco Oliva www.eggemplo.com
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
* @author Antonio Blanco Oliva
* @package karma
* @since karma 1.0
*/
class Karma_Wordpress {

	/**
	 * Add shortcodes.
	 */
	public static function init() {
		
		if ( get_option('karma-comments_enable', 1) ) {
			// comments
			add_action('wp_set_comment_status', array( __CLASS__, 'wp_set_comment_status' ), 10, 2);
			add_action('comment_post', array( __CLASS__, 'comment_post' ), 10, 2);
		}
		
		if ( get_option('karma-welcome', "0") !== "0" ) {
			add_action( 'user_register', array( __CLASS__,'user_register' ) );
		}
	}

	public static function user_register ( $user_id ) {
		Karma::setKarma( Karma::getKarma( $user_id ) + get_option('karma-welcome', 0), $user_id );
	}
	
	public static function wp_set_comment_status( $comment_id, $status ) {
		$user = get_user_by( 'email', get_comment_author_email( $comment_id ) );
		if ( $user ) {
			if ( $status == "approve" ) {
				Karma::setKarma( Karma::getKarma( $user->ID ) + get_option('karma-comments', 1), $user->ID );
			} else if ( $status == "hold" || $status == "spam" || $status == "delete" || $status == "trash" ) {
				Karma::setKarma( Karma::getKarma( $user->ID ) - get_option('karma-comments', 1), $user->ID );
			}
			
		}
	}
	
	public static function comment_post( $comment_id, $status ) {
		$user = get_user_by( 'email', get_comment_author_email( $comment_id ) );
		if ( $user ) {
			if ( $status == "1" ) {
				Karma::setKarma( Karma::getKarma( $user->ID ) + get_option('karma-comments', 1), $user->ID );
			}
		}
	}
	
		
}
Karma_Wordpress::init();
