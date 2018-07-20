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
      'database' => __NAMESPACE__ .'\Database',
      'flash' => new Flash(),
      'hash' => new Hash(),
      'config' => new Config(),
      'args' => array()
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
      $request_uri = str_replace( dirname( $script_name ), '', strtok( $_SERVER[ 'REQUEST_URI' ],'?' ) );
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

  function get( $path, $func ) {
    $this->map( 'GET', $path, $func );
  }

  function post( $path, $func ) {
    $this->map( 'POST', $path, $func );
  }

  function put( $path, $func ) {
    $this->map( 'PUT', $path, $func );
  }

  function patch( $path, $func ) {
    $this->map( 'PATCH', $path, $func );
  }

  function delete( $path, $func ) {
    $this->map( 'DELETE', $path, $func );
  }

  function options( $path, $func ) {
    $this->map( 'OPTIONS', $path, $func );
  }

  function any( $path, $func ) {
    $this->map( 'ANY', $path, $func );
  }

  function group( $base, $routes=array() ) {
    foreach ( $routes as $route => $func ) {
      $route = explode( '->', $route );
      $verb = trim( $route[0] );
      $path = !empty( $route[1] ) ? $base . trim( $route[1] ) : $base;
      $this->map( $verb, $path, $func );
    }
  }

  function notFound( $func ) {
    $this->map( 'ANY', '404', $func );
  }

}