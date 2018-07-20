<?php

require_once 'config.php';

switch ( H_PHP_ENVIRONMENT ) {
  case 'development':
    error_reporting( -1 );
    ini_set( 'display_errors', 1 );
    break;
  case 'testing':
  case 'production':
    ini_set( 'display_errors', 0 );
    error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED );
    break;
  default:
    header( 'HTTP/1.1 503 Service Unavailable.', TRUE, 503 );
    echo 'The application environment is not set correctly.';
    exit(1);
}


$core_files = array_diff( scandir( H_PHP_DIR.'core' ), array( '..', '.' ) );
foreach ( $core_files as $core_file ) {
  require_once H_PHP_DIR . '/core/' . $core_file;
}

$controller_files = array_diff( scandir( H_PHP_DIR.H_PHP_CONTROLLERS ), array( '..', '.' ) );
foreach ( $controller_files as $controller_file ) {
  require_once H_PHP_DIR . H_PHP_CONTROLLERS. '/' . $controller_file;
}

$model_files = array_diff( scandir( H_PHP_DIR.H_PHP_MODELS ), array('..', '.') );
foreach ( $model_files as $model_file ) {
  require_once H_PHP_DIR . H_PHP_MODELS.'/'. $model_file;
}

$composer = H_PHP_DIR . 'vendor/autoload.php';
if ( file_exists( $composer ) ) {
  require_once $composer;
}