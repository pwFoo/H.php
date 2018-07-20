<?php

define( 'H_PHP_DIR', $_SERVER[ 'DOCUMENT_ROOT' ] . '/' ); // App Path

define( 'H_PHP_CORE', H_PHP_DIR.'/core' ); // H.php Core

define( 'H_PHP_ENVIRONMENT', 'development' ); // Options: development, testing, production

define( 'H_PHP_CONTROLLERS', 'app/Controllers' ); // Controllers Directory

define( 'H_PHP_MODELS', 'app/Models' ); // Models Directory

define( 'H_PHP_VIEWS', 'app/Views' ); // Views Directory

// Stop Editing!

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


$scan_core_files = array_diff( scandir( H_PHP_DIR.'core' ), array( '..', '.' ) );
foreach ( $scan_core_files as $scf ) {
  try {
    require_once H_PHP_DIR .'/core/'. $scf;
  } catch ( Exception $e ) {
    die( 'Failed to autoload core files' );
  }
}

try {
  $CONFIG = require_once 'config.php';
} catch ( Exception $e ) {
  throw new Exception( 'Could not load config file!' );
}


$scan_controllers_dir = array_diff( scandir( H_PHP_DIR.H_PHP_CONTROLLERS ), array( '..', '.' ) );
foreach ($scan_controllers_dir as $scd) {
  try {
    require_once H_PHP_DIR . H_PHP_CONTROLLERS. '/' . $scd;
  } catch ( Exception $e ) {
    die( 'Failed to autoload controllers' );
  }
}

$scan_models_dir = array_diff( scandir( H_PHP_DIR.H_PHP_MODELS ), array('..', '.') );
foreach ( $scan_models_dir as $smd ) {
  try {
    require_once H_PHP_DIR . H_PHP_MODELS.'/'.$smd;
  } catch ( Exception $e ) {
    die( 'Failed to autoload models' );
  }
}

try {
  if ( $CONFIG[ 'load_composer' ] === TRUE ) {
    require_once H_PHP_DIR . 'vendor/autoload.php';
  }
} catch ( Exception $e ) {
  throw new Exception( 'Failed to load composer' );
}