<?php
spl_autoload_register(
    function ( $class ) {
        if ( strpos( $class, 'MECFSTracker\\' ) !== 0 ) {
            return;
        }
        $path = __DIR__ . '/' . strtolower( str_replace( '\\', '/', substr( $class, 14 ) ) ) . '.php';
        if ( file_exists( $path ) ) {
            require $path;
        }
    }
);
