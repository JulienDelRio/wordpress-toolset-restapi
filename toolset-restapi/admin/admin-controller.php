<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once TOOLSET_RESTAPI_PATH . 'admin/admin-view.php';

/**
 * Handle the admin menu registration and form submission.
 */
function toolset_restapi_add_admin_menu() {
    add_management_page(
        __( 'Toolset RestAPI', 'toolset-restapi' ),
        __( 'Toolset RestAPI', 'toolset-restapi' ),
        'manage_options',
        'toolset-restapi',
        'toolset_restapi_admin_page'
    );
}
add_action( 'admin_menu', 'toolset_restapi_add_admin_menu' );

/**
 * Display the admin page and handle form submission.
 */
function toolset_restapi_admin_page() {
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['selected_post_types'] ) ) {
        // Sanitize and save the selected post types.
        $selected_post_types = array_map( 'sanitize_text_field', $_POST['selected_post_types'] );
        update_option( 'toolset_restapi_selected_post_types', $selected_post_types );

        echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'toolset-restapi' ) . '</p></div>';
    }

    // Retrieve saved post types.
    $saved_post_types = get_option( 'toolset_restapi_selected_post_types', array() );
    $custom_post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );

    // Call the view to render the page.
    toolset_restapi_render_admin_page( $custom_post_types, $saved_post_types );
}
