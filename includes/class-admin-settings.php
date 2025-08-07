<?php
namespace MECFSTracker;

class Admin_Settings {

    public function register() {
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_init', [ $this, 'settings' ] );
    }

    public function menu() {
        add_options_page(
            __( 'MECFS Tracker', 'mecfs-tracker' ),
            __( 'MECFS Tracker', 'mecfs-tracker' ),
            'manage_options',
            'mecfs-tracker',
            [ $this, 'render' ]
        );
    }

    public function settings() {
        register_setting( 'mecfs_tracker', Database::OPTION_CLEANUP );
        add_settings_section( 'mecfs_tracker_main', '', '__return_false', 'mecfs-tracker' );
        add_settings_field(
            Database::OPTION_CLEANUP,
            __( 'Tabellen bei Deaktivierung löschen', 'mecfs-tracker' ),
            [ $this, 'cleanup_field' ],
            'mecfs-tracker',
            'mecfs_tracker_main'
        );
    }

    public function cleanup_field() {
        $value = get_option( Database::OPTION_CLEANUP, 'no' );
        echo '<label><input type="checkbox" name="' . Database::OPTION_CLEANUP . '" value="yes" ' . checked( 'yes', $value, false ) . '/> ' . esc_html__( 'Ja', 'mecfs-tracker' ) . '</label>';
    }

    public function render() {
        echo '<div class="wrap"><h1>MECFS Tracker</h1><form method="post" action="options.php">';
        settings_fields( 'mecfs_tracker' );
        do_settings_sections( 'mecfs-tracker' );
        submit_button();
        echo '</form></div>';
    }
}
