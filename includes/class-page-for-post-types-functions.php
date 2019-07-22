<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Page_For_Post_Types_Functions {

	/**
	 * Retrieves the UUID set on plugin activation.
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public static function get_uuid() {

		return get_option( 'page_for_post_types_uuid' );
	}

	/**
	 * Returns compiled option name for post type
	 *
	 * @param string $suffix
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function option_name( $suffix ) {

		$prefix = Page_For_Post_Types_Functions::get_uuid();

		return sprintf( '%1$s_page_for_%2$s', $prefix, $suffix );
	}

	/**
	 * Returns the option setting.
	 *
	 * @param string $suffix
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_page_for( $suffix ) {

		return get_option( Page_For_Post_Types_Functions::option_name( $suffix ) );
	}

	/**
	 * Generates stdClass for page_for_post_types object.
	 *
	 * @param string      $name
	 * @param string      $label
	 * @param int         $id
	 * @param bool        $disable_editor
	 * @param bool|string $notice
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public static function add_page_for_post_type_object( $name, $label, $id = 0, $disable_editor = true, $notice = false ) {

		$id = 0 === intval( $id ) ? self::get_page_for( $name ) : $id;

		return (object) [ 'name' => $name, 'label' => $label, 'id' => $id, $disable_editor, $notice ];
	}

}