<?php
namespace MECFSTracker;

class Exporter {

    public function register() {
        add_action( 'wp_ajax_mecfs_export', [ $this, 'export_csv' ] );
        add_shortcode( 'mecfs_export_button', [ $this, 'button' ] );
    }

    public function button() {
        if ( ! is_user_logged_in() ) {
            return '';
        }
        return '<button id="mecfs-export">' . esc_html__( 'Daten exportieren', 'mecfs-tracker' ) . '</button>';
    }

    public function export_csv() {
        if ( ! is_user_logged_in() ) {
            wp_die();
        }
        global $wpdb;
        $table = $wpdb->prefix . 'mecfs_entries';
        $rows  = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY entry_date ASC", get_current_user_id() ),
            ARRAY_A
        );
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=mecfs-data.csv' );
        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, array_keys( $rows[0] ?? [ 'entry_date', 'bell_score', 'emotion', 'notes', 'positives', 'negatives' ] ) );
        foreach ( $rows as $row ) {
            fputcsv( $out, $row );
        }
        fclose( $out );
        exit;
    }
}
