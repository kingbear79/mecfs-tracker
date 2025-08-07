<?php
spl_autoload_register(
    function ( $class ) {
        $prefix = 'MECFSTracker\\';

        if ( strpos( $class, $prefix ) !== 0 ) {
            return;
        }

        $relative_class = substr( $class, strlen( $prefix ) );
        $relative_class = str_replace( '_', '-', $relative_class );
        $relative_class = strtolower( str_replace( '\\', '/', $relative_class ) );

        $path = __DIR__ . '/class-' . $relative_class . '.php';

        if ( file_exists( $path ) ) {
            require $path;
        }
    }
);
