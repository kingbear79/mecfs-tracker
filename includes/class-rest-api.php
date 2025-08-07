<?php
namespace MECFSTracker;

class REST_API {

    public function register() {
        add_action( 'rest_api_init', [ $this, 'routes' ] );
    }

    public function routes() {
        register_rest_route(
            'mecfs/v1',
            '/entries',
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_entries' ],
                'permission_callback' => function () {
                    return is_user_logged_in();
                },
            ]
        );
    }

    public function get_entries( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mecfs_entries';
        $rows  = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT entry_date, bell_score, emotion FROM $table WHERE user_id = %d ORDER BY entry_date ASC",
                get_current_user_id()
            ),
            ARRAY_A
        );
        return rest_ensure_response( $rows );
    }
}
