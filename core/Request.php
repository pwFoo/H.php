<?php

namespace H;

class Request {

  function __construct() {
    $this->headers = $this->getHeaders();
    $this->files = $_FILES;
    $this->get = $_GET;
    $this->post = $_POST;
    $this->server = $_SERVER;
  }

  function get( $key, $def=null ) {
    return isset( $this->get[ $key ] ) ? $this->get[ $key ] : $def;
  }

  function post( $key, $def=null ) {
    return isset( $this->post[ $key ] ) ? $this->post[ $key ] : $def;
  }

  function put( $key, $def=null ) {
    return $this->method( 'PUT' ) ? $this->raw( $key ) : $def;
  }

  function patch( $key, $def=null ) {
    return $this->method( 'PATCH' ) ? $this->raw( $key ) : $def;
  }

  function raw( $key=null, $def=null ) {
    $input = file_get_contents( 'php://input' );
    if ( is_null( $key ) ) {
      return $input;
    }
    parse_str( $input, $rawBody );
    return isset( $rawBody[ $key ] ) ? $rawBody[ $key ] : $def;
  }

  function files( $key ) {
    return isset( $this->files[ $key ] ) ? $this->files[ $key ] : null;
  }

  function method( $key=null ) {
    $verb = strtoupper( $this->env( 'REQUEST_METHOD' ) );
    if ( $this->hasHeader( 'X-Http-Method-Override' ) ) {
      $verb = strtoupper( $this->header( 'X-Http-Method-Override' ) );
    } else {
      $verb = isset( $this->post['_method'] ) ? strtoupper( $this->post['_method'] ) : $verb;
    }
    return is_null( $key ) ? $verb : (strtoupper( $key ) === $verb);
  }

  function getHeaders() {
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
    return $headers;
  }

  function hasHeader( $key ) {
    return isset( $this->headers[ strtoupper( $key ) ] );
  }

  function header( $key ) {
    return $this->hasHeader( $key ) ? $this->headers[ strtoupper( $key ) ] : null;
  }

  function env( $key, $def=null ) {
    $key = strtoupper( $key );
    return isset( $this->server[ $key ] ) ? $this->server[ $key ] : $def;
  }

  function base( $str=null ) {
    return str_replace( '\\', '', dirname( dirname( 'Request.php' ) ) ) . $str;
  }

  function site( $str=null ) {
    $protocol = ! empty( $this->server[ 'HTTPS' ] ) && $this->server[ 'HTTPS' ] === 'on'
      ? 'https://' : 'http://';
    $domainName = $this->server[ 'HTTP_HOST' ];
    return $protocol . $domainName . $str;
  }

}