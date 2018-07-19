<?php
/*
  Dummy Tests for some H.php Functions
  To test other functions, add them below and make sure you setup the dummy environment for it!

  Run test using:

  $~> php tests.php
*/

require 'H.php';

// Environment Variables

$_SERVER = array(
  'REQUEST_URI' => '/', //change this to set current URL
  'REQUEST_METHOD' => 'GET', // change this to set Request method
);

$_GET = array(
  'q' => 'H.php',
);

$_COOKIE = array();

$_FILES = array();

$_REQUEST = array();

$_SESSION = array();

$_ENV = array();

// Tests

route( 'ANY', '/', function() {
  return 'Hello Index';
} );

route( 'GET', '/user', function() {
  return 'Users';
} );

route( 'GET', '/user/:name', function( $args ) {
  return 'User: ' . $args[0];
} );

route( 'GET', '/search', function() {
  return 'Search results for: ' . req_get('q');
} );

route( 'GET', '/template', function( $args ) {
  return res_renderStr('Hello <?= $name ?>!', array(
    'name' => 'John Doe',
  ) );
} );

route( 'GET', '/json', function() {
  return res_json( array(
    'name' => 'John Doe',
    'age' => 32,
  ) );
} );

route( 'GET', '/optional(/@opt:[0-9]+)', function( $args ) {
  if ( !isset( $args['opt'] ) ) {
    return 'Optional: not provided!';
  }
  return 'Optional: provided';
});


route( 'ANY', '.*?', function() {
  return '404 Error Page';
} );