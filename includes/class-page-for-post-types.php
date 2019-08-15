<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cnpagency.com
 * @since      1.0.0
 *
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Page_For_Post_Types
 * @subpackage Page_For_Post_Types/includes
 * @author     CNP <wordpress@cnpagency.com>
 */
class Page_For_Post_Types {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Page_For_Post_Types_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PAGE_FOR_POST_TYPES_VERSION' ) ) {
			$this->version = PAGE_FOR_POST_TYPES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'page-for-post-types';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Page_For_Post_Types_Loader. Orchestrates the hooks of the plugin.
	 * - Page_For_Post_Types_i18n. Defines internationalization functionality.
	 * - Page_For_Post_Types_Admin. Defines all hooks for the admin area.
	 * - Page_For_Post_Types_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-page-for-post-types-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-page-for-post-types-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-page-for-post-types-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-page-for-post-types-public.php';

		/**
		 * The class responsible for defining functionality shared across admin and public-facing sides of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-page-for-post-types-shared.php';

		/**
		 * The class responsible for defining publicly accessible functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-page-for-post-types-functions.php';

		$this->loader = new Page_For_Post_Types_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Page_For_Post_Types_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Page_For_Post_Types_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Page_For_Post_Types_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_setting_section' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'disable_editor' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'add_edit_link_for_post_type_pages', 80, 1 );
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'show_notice', 10, 1 );

		$this->loader->add_filter( 'display_post_states', $plugin_admin, 'filter_post_states', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links_' . PAGE_FOR_POST_TYPES_PATH, $plugin_admin, 'add_plugin_action_links', 10, 4 );

		$this->loader->add_action( 'pre_update_option_page_for_post_type_keys_hidden', $plugin_admin, 'update_options', 20, 0 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Page_For_Post_Types_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'register_post_type_args', $plugin_public, 'filter_post_type_args', 10, 2 );
		$this->loader->add_filter( 'register_taxonomy_args', $plugin_public, 'filter_taxonomy_args', 10, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Page_For_Post_Types_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
