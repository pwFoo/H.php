<?php

define( 'H_PHP', true );

define( 'H_PHP_DIR', str_replace( '\\', '', dirname( dirname( 'config.php' ) ) ) ); // Base Path

define( 'H_PHP_CORE', H_PHP_DIR . '/core' ); // H.php Core

define( 'H_PHP_ENVIRONMENT', 'development' ); // Options: development, testing, production

define( 'H_PHP_CONTROLLERS', H_PHP_DIR . '/app/controllers' ); // Controllers Directory

define( 'H_PHP_MODELS', H_PHP_DIR .'/app/models' ); // Models Directory

define( 'H_PHP_VIEWS', H_PHP_DIR . '/app/views' ); // Views Directory

define( 'DB_USER', null );

define( 'DB_HOST', null );

define( 'DB_PASS', null );

define( 'DB_NAME', null );

// uncomment below to set custom PDO DSN
// define( 'DB_DSN', '' );

// Other Global configurations