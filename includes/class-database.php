<?php
namespace MECFSTracker;

class Database {

    const OPTION_CLEANUP = 'mecfs_tracker_cleanup';

    public static function activate() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        $entries = "{$wpdb->prefix}mecfs_entries";
        $symptoms = "{$wpdb->prefix}mecfs_symptoms";
        $user_symptoms = "{$wpdb->prefix}mecfs_user_symptoms";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = [];
        $sql[] = "CREATE TABLE $entries (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            entry_date DATE NOT NULL,
            bell_score TINYINT UNSIGNED,
            emotion TINYINT UNSIGNED,
            notes TEXT,
            positives TEXT,
            negatives TEXT,
            PRIMARY KEY(id),
            UNIQUE KEY user_date (user_id, entry_date)
        ) $charset;";

        $sql[] = "CREATE TABLE $symptoms (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            slug VARCHAR(60) NOT NULL,
            label VARCHAR(120) NOT NULL,
            PRIMARY KEY(id),
            UNIQUE KEY slug (slug)
        ) $charset;";

        $sql[] = "CREATE TABLE $user_symptoms (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            symptom_id BIGINT UNSIGNED NOT NULL,
            severity TINYINT UNSIGNED DEFAULT 0,
            entry_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY(id),
            KEY entry (entry_id),
            KEY user (user_id)
        ) $charset;";

        dbDelta( $sql );

        self::seed_symptoms();
    }

    private static function seed_symptoms() {
        global $wpdb;
        $table = $wpdb->prefix . 'mecfs_symptoms';
        $defaults = [
            [ 'slug' => 'fatigue', 'label' => 'Fatigue' ],
            [ 'slug' => 'pain', 'label' => 'Schmerzen' ],
            [ 'slug' => 'brain-fog', 'label' => 'Brain Fog' ],
        ];
        foreach ( $defaults as $symptom ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE slug = %s", $symptom['slug'] ) );
            if ( ! $exists ) {
                $wpdb->insert( $table, $symptom );
            }
        }
    }

    public static function maybe_cleanup() {
        if ( get_option( self::OPTION_CLEANUP ) === 'yes' ) {
            global $wpdb;
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mecfs_user_symptoms" );
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mecfs_symptoms" );
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mecfs_entries" );
        }
    }
}
