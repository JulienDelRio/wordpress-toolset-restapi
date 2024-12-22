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
                    return $this->get_custom_type_items_dynamic($type, $request);
                },
                'args'     => array(
                    'page'    => array(
                        'default' => 1,
                        'sanitize_callback' => 'absint',
                    ),
                    'per_page' => array(
                        'default' => 10,
                        'sanitize_callback' => 'absint',
                    ),
                    'order'   => array(
                        'default' => 'DESC',
                        'validate_callback' => function($param) {
                            return in_array(strtoupper($param), array('ASC', 'DESC'));
                        },
                    ),
                    'orderby' => array(
                        'default' => 'date',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'permission_callback' => '__return_true',
            ));

            // Route for fetching a single item by ID
            register_rest_route( $base_url . '/' . $version, '/' . $type . '/(?P<id>\d+)', array(
                'methods'  => 'GET',
                'callback' => function($request) use ($type) {
                    return $this->get_custom_type_item_by_id($type, $request);
                },
                'args'     => array(
                    'id' => array(
                        'required' => true,
                        'sanitize_callback' => 'absint',
                    ),
                ),
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
     * Get all items of a custom post type dynamically with pagination and sorting.
     *
     * @param string $type The custom post type.
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response
     */
    public function get_custom_type_items_dynamic($type, $request) {
        // Check if the type is allowed
        $selected_post_types = get_option('toolset_restapi_selected_post_types', array());
        if (!in_array($type, $selected_post_types)) {
            return new WP_REST_Response(array('message' => 'Custom type not allowed.'), 403);
        }

        // Fetch parameters
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $order = $request->get_param('order');
        $orderby = $request->get_param('orderby');

        // Fetch posts of the specified type
        $query = new WP_Query(array(
            'post_type'      => $type,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post_status'    => 'publish',
            'order'          => $order,
            'orderby'        => $orderby,
        ));

        $total_posts = $query->found_posts;
        $total_pages = $query->max_num_pages;

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

        $response = new WP_REST_Response($items, 200);
        $response->header('X-WP-Total', $total_posts);
        $response->header('X-WP-TotalPages', $total_pages);

        return $response;
    }

    /**
     * Get a single item of a custom post type by ID.
     *
     * @param string $type The custom post type.
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response
     */
    public function get_custom_type_item_by_id($type, $request) {
        $id = $request->get_param('id');

        // Verify the post exists and matches the type
        $post = get_post($id);
        if (!$post || $post->post_type !== $type || $post->post_status !== 'publish') {
            return new WP_REST_Response(array('message' => 'Item not found.'), 404);
        }

        // Prepare the response
        $item = array(
            'id'    => $post->ID,
            'title' => get_the_title($post),
            'link'  => get_permalink($post),
            'content' => apply_filters('the_content', $post->post_content),
        );

        return new WP_REST_Response($item, 200);
    }
}

// Initialize the REST API controller.
add_action( 'rest_api_init', function () {
    $controller = new Toolset_RestAPI_Controller();
    $controller->register_routes();
});
