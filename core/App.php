<?php

namespace H;

class App {

  private $routes = array();

  function __construct() {
    define( 'H_PHP', 1 );
    $this->app = (object) array(
      'request' => new Request(),
      'response' => new Response(),
      'cookie' => new Cookie(),
      'session' => new Session(),
      'flash' => new Flash(),
      'hash' => new Hash(),
      'args' => array(),
      'plugins' => array(),
    );
  }

  function map( $verb, $path, $func ) {
    $this->routes[ $path ] = array( $verb, $func );
  }

  function run() {
    foreach ( $this->routes as $path => $route ) {
      $verb = explode( '|', $route[0] );
      $verb = array_map( 'trim', array_map( 'strtoupper', $verb ) );
      $script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : 'index.php';
      $request_uri = !empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER[ 'REQUEST_URI' ] : '/';
      $request_uri = str_replace( dirname( $script_name ), '', $request_uri );
      $path = preg_replace_callback(
        '#@([\w]+)(:([^/()]*))?#',
        function( $matches ) {
          if ( isset( $matches[3] ) ) {
            return '(?P<' . $matches[1] . '>' . $matches[3] . ')';
          }
          return '(?P<' . $matches[1] . '>[^/]+)';
        },
        str_replace( ')', ')?', '~^' . $path . '/?$~' )
      );
      $verb_matched = in_array( 'ANY', $verb ) || in_array( $this->app->request->method(), $verb );
      if ( preg_match( $path, $request_uri, $args ) && $verb_matched ) {
        array_shift( $args );
        $this->app->args = $args;
        die( call_user_func( $route[1], $this->app ) );
      }
    }
    $this->app->response->statusCode( 404 );
    if ( isset( $this->routes['404'] ) ) {
      die( call_user_func( $this->routes['404'][1], $this->app ) );
    } else {
      die( 'Page not found!' );
    }
  }

  function group( $base, $routes=array() ) {
    foreach ( $routes as $route => $func ) {
      $route = explode( '->', $route );
      $verb = trim( $route[0] );
      $path = !empty( $route[1] ) ? $base . trim( $route[1] ) : $base;
      $this->map( $verb, $path, $func );
    }
  }

  function plugin( $key, $val ) {
    $this->app->plugins[ $key ] = $val;
  }

  function notFound( $func ) {
    $this->map( 'ANY', '404', $func );
  }

}