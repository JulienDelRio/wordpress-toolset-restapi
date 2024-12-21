<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Toolset RestAPI Controller
 */
class Toolset_RestAPI_Controller {

    /**
     * Register REST API routes.
     */
    public function register_routes() {
        $base_url = get_option( 'toolset_restapi_base_url', 'toolset-data' );
        $version = 'v1';
        register_rest_route( $base_url . '/' . $version, '/custom-types', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_selected_custom_types' ),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get the selected custom post types.
     *
     * @return WP_REST_Response
     */
    public function get_selected_custom_types() {
        $selected_post_types = get_option( 'toolset_restapi_selected_post_types', array() );

        if ( empty( $selected_post_types ) ) {
            return new WP_REST_Response( array( 'message' => 'No custom types selected.' ), 404 );
        }

        $custom_types = array();

        foreach ( $selected_post_types as $post_type_name ) {
            $post_type_object = get_post_type_object( $post_type_name );

            if ( $post_type_object ) {
                $custom_types[] = array(
                    'name'  => $post_type_object->name,
                    'label' => $post_type_object->label,
                );
            }
        }

        return new WP_REST_Response( $custom_types, 200 );
    }
}

// Initialize the REST API controller.
add_action( 'rest_api_init', function () {
    $controller = new Toolset_RestAPI_Controller();
    $controller->register_routes();
});
