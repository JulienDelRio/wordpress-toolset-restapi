<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once TOOLSET_RESTAPI_PATH . 'includes/utils.php';

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
    $custom_post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'Toolset RestAPI', 'toolset-restapi' ) . '</h1>';

    echo '<h2>' . esc_html__( 'Selection des types à afficher', 'toolset-restapi' ) . '</h2>';
    echo '<form method="post" action="">';
    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . esc_html__( 'Select', 'toolset-restapi' ) . '</th>';
    echo '<th>' . esc_html__( 'Name', 'toolset-restapi' ) . '</th>';
    echo '<th>' . esc_html__( 'Label', 'toolset-restapi' ) . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ( ! empty( $custom_post_types ) ) {
        foreach ( $custom_post_types as $post_type ) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="selected_post_types[]" value="' . esc_attr( $post_type->name ) . '"></td>';
            echo '<td>' . esc_html( $post_type->name ) . '</td>';
            echo '<td>' . esc_html( $post_type->label ) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
        echo '<td colspan="3">' . esc_html__( 'No custom post types found.', 'toolset-restapi' ) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<p><input type="submit" class="button-primary" value="' . esc_attr__( 'Save', 'toolset-restapi' ) . '"></p>';
    echo '</form>';
    echo '</div>';
}
