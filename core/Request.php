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

  function get( $key, $def=NULL ) {
    return isset( $this->get[ $key ] ) ? $this->get[ $key ] : $def;
  }

  function post( $key, $def=NULL ) {
    return isset( $this->post[ $key ] ) ? $this->post[ $key ] : $def;
  }

  function put( $key, $def=NULL ) {
    return $this->method( 'PUT' ) ? $this->raw( $key ) : $def;
  }

  function patch( $key, $def=NULL ) {
    return $this->method( 'PATCH' ) ? $this->raw( $key ) : $def;
  }

  function raw( $key=NULL, $def=NULL ) {
    $input = file_get_contents( 'php://input' );
    if ( is_null( $key ) ) {
      return $input;
    }
    parse_str( $input, $rawBody );
    return isset( $rawBody[ $key ] ) ? $rawBody[ $key ] : $def;
  }

  function files( $key ) {
    return isset( $this->files[ $key ] ) ? $this->files[ $key ] : NULL;
  }

  function method( $key=NULL ) {
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
    return $this->hasHeader( $key ) ? $this->headers[ strtoupper( $key ) ] : NULL;
  }

  function env( $key, $def=NULL ) {
    $key = strtoupper( $key );
    return isset( $this->server[ $key ] ) ? $this->server[ $key ] : $def;
  }

  function base( $str=NULL ) {
    return str_replace( '\\', '', dirname( dirname( $this->env( 'SCRIPT_NAME' ) ) ) ) . $str;
  }

  function site( $str=NULL ) {
    $protocol = ! empty( $this->server[ 'HTTPS' ] ) && $this->server[ 'HTTPS' ] === 'on'
      ? 'https://' : 'http://';
    $domainName = $this->server[ 'HTTP_HOST' ];
    return $protocol . $domainName . $str;
  }

}