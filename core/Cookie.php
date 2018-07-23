<?php

namespace H;

class Cookie {

  function set( $key, $val=null, $exp=null, $path='/',$domain=null, $secure=null, $httponly=null ) {
    setcookie( $key, $val, time() + $exp, $path, $domain, $secure, $httponly );
  }

  function get( $key, $def=null ) {
    return $this->has( $key ) ? $_COOKIE[ $key ] : $def;
  }

  function has( $key ) {
    return isset( $_COOKIE[ $key ] );
  }

  function delete( $key, $path='/', $domain=null, $httponly=null ) {
    if ( $this->has( $key ) ) {
      $this->set( $key, null, time() - (3600 * 3650), $path, $domain, $httponly );
    }
  }

  function reset() {
    $_COOKIE[] = array();
  }

}
