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
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public function update_options() {

		// First, get our post types.
		$shared     = new Page_For_Post_Types_Shared( $this->plugin_name, $this->version );
		$post_types = $shared->get_page_for_post_type_objects();

		$page_for_post_types_keys = get_option( 'page_for_post_types_keys' );

		foreach ( $post_types as $post_type ) {
			$option_name        = $shared->option_name( $post_type->name );
			$page_for_post_type = intval( get_option( $option_name ) ); // Note that options are converted to strings on save-- we have to convert back to an integer.

            if ( 0 === $page_for_post_type ) {
                $page_for_post_type = false;
            }

			// Add or update the plural version of the option name
			$plural_option_name = false;
			if ( 's' !== substr( $option_name, - 1 )  && 0 !== $page_for_post_type ) {
				$plural_option_name = $option_name . 's';
				update_option( $plural_option_name, $page_for_post_type );
			}

			// Add the option name
			if ( ! in_array( $option_name, $page_for_post_types_keys, true ) && false !== $page_for_post_type ) {
				$page_for_post_types_keys[] = $option_name;
			}

			// Add the plural if it's not there already.
			if ( ! in_array( $plural_option_name, $page_for_post_types_keys, true ) && false !== $page_for_post_type && false !== $plural_option_name ) {
				$page_for_post_types_keys[] = $plural_option_name;
			}

			// Now handle removing the option names if the value is 0.
			if ( false === $page_for_post_type ) {

				// Don't save zeros.
				delete_option( $option_name );

				$option_name_key = array_search( $option_name, $page_for_post_types_keys );
				if ( false !== $option_name_key ) {
					unset( $page_for_post_types_keys[ $option_name_key ] );
				}

				if ( false !== $plural_option_name ) {

					// Don't save zeros.
					delete_option( $plural_option_name );

					$plural_option_name_key = array_search( $plural_option_name, $page_for_post_types_keys );

					if ( false !== $plural_option_name_key ) {
						unset( $page_for_post_types_keys[ $plural_option_name_key ] );
					}
				}
			}
		}

		update_option( 'page_for_post_types_keys', $page_for_post_types_keys );
	}

	/**
	 * Add plugin action links.
	 *
	 * @param string[] $actions
	 * @param string $plugin_file
	 * @param string $plugin_data
	 * @param string $context
	 *
	 * @return array
	 */
	public function add_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {

		$plugin_actions = [
			'settings' => sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'options-reading.php' ) ), __( 'Settings', 'lcs-core' ) ),
		];

		return array_merge( $plugin_actions, $actions );
	}

	/**
	 * Add settings section.
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

	/**
	 * Add settings fields.
	 *
	 * @since 1.0.0
	 */
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
                    <td>
						<?php
						wp_dropdown_pages( [
							'name'              => $option_name,
							'id'                => $option_name,
							'selected'          => get_option( $option_name ),
							'show_option_none'  => '— Select —',
							'option_none_value' => 0
						] );
						?>
                    </td>
                </tr>
				<?php
			}
			?>
            <input type="hidden" name="page_for_post_type_keys_hidden" value="0"/>
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

			register_setting( 'reading', $shared->option_name( $post_type->name ), [
				'type'              => 'integer',
				'sanitize_callback' => [
					$this,
					'sanitize_input_save'
				]
			] );
		}

		register_setting( 'reading', 'page_for_post_type_keys_hidden', [ 'type' => 'hidden' ] );
	}

	public function sanitize_input_save( $option_value ) {
		return ( 0 === intval( $option_value ) ? false : $option_value );
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

	/**
	 * Filter the admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function add_edit_link_for_post_type_pages( $wp_admin_bar ) {

		if ( is_404() ) {

			$page_id = Page_For_Post_Types_Functions::get_page_for( '404' );
			if ( ! $page_id ) {
				return false;
			}

			$this->add_admin_bar_menu( $wp_admin_bar, $page_id, 'Edit 404 Page' );

			return true;
		}

		if ( is_post_type_archive() || is_tax() ) {

			$post_type = get_post_type();
			$page_id   = Page_For_Post_Types_Functions::get_page_for( $post_type );
			if ( ! $page_id ) {
				return false;
			}

			$this->add_admin_bar_menu( $wp_admin_bar, $page_id );

			return true;
		}

		return false;
	}

	/**
	 * Add admin be menu links
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 * @param int|string $page_id
	 * @param string $title
	 *
	 * @since 1.0.0
	 */
	public function add_admin_bar_menu( $wp_admin_bar, $page_id, $title = 'Edit Archive Page' ) {

		if ( empty( $page_id ) ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'    => 'edit',
			'title' => apply_filters( 'page_for_post_types_admin_bar_menu_title', $title ),
			'href'  => apply_filters( 'page_for_post_types_admin_bar_menu_link', get_edit_post_link( $page_id ) ),
		) );

	}

}
