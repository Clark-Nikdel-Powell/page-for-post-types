<?php
/**
 * The admin & public-facing shared functionality of the plugin.
 *
 * @link       https://cnpagency.com
 * @version    1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 */

/**
 * The admin & public-facing shared functionality of the plugin.
 *
 * @link       https://cnpagency.com
 * @version    1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 * @author     CNP <wordpress@cnpagency.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Page_For_Post_Types_Shared
 */
class Page_For_Post_Types_Shared {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The version of this plugin.
	 */
	private $version;

	/**
	 * Page_For_Post_Types_Shared constructor.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Retrieves the UUID set on plugin activation.
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public function get_uuid() {

		return get_option( 'page_for_post_types_uuid' );
	}

	/**
	 * Returns the option setting.
	 *
	 * @param string $suffix
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_page_for( $suffix ) {

		return get_option( $this->option_name( $suffix ) );
	}

	/**
	 * Returns compiled option name for post type
	 *
	 * @param string $suffix
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function option_name( $suffix ) {

		$prefix = $this->get_uuid();

		return sprintf( '%1$s_page_for_%2$s', $prefix, $suffix );
	}

	/**
	 * Returns an array of page_for_post_types stdClass objects.
	 *
	 * @return stdClass[]
	 * @since 1.0.0
	 */
	public function get_page_for_post_type_objects() {

		$objs = [];

		$post_types = get_post_types( [ 'has_post_type_page' => true ] );

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$objs[]           = $this->add_page_for_post_type_object( $post_type_object->name, $post_type_object->label );
		}

		/**
		 * Filters the array of page_for_post_types objects
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'page_for_post_types_objects', $objs );
	}

	/**
	 * Gets the object for the post type page.
	 *
	 * @param int|\WP_Post $the_post
	 *
	 * @return bool|stdClass
	 * @since 1.0.0
	 */
	public function get_page_for_post_type_object( $the_post ) {

		$the_post = is_a( $the_post, 'WP_Post' ) ? $the_post->ID : $the_post;

		$post_type_objects = $this->get_page_for_post_type_objects();
		foreach ( $post_type_objects as $obj ) {

			if ( intval( $the_post ) === intval( $obj->id ) ) {

				return $obj;
			}
		}

		return false;
	}

	/**
	 * Generates stdClass for page_for_post_types object.
	 *
	 * @param string      $name
	 * @param string      $label
	 * @param bool        $disable_editor
	 * @param bool|string $notice
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public function add_page_for_post_type_object( $name, $label, $disable_editor = true, $notice = false ) {

		return (object) [
			'name'           => $name,
			'label'          => $label,
			'id'             => $this->get_page_for( $name ),
			'disable_editor' => $disable_editor,
			'notice'         => $notice
		];
	}

	/**
	 * Returns true is the post has been selected as a page for a post type.
	 *
	 * @param int|\WP_Post $current_post
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function is_page_for_post_types( $current_post ) {

		$current_post = is_a( $current_post, 'WP_Post' ) ? $current_post->ID : $current_post;

		$objs = $this->get_page_for_post_type_objects();
		foreach ( $objs as $obj ) {

			if ( intval( $current_post ) !== intval( $obj->id ) ) {
				continue;
			}

			/**
			 * If we're here, then we've found a page selected for a post type.
			 */
			return true;
		}

		return false;
	}

	/**
	 * Returns the relative permalink for a post.
	 *
	 * @param int|\WP_Post $post_object
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rewrite_slug( $the_post ) {

		$permalink = get_the_permalink( $the_post );
		$domain    = trailingslashit( site_url() );

		return untrailingslashit( str_replace( $domain, '', $permalink ) );
	}

}
