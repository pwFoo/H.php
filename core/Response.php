<?php

namespace H;

class Response {

  function addHeader( $str, $code=null ) {
    if ( ! headers_sent() ) {
      header( $str , true, $code );
    }
  }

  function redirect( $url ) {
    $this->addHeader( 'Location: ' . $url );
  }

  function statusCode( $code ) {
    return http_response_code( $code );
  }

  function back() {
    $ref = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '';
    $this->redirect( $ref );
  }

  function type( $type ) {
    $this->addHeader( 'Content-type: ' . $type );
  }

  function json( $data ) {
    $this->type( 'application/json' );
    return json_encode( $data );
  }

  function jsonp( $data, $func='callback' ) {
    if ( ! isset( $_GET[ $func ] ) ) {
      die( 'No JSONP Callback `'. $func . '`' );
    }
    $this->type( 'text/javascript' );
    $func = preg_replace( '/[^\[\]\w$.]/g', '', $_GET[ $func ] );
    echo '/**/ typeof ' . $func . ' === "function" && ' . $func . '(' . json_encode( $data ) .');';
  }

}
