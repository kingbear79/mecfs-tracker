<?php
/**
 * Plugin Name: MECFS Tracker
 * Description: Tagesprotokoll für Bell-Score, Emotionen, Symptome und Notizen.
 * Version:     0.1.0
 * Author:      Ihr Name
 * Text Domain: mecfs-tracker
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/autoloader.php';

function mecfs_tracker_run() {
    $plugin = new \MECFSTracker\Plugin();
    $plugin->run();
}
mecfs_tracker_run();

register_activation_hook( __FILE__, [ '\\MECFSTracker\\Database', 'activate' ] );
register_deactivation_hook( __FILE__, [ '\\MECFSTracker\\Database', 'maybe_cleanup' ] );

function mecfs_tracker_render_form_block() {
    $form = new \MECFSTracker\Frontend_Form();
    return $form->render();
}

function mecfs_tracker_render_export_block() {
    $exporter = new \MECFSTracker\Exporter();
    return $exporter->button();
}
