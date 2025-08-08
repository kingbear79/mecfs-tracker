<?php
/**
 * Plugin Name: MECFS Tracker
 * Description: Tagesprotokoll für Bell-Score, Emotionen, Symptome und Notizen.
 * Version:     0.1.12
 * Author:      Christian Schweden
 * Text Domain: mecfs-tracker
 * Update URI:  https://github.com/kingbear79/mecfs-tracker
 */

defined( 'ABSPATH' ) || exit;

define( 'MECFS_TRACKER_VERSION', '0.1.12' );
define( 'MECFS_TRACKER_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/includes/autoloader.php';

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'mecfs-tracker', plugins_url( 'assets/mecfs-tracker.css', __FILE__ ), [], MECFS_TRACKER_VERSION );
}, 11 );

   

if ( ! function_exists( 'mecfs_tracker_run' ) ) {
    function mecfs_tracker_run() {
        $plugin = new \MECFSTracker\Plugin();
        $plugin->run();
    }
}
mecfs_tracker_run();

register_activation_hook( __FILE__, [ '\\MECFSTracker\\Database', 'activate' ] );
register_deactivation_hook( __FILE__, [ '\\MECFSTracker\\Database', 'maybe_cleanup' ] );
