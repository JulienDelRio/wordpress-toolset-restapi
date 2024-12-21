<?php
// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options.
delete_option( 'toolset_restapi_base_url' );
delete_option( 'toolset_restapi_selected_post_types' );
