<?php

namespace H;

class Flash {

  function set( $key, $val ) {
    if ( ! session_id() ) {
      session_start();
    }
    $_SESSION[ 'h_php_flash_msg' ][ $key ] = $val;
  }

  function has( $key ) {
    if ( ! session_id() ) {
      session_start();
    }
    return isset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
  }

  function get( $key ) {
    if ( ! session_id() ) {
      session_start();
    }
    if ( ! $this->has( $key ) ) {
      return null;
    }
    $val = $_SESSION[ 'h_php_flash_msg' ][ $key ];
    unset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
    return $val;
  }

  function keep( $key ) {
    if ( ! session_id() ) {
      session_start();
    }
    if ( ! $this->has( $key ) ) {
      return null;
    }
    return $_SESSION[ 'h_php_flash_msg' ][ $key ];
  }

}
