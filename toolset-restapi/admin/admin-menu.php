<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add admin menu under Tools.
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
 * Display the admin page.
 */
function toolset_restapi_admin_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'Toolset RestAPI', 'toolset-restapi' ) . '</h1>';
    echo '</div>';
}
