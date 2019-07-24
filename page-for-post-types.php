<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cnpagency.com
 * @since             1.0.0
 * @package           Page_For_Post_Types
 *
 * @wordpress-plugin
 * Plugin Name:       Page For Post Types
 * Plugin URI:        https://cnpagency.com
 * Description:       Select pages to use as custom post type archives just like the native WordPress <code>page_for_posts</code> setting.
 * Version:           1.0.0
 * Author:            CNP
 * Author URI:        https://cnpagency.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       page-for-post-types
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAGE_FOR_POST_TYPES_VERSION', '1.0.0' );
define( 'PAGE_FOR_POST_TYPES_PATH', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-page-for-post-types-activator.php
 */
function activate_page_for_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-page-for-post-types-activator.php';
	Page_For_Post_Types_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-page-for-post-types-deactivator.php
 */
function deactivate_page_for_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-page-for-post-types-deactivator.php';
	Page_For_Post_Types_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_page_for_post_types' );
register_deactivation_hook( __FILE__, 'deactivate_page_for_post_types' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-page-for-post-types.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_page_for_post_types() {

	$plugin = new Page_For_Post_Types();
	$plugin->run();

}

run_page_for_post_types();
