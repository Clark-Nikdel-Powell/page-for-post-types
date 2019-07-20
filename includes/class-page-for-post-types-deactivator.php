<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://cnpagency.com
 * @since      1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 * @author     CNP <wordpress@cnpagency.com>
 */
class Page_For_Post_Types_Deactivator {

	/**
	 * Plugin deactivator.
	 *
	 * Runs when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		flush_rewrite_rules();

	}

}
