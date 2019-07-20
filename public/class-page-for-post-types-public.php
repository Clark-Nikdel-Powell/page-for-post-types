<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cnpagency.com
 * @since      1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/public
 * @author     CNP <wordpress@cnpagency.com>
 */
class Page_For_Post_Types_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Filter post tpye registrations to set the rewrite slug to the selected page.
	 *
	 * @param mixed[] $args
	 * @param string  $post_type_name
	 *
	 * @return mixed[]
	 * @since 1.0.0
	 */
	public function filter_post_type_args( $args, $post_type_name ) {

		if ( isset( $args['_builtin'] ) && $args['_builtin'] ) {

			return $args;
		}

		if ( isset( $_GET['action'] ) && 'deactivate' === $_GET['action'] ) {

			return $args;
		}

		if ( isset( $args['has_post_type_page'] ) && $args['has_post_type_page'] ) {

			$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

			$page_for = $shared->get_page_for( $post_type_name );
			if ( ! $page_for ) {

				return $args;
			}

			$args['rewrite'] = [
				'slug'       => $shared->get_rewrite_slug( $page_for ),
				'with_front' => false,
			];
			//TODO: DO we need this setting?
			$args['post_type_page_id'] = $page_for;

		}

		return $args;
	}

}
