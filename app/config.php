<?php

define( 'DS', DIRECTORY_SEPARATOR );

define( 'H_PHP_DIR', dirname( dirname( __FILE__ ) ) . DS ); // App Path

define( 'H_PHP_CORE', H_PHP_DIR . 'core' ); // H.php Core

define( 'H_PHP_ENVIRONMENT', 'development' ); // Options: development, testing, production

define( 'H_PHP_CONTROLLERS', 'app' . DS . 'Controllers' ); // Controllers Directory

define( 'H_PHP_MODELS', 'app'. DS .'Models' ); // Models Directory

define( 'H_PHP_VIEWS', 'app'. DS .'Views' ); // Views Directory

// Other configurations