<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cnpagency.com
 * @since      1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 * @author     CNP <wordpress@cnpagency.com>
 */
class Page_For_Post_Types_Activator {

	/**
	 * Plugin activator.
	 *
	 * Runs when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$uuid = get_option( 'page_for_post_types_uuid' );
		if ( ! $uuid ) {
			$uuid = wp_generate_uuid4();
			update_option( 'page_for_post_types_uuid', $uuid, true );
		}
	}

}
