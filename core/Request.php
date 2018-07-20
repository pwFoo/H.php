<?php

namespace H;

class Request {

  function get( $key, $def=NULL ) {
    return isset( $_GET[ $key ] ) ? $_GET[ $key ] : $def;
  }

  function post( $key, $def=NULL ) {
    return isset( $_POST[ $key ] ) ? $_POST[ $key ] : $def;
  }

  function put( $key, $def=NULL ) {
    return $this->method( 'PUT' ) ? $this->raw( $key ) : $def;
  }

  function patch( $key, $def=NULL ) {
    return $this->method( 'PATCH' ) ? $this->raw( $key ) : $def;
  }

  function raw( $key=NULL, $def=NULL ) {
    $input = file_get_contents( 'php://input' );
    if ( empty( $key ) ) {
      return $input;
    }
    parse_str( $input, $rawBody );
    return isset( $rawBody[ $key ] ) ? $rawBody[ $key ] : $def;
  }

  function files( $key ) {
    return isset( $_FILES[ $key ] ) ? $_FILES[ $key ] : NULL;
  }

  function method( $key='' ) {
    $verb = strtoupper( $_SERVER['REQUEST_METHOD'] );
    if ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
      $verb = strtoupper( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] );
    } else {
      $verb = isset( $_POST['_method'] ) ? strtoupper( $_POST['_method'] ) : $verb;
    }
    return empty( $key ) ? $verb : (strtoupper( $key ) === $verb);
  }

  function header( $key ) {
    $headers = array();
    foreach ( $_SERVER as $k => $v ) {
      if ( substr( $k, 0, 5 ) == 'HTTP_' ) {
        $k = str_replace( '_', '-', substr( $k, 5 ) );
        $headers[ $k ] = $v;
      } elseif ( $k === 'CONTENT_TYPE' || $k === 'CONTENT_LENGTH' ) {
        $k = str_replace( '_', '-', $k );
        $headers[ $k ] = $v;
      }
    }
    $key = strtoupper( $key );
    return isset( $headers[ $key ] ) ? $headers[ $key ] : NULL;
  }

  function env( $key='' ) {
    $key = strtoupper( $key );
    return isset( $_SERVER[ $key ] ) ? $_SERVER[ $key ] : getenv( $key );
  }

  function base( $str='' ) {
    return str_replace( '\\', '', dirname( req_env( 'SCRIPT_NAME' ) ) ) . $str;
  }

  function site( $str='' ) {
    $protocol = ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] === 'on'
      ? 'https://' : 'http://';
    $domainName = $_SERVER[ 'HTTP_HOST' ];
    return $protocol . $domainName . $str;
  }

}