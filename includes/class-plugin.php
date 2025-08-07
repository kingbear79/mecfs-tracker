<?php
namespace MECFSTracker;

class Plugin {

    public function run() {
        ( new Admin_Settings() )->register();
        ( new Frontend_Form() )->register();
        ( new Exporter() )->register();
        ( new REST_API() )->register();
        add_action( 'init', [ $this, 'register_blocks' ] );
    }

    public function register_blocks() {
        register_block_type( __DIR__ . '/../blocks/form' );
        register_block_type( __DIR__ . '/../blocks/chart' );
    }
}
