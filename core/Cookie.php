<?php

namespace H;

class Cookie {

  function set( $key, $val=NULL, $exp=NULL, $path='/',$domain=NULL, $secure=NULL, $httponly=NULL ) {
    setcookie( $key, $val, time() + $exp, $path, $domain, $secure, $httponly );
  }

  function get( $key, $def=NULL ) {
    return $this->has( $key ) ? $_COOKIE[ $key ] : $def;
  }

  function has( $key ) {
    return isset( $_COOKIE[ $key ] );
  }

  function delete( $key, $path='/', $domain=NULL, $httponly=NULL ) {
    if ( $this->has( $key ) ) {
      $this->set( $key, NULL, time() - (3600 * 3650), $path, $domain, $httponly );
    }
  }

  function reset() {
    $_COOKIE[] = array();
  }

}