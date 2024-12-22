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

        // Route for getting selected types
        register_rest_route( $base_url . '/' . $version, '/types', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'get_selected_custom_types' ),
            'permission_callback' => '__return_true',
        ));

        // Dynamically register a route for each selected custom post type
        $selected_post_types = get_option('toolset_restapi_selected_post_types', array());
        foreach ($selected_post_types as $type) {
            register_rest_route( $base_url . '/' . $version, '/' . $type . '/', array(
                'methods'  => 'GET',
                'callback' => function($request) use ($type) {
                    return $this->get_custom_type_items_dynamic($type);
                },
                'permission_callback' => '__return_true',
            ));
        }
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

    /**
     * Get all items of a custom post type dynamically.
     *
     * @param string $type The custom post type.
     * @return WP_REST_Response
     */
    public function get_custom_type_items_dynamic($type) {
        // Check if the type is allowed
        $selected_post_types = get_option('toolset_restapi_selected_post_types', array());
        if (!in_array($type, $selected_post_types)) {
            return new WP_REST_Response(array('message' => 'Custom type not allowed.'), 403);
        }

        // Fetch posts of the specified type
        $query = new WP_Query(array(
            'post_type'      => $type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));

        if (!$query->have_posts()) {
            return new WP_REST_Response(array('message' => 'No items found.'), 404);
        }

        $items = array();
        while ($query->have_posts()) {
            $query->the_post();

            $items[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'link'  => get_permalink(),
            );
        }

        // Restore global post data
        wp_reset_postdata();

        return new WP_REST_Response($items, 200);
    }
}

// Initialize the REST API controller.
add_action( 'rest_api_init', function () {
    $controller = new Toolset_RestAPI_Controller();
    $controller->register_routes();
});
