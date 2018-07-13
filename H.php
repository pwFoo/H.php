<?php
/**
 * H.php | The Minimalist PHP Framework!
 * @author Oyedele Hammed (devHammed)
 * @package H.php
 * @see http://github.com/devHammed/H.php
 * @version 1.3.0
 * @license MIT License
 */

# Route

function route( $verb, $path, $func ) {
  $verb = explode( '|', $verb );
  $verb = array_map( 'trim', array_map( 'strtoupper', $verb ) );
  $script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : 'index.php';
  $request_uri = !empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER[ 'REQUEST_URI' ] : '/';
  $request_uri = str_replace( dirname( $script_name ), '', $request_uri );
  $path = '~^' . $path . '/?$~';
  $path = preg_replace_callback(
    '#@([\w]+)(:([^/()]*))?#',
    function( $matches ) {
      if ( isset( $matches[3] ) ) {
        return '(?P<' . $matches[1] . '>' . $matches[3] . ')';
      }
      return '(?P<' . $matches[1] . '>[^/]+)';
    },
    str_replace( ')', ')?', $path )
  );
  $verb_matched = in_array( 'ANY', $verb ) || in_array( req_method(), $verb );
  if ( preg_match( $path, $request_uri, $args ) && $verb_matched ) {
    array_shift( $args );
    die( call_user_func( $func, $args ) );
  }
}

function route_base( $base, $routes=array() ) {
  foreach ( $routes as $route => $func ) {
    $route = explode( '->', $route );
    $verb = trim( $route[0] );
    $path = !empty( $route[1] ) ? $base . trim( $route[1] ) : $base;
    route( $verb, $path, $func );
  }
}

# Database

function db_run( $sql, $bind=array() ) {
  $options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  $sql = trim( $sql );
  try {
    $db_host = config_get( 'DB_HOST', 'localhost' );
    $db_name = config_get( 'DB_NAME' );
    $db_user = config_get( 'DB_USER', 'root' );
    $db_pass = config_get( 'DB_PASS' );
    $dbh = new PDO( 'mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass, $options );
  } catch ( PDOException $e ) {
    die( 'Error connecting to the database!' );
  }
  try {
    $res = $dbh->prepare( $sql );
    $res->execute( $bind );
    return $res;
  } catch ( PDOException $e ) {
    die( 'Error executing SQL query!' );
  }
}

function db_select( $sql, $bind=array() ) {
  $res = db_run( $sql, $bind );
  $rows = array();
  while ( $row = $res->fetch( PDO::FETCH_ASSOC ) ) {
    $rows[] = $row;
  }
  return $rows;
}

# Request

function req_get( $key, $def=NULL ) {
  return isset( $_GET[ $key ] ) ? $_GET[ $key ] : $def;
}

function req_post( $key, $def=NULL ) {
  return isset( $_POST[ $key ] ) ? $_POST[ $key ] : $def;
}

function req_put( $key, $def=NULL ) {
    return req_method( 'PUT' ) ? req_raw( $key ) : $def;
}

function req_patch( $key, $def=NULL ) {
  return req_method( 'PATCH' ) ? req_raw( $key ) : $def;
}

function req_raw( $key=NULL, $def=NULL ) {
  $input = file_get_contents( 'php://input' );
  if ( empty( $key ) ) {
    return $input;
  }
  parse_str( $input, $rawBody );
  return isset( $rawBody[ $key ] ) ? $rawBody[ $key ] : $def;
}

function req_cookie( $key, $def=NULL ) {
  return isset( $_COOKIE[ $key ] ) ? $_COOKIE[ $key ] : $def;
}

function req_file( $key ) {
  return isset( $_FILES[ $key ] ) ? $_FILES[ $key ] : NULL;
}

function req_session( $key, $def=NULL ) {
  return isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : $def;
}

function req_method( $key='' ) {
  $verb = strtoupper( $_SERVER['REQUEST_METHOD'] );
  if ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
    $verb = strtoupper( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] );
  } else {
    $verb = isset( $_POST['_method'] ) ? strtoupper( $_POST['_method'] ) : $verb;
  }
  return empty( $key ) ? $verb : (strtoupper( $key ) === $verb);
}

function req_header( $key ) {
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

function req_env( $key='' ) {
  $key = strtoupper( $key );
  return isset( $_SERVER[ $key ] ) ? $_SERVER[ $key ] : getenv( $key );
}

function req_base( $str='' ) {
  return str_replace( '\\', '', dirname( req_env( 'SCRIPT_NAME' ) ) ) . $str;
}

function req_site( $str='' ) {
  $protocol = ( ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] === 'on' || $_SERVER[ 'SERVER_PORT' ] === 443 )
  ? "https://" : "http://";
  $domainName = $_SERVER[ 'HTTP_HOST' ];
  return $protocol . $domainName . $str;
}

# Response / View

function res_addHeader( $str, $code=NULL ) {
  if ( ! headers_sent() ) {
    header( $str , true, $code );
  }
}

