<?php

/*
 * Copyright 2012-2018 WeCodePixels, http://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSliderFramework;

class Autoloader {
    // Root folder of the plugin.
    private $root;
    
    // Namespace of the plugin classes.
    private $namespace;

    public function __construct( $root, $namespace ) {
        $this->root      = $root;
        $this->namespace = $namespace;

        spl_autoload_register( array( $this, 'autoload' ) );
    }

    public function autoload( $class ) {
        $this->load( $class, $this->namespace . '\\', $this->root . '/src/', false );
        $this->load( $class, $this->namespace . 'Framework\\', __DIR__ . '/', true );
    }

    protected function load( $class, $namespace, $root, $stripNamespace = false ) {
        // Ignore classes that don't match our namespace.
        if ( strpos( $class, $namespace ) !== 0 ) {
            return;
        }

        if ( $stripNamespace ) {
            $class = substr( $class, strlen( $namespace ) );
        }

        $path = $root . str_replace( "\\", "/", $class ) . '.php';

        if ( is_file( $path ) ) {
            require $path;
        }
    }
}
