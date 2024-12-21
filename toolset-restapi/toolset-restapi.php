<?php
/**
 * Plugin Name: Toolset RestAPI
 * Plugin URI: https://github.com/JulienDelRio/wordpress-toolset-restapi
 * Description: Adds a Rest API to WordPress for managing Toolset data.
 * Version: 0.1.0
 * Author: Julien DEL RIO
 * Author URI: https://github.com/JulienDelRio
 * License: Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: toolset-restapi
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'TOOLSET_RESTAPI_VERSION', '0.1.0' );
define( 'TOOLSET_RESTAPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOOLSET_RESTAPI_URL', plugin_dir_url( __FILE__ ) );

// Include admin functionality.
require_once TOOLSET_RESTAPI_PATH . 'admin/admin-controller.php';

// Include utility functions.
require_once TOOLSET_RESTAPI_PATH . 'includes/utils.php';

// Include REST API functionality.
require_once TOOLSET_RESTAPI_PATH . 'rest-api/rest_controller.php';

/**
 * Initialize the plugin.
 */
function toolset_restapi_init() {
    // Load translations.
    load_plugin_textdomain( 'toolset-restapi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'toolset_restapi_init' );

/**
 * Cleanup on plugin deactivation.
 */
function toolset_restapi_deactivate() {
    // Perform cleanup actions here if necessary.
}
register_deactivation_hook( __FILE__, 'toolset_restapi_deactivate' );
