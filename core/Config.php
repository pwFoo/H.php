<?php

namespace H;

class Config {

  private $configs = array();

  function set( $key, $val ) {
    $this->configs[ $key ] = $val;
  }

  function get( $key, $def=null ) {
    return $this->has( $key ) ? $this->configs[ $key ] : $def;
  }

  function has( $key ) {
    return isset( $this->configs[ $key ] );
  }

  function remove( $key ) {
    if ( $this->has( $key ) ) {
      unset( $this->configs[ $key ] );
    }
  }

  function reset() {
    $this->configs = array();
  }

}