<?php

namespace H;

class Hash {

  function make( $str, $algo=PASSWORD_DEFAULT, $opts=null ) {
    return password_hash( $str, $algo, $opts );
  }

  function check( $str, $hash ) {
    return password_verify( $str, $hash );
  }

  function needsRehash( $str, $algo=PASSWORD_DEFAULT, $opts=null ) {
    return password_needs_rehash( $str, $algo, $opts );
  }

  function random( $length=32 ) {
    return substr( md5( mt_rand() ), 0, $length );
  }

}
