<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get a list of all custom post types.
 *
 * @return array List of custom post type names.
 */
function toolset_restapi_get_custom_post_types() {
    $args = array(
        'public'   => true,
        '_builtin' => false,
    );

    $custom_post_types = get_post_types( $args, 'names' );

    return $custom_post_types;
}
