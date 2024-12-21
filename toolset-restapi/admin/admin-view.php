<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the admin page for Toolset RestAPI.
 *
 * @param array $custom_post_types List of custom post types.
 * @param array $saved_post_types  List of saved post types.
 */
function toolset_restapi_render_admin_page( $custom_post_types, $saved_post_types ) {
    // Retrieve the saved API base URL or set the default value.
    $api_base_url = get_option( 'toolset_restapi_base_url', 'toolset-data' );

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'Toolset RestAPI', 'toolset-restapi' ) . '</h1>';

    echo '<h2>' . esc_html__( 'Selection des types à afficher', 'toolset-restapi' ) . '</h2>';
    echo '<form method="post" action="">';
    echo '<table class="wp-list-table widefat fixed striped">'; // Classes ajoutées pour correspondre au style WordPress
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 60px;" class="manage-column column-cb">' . esc_html__( 'Select', 'toolset-restapi' ) . '</th>'; // Taille ajustée pour être minimale
    echo '<th class="manage-column column-primary">' . esc_html__( 'Name', 'toolset-restapi' ) . '</th>';
    echo '<th class="manage-column">' . esc_html__( 'Label', 'toolset-restapi' ) . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ( ! empty( $custom_post_types ) ) {
        foreach ( $custom_post_types as $post_type ) {
            $checked = in_array( $post_type->name, $saved_post_types ) ? 'checked' : '';
            echo '<tr>';
            echo '<th scope="row" class="check-column"><input type="checkbox" name="selected_post_types[]" value="' . esc_attr( $post_type->name ) . '" ' . $checked . '></th>'; // Utilise les classes natives WordPress
            echo '<td class="column-primary">' . esc_html( $post_type->name ) . '</td>';
            echo '<td>' . esc_html( $post_type->label ) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
        echo '<td colspan="3">' . esc_html__( 'No custom post types found.', 'toolset-restapi' ) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr>';
    echo '<th style="width: 60px;" class="manage-column column-cb">' . esc_html__( 'Select', 'toolset-restapi' ) . '</th>'; // Taille ajustée pour être minimale
    echo '<th class="manage-column column-primary">' . esc_html__( 'Name', 'toolset-restapi' ) . '</th>';
    echo '<th class="manage-column">' . esc_html__( 'Label', 'toolset-restapi' ) . '</th>';
    echo '</tr>';
    echo '</tfoot>';
    echo '</table>';

    echo '<h2>' . esc_html__( 'Base d\'url de l\'api', 'toolset-restapi' ) . '</h2>';
    echo '<p>' . esc_html__( 'Les seuls caractères acceptés sont des lettres minuscules, des chiffres et les caractères _ et -.', 'toolset-restapi' ) . '</p>';
    echo '<input type="text" name="api_base_url" value="' . esc_attr( $api_base_url ) . '" pattern="[a-z0-9_-]+" title="' . esc_attr__( 'Only lowercase letters, numbers, underscores, and hyphens are allowed.', 'toolset-restapi' ) . '" style="width: 300px;">';

    echo '<p><input type="submit" class="button-primary" value="' . esc_attr__( 'Save', 'toolset-restapi' ) . '"></p>';
    echo '</form>';
    echo '</div>';
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    // Handle saving selected post types.
    if ( isset( $_POST['selected_post_types'] ) ) {
        $selected_post_types = array_map( 'sanitize_text_field', $_POST['selected_post_types'] );
        update_option( 'toolset_restapi_selected_post_types', $selected_post_types );
    }

    // Handle saving the API base URL.
    if ( isset( $_POST['api_base_url'] ) ) {
        $api_base_url = sanitize_text_field( $_POST['api_base_url'] );

        // Validate the API base URL.
        if ( preg_match( '/^[a-z0-9_-]+$/', $api_base_url ) ) {
            update_option( 'toolset_restapi_base_url', $api_base_url );
        } else {
            echo '<div class="error"><p>' . esc_html__( 'The API base URL contains invalid characters. Only lowercase letters, numbers, underscores, and hyphens are allowed.', 'toolset-restapi' ) . '</p></div>';
        }
    }
}