function res_redirect( $url ) {
  res_addHeader( 'Location: ' . $url );
}

function res_status( $code ) {
  return http_response_code( $code );
}

function res_back() {
  res_redirect( req_header( 'Referer' ) );
}

function res_type( $type ) {
  res_addHeader( 'Content-type: ' . $type );
}

function res_render( $file='', $vars='' ) {
  $file = config_get( 'VIEWS_DIR', 'views/' ) . $file . config_get( 'VIEWS_EXTENSION', '.php' );
  if ( ! file_exists( $file ) ) {
    die( 'View file not found: ' . $file );
  }
  if ( is_array( $vars ) ) {
    extract( $vars );
  }
  ob_start();
  require( $file );
  return ob_get_clean();
}

function res_renderStr( $tpl, $vars=NULL ) {
  if ( is_array( $vars ) ) {
    extract( $vars );
  }
  ob_start();
  eval( '?>' . $tpl );
  return ob_get_clean();
}

function res_json( $data ) {
  res_type( 'application/json' );
  return json_encode( $data );
}

function res_jsonp( $data, $func='callback' ) {
  if ( ! isset( $_GET[ $func ] ) ) {
    die( 'No JSONP Callback `'. $func . '`' );
  }
  res_type( 'text/javascript' );
  $func = preg_replace( '/[^\[\]\w$.]/g', '', $_GET[ $func ] );
  echo '/**/ typeof ' . $func . ' === "function" && ' . $func . '(' . json_encode( $data ) .');';
}

function esc( $var ) {
  return htmlspecialchars( $var );
}

# Cookie

function cookie_set( $key, $val=NULL, $exp=NULL, $path='/',$domain=NULL, $secure=NULL, $httponly=NULL ) {
  setcookie( $key, $val, time() + $exp, $path, $domain, $secure, $httponly );
}

function cookie_get( $key, $def=NULL ) {
  return req_cookie( $key, $def );
}

function cookie_has( $key ) {
  return isset( $_COOKIE[ $key ] );
}

function cookie_delete( $key, $path='/', $domain=NULL, $httponly=NULL ) {
  if ( cookie_has( $key ) ) {
    cookie_set( $key, NULL, time() - (3600 * 3650), $path, $domain, $httponly );
  }
}

function cookie_reset() {
  $_COOKIE[] = array();
}



# Session

function ses_start() {
  session_start();
}

function ses_set( $key, $val ) {
   $_SESSION[ $key ] = $val;
}

function ses_get( $key, $def=NULL ) {
  return req_session( $key, $def );
}

function ses_has( $key ) {
  return isset( $_SESSION[ $key ] );
}

function ses_delete( $key ) {
  if ( ses_has( $key ) ) {
    unset( $_SESSION[ $key ] );
  }
}

function ses_id( $newID='' ) {
  return ( !empty( $newID ) ) ? session_id( $newID ) : session_id();
}

function ses_reset() {
  session_destroy();
}

# Flash

function flash_set( $key, $val ) {
  if ( ! ses_id() ) {
    ses_start();
  }
  $_SESSION[ 'h_php_flash_msg' ][ $key ] = $val;
}
function flash_has( $key ) {
  if ( ! ses_id() ) {
    ses_start();
  }
  return isset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
}
function flash_get( $key ) {
  if ( ! ses_id() ) {
    ses_start();
  }
  if ( ! flash_has( $key ) ) {
    return NULL;
  }
  $val = $_SESSION[ 'h_php_flash_msg' ][ $key ];
  unset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
  return $val;
}
function flash_keep( $key ) {
  if ( ! ses_id() ) {
    ses_start();
  }
  if ( ! flash_has( $key ) ) {
    return NULL;
  }
  return $_SESSION[ 'h_php_flash_msg' ][ $key ];
}

# Hash

function hash_make( $str, $algo=PASSWORD_DEFAULT, $opts=NULL ) {
  return password_hash( $str, $algo, $opts );
}

function hash_check( $str, $hash ) {
  return password_verify( $str, $hash );
}

function hash_needsRehash( $str, $algo=PASSWORD_DEFAULT, $opts=NULL ) {
  return password_needs_rehash( $str, $algo, $opts );
}

function hash_random( $length=32 ) {
  return substr( md5( mt_rand() ), 0, $length );
}

# Configuration

function config_set( $key, $val ) {
  $key = strtolower( $key );
  $GLOBALS[ 'h_php_config' ][ $key ] = $val;
}

function config_get( $key, $def=NULL ) {
  $key = strtolower( $key );
  return config_has( $key ) ? $GLOBALS[ 'h_php_config' ][ $key ] : $def;
}

function config_has( $key ) {
  $key = strtolower( $key );
  return isset( $GLOBALS[ 'h_php_config' ][ $key ] );
}

function config_delete( $key ) {
  $key = strtolower( $key );
  unset( $GLOBALS[ 'h_php_config' ][ $key ] );
}

function config_reset() {
  $GLOBALS[ 'h_php_config' ] = array();
}

# Initialize
define( 'H_PHP', 1 );