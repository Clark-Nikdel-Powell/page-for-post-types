<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cnpagency.com
 * @since      1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/admin
 * @author     CNP <wordpress@cnpagency.com>
 */
class Page_For_Post_Types_Admin {

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Add settings fields.
	 *
	 * @since 1.0.0
	 */
	public function add_setting_section() {

		$this->register_settings();

		$page_id       = 'pages_for_custom_post_types';
		$page_title    = __( 'Pages for Custom Post Types', 'page-for-post-types' );
		$page_callback = [ $this, 'page_for_custom_post_types_settings' ];
		$page          = 'reading';

		add_settings_section( $page_id, $page_title, $page_callback, $page );

	}

	public function page_for_custom_post_types_settings() {

		$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

		$post_types = $shared->get_page_for_post_type_objects();
		?>
        <p><?php printf( wp_kses( __( 'Set pages for custom post types. <a href="%s">Rebuild permalinks</a> after making any changes.', 'lcs-core' ), [ 'a' => [ 'href' => [] ] ] ), esc_url( admin_url( 'options-permalink.php' ) ) ); ?></p>
        <table class="form-table">
            <tbody>
			<?php
			foreach ( $post_types as $post_type ) {

				$option_name = $shared->option_name( $post_type->name );
				?>
                <tr>
                    <th scope="row"><?php printf( __( 'Page for %s', 'lcs-core' ), $post_type->label ); ?></th>
                    <td><?php wp_dropdown_pages( [
							'name'              => $option_name,
							'id'                => $option_name,
							'selected'          => get_option( $option_name ),
							'show_option_none'  => '— Select —',
							'option_none_value' => 0
						] ); ?></td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
	}

	/**
	 * Disable the editor for selected post types pages.
	 *
	 * @since 1.0.0
	 */
	public function disable_editor() {

		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['post'] ) ) {

			return;
		}

		if ( 'edit' !== $_GET['action'] ) {

			return;
		}

		$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

		$current_post = intval( $_GET['post'] );
		if ( $shared->is_page_for_post_types( $current_post ) ) {

			$obj = $shared->get_page_for_post_type_object( $current_post );
			if ( $obj->disable_editor ) {

				remove_post_type_support( 'page', 'editor' );

			}
		}

	}

	/**
	 * Add post state for selected post type pages.
	 *
	 * @param string[] $post_states
	 * @param \WP_Post $current_post
	 *
	 * @return string[]
	 * @since 1.0.0
	 */
	public function filter_post_states( $post_states, $current_post ) {

		$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

		if ( $shared->is_page_for_post_types( $current_post ) ) {
			$obj           = $shared->get_page_for_post_type_object( $current_post );
			$post_states[] = sprintf( __( '%1$s Page', 'page-for-post-types' ), $obj->label );
		}

		return $post_states;
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {

		$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

		$post_types = $shared->get_page_for_post_type_objects();
		foreach ( $post_types as $post_type ) {

			register_setting( 'reading', $shared->option_name( $post_type->name ), [ 'type' => 'integer' ] );
		}

	}

	/**
	 * Show a notice on the page edit screen for post type pages.
	 *
	 * @param \WP_Post $the_post
	 *
	 * @since 1.0.0
	 */
	public function show_notice( $the_post ) {

		if ( 'page' !== $the_post->post_type ) {

			return;
		}

		$shared = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );

		if ( $shared->is_page_for_post_types( $the_post ) ) {
			$obj = $shared->get_page_for_post_type_object( $the_post );
			echo '<div class="notice notice-warning inline"><p>' . sprintf( __( 'You are currently editing the page that shows your %s posts.', 'lcs-core' ), $obj->label ) . '</p></div>';
		}

	}

}
