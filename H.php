<?php
/**
 * H.php | The Minimalist PHP Framework!
 * @author Oyedele Hammed (devHammed)
 * @see http://github.com/devHammed/H.php
 * @version 1.0
 * @license MIT License
 */

# Route

function route( $type, $regex, $fn ) {
  $type = explode( '|', $type );
  $type = array_map( 'trim', array_map( 'strtoupper', $type ) );
  $path = !empty( $_SERVER[ 'PATH_INFO' ] ) ? $_SERVER[ 'PATH_INFO' ] : '/';
  $regex = '~^' . $regex . '/?$~';
  $req_valid = ( in_array( 'ANY', $type ) || in_array( req_method(), $type ) );
  if ( preg_match( $regex, $path, $args ) && $req_valid ) {
    if ( is_string( $fn ) && strpos( $fn, '#' ) ) {
      $parts = explode( '#', $fn );
      $ctrl = $parts[0];
      $method = $parts[1];
      $ctrl_path = config_get( 'controllers_dir', 'controllers/' ) . $ctrl . '.php';
      if ( !file_exists( $ctrl_path ) )
        die( 'No Controller: ' . $ctrl );
      include $ctrl_path;
      if ( !method_exists( $ctrl, $method ) )
        die( 'No Method: ' . $method );
      $fn = array( new $ctrl, $method );
    }
    array_shift( $args );
    die( call_user_func_array( $fn, array_values( $args ) ) );
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
    $db_host = config_get( 'db_host', 'localhost' );
    $db_name = config_get( 'db_name' );
    $db_user = config_get( 'db_user', 'root' );
    $db_pass = config_get( 'db_pass' );
    $dbh = new PDO( 'mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass, $options );
  } catch ( PDOException $e ) {
    die( 'Error connecting to the database!' );
  }
  try {
    $res = $dbh->prepare( $sql );
    $res->execute( $bind );
    return $res;
  } catch (PDOException $e) {
    die( 'Error executing SQL query!' );
  }
}

function db_select( $sql, $bind=array() ) {
  $res = db_run( $sql, $bind );
  $res->setFetchMode( PDO::FETCH_ASSOC );
  $rows = array();
  while ( $row = $res->fetch() )
    $rows[] = $row;
  return $rows;
}

# Request

function req_get( $key, $def='' ) {
  return isset( $_GET[ $key ] ) ? $_GET[ $key ] : $def;
}

function req_post( $key, $def='' ) {
  return isset( $_POST[ $key ] ) ? $_POST[ $key ] : $def;
}

function req_put( $key, $def='' ) {
    return req_method( 'PUT' ) ? ( req_raw( $key ) ) : $def;
}

function req_patch( $key, $def='' ) {
  return req_method( 'PATCH' ) ? ( req_raw( $key ) ) : $def;
}

function req_raw( $key='', $def='' ) {
  $input = file_get_contents('php://input');
  if ( empty( $key ) )
    return $input;
  parse_str( $input, $raw );
  return isset( $raw[ $key ] ) ? $raw[ $key ] : $def;
}

function req_cookie( $key, $def='' ) {
  return isset( $_COOKIE[ $key ] ) ? $_COOKIE[ $key ] : $def;
}

function req_file( $key ) {
  return isset( $_FILES[ $key ] ) ? $_FILES[ $key ] : NULL;
}

function req_session( $key, $def='' ) {
  return isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : $def;
}

function req_method( $key='' ) {
  $method = isset( $_REQUEST[ '_method' ] ) ? ucwords( $_REQUEST[ '_method' ] ) : getenv( 'request_method' );
  return ( !empty( $key ) ) ? ( $method == $key ) : $method;
}

function req_header( $key ) {
  foreach ( $_SERVER as $k => $v ) {
    if ( substr( $k, 0, 5 ) == 'HTTP_' ) {
      $k = str_replace( '_', '-', substr( $k, 5 ) );
      $headers[ $k ] = $v;
    } elseif ( $k == 'CONTENT_TYPE' || $k == 'CONTENT_LENGTH' ) {
      $k = str_replace( '_', '-', $k );
      $headers[ $k ] = $v;
    }
  }
  $key = strtoupper( $key );
  return isset( $headers[ $key ] ) ? $headers[ $key ] : NULL;
}

function req_env( $key='' ) {
  return getenv( $key );
}

function req_base( $str='/' ) {
  return str_replace( '\\', '', dirname( req_env( 'SCRIPT_NAME' ) ) ) . $str;
}

function req_site( $str='/' ) {
    $protocol = ( !empty($_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] !== 'off' || $_SERVER[ 'SERVER_PORT' ] == 443 ) ? "https://" : "http://";
    $domainName = $_SERVER[ 'HTTP_HOST' ] ;
    return $protocol . $domainName . $str;
}

# Response / View

function res_addHeader( $str, $code=NULL ) {
  header( $str , true, $code );
}

function res_redirect( $url ) {
  res_addHeader( 'Location: ' . $url );
}

function res_status( $code=NULL ) {
  return http_response_code( $code );
}

function res_back() {
  res_redirect( req_header( 'Referer' ) );
}

function res_type( $type ) {
  res_addHeader( 'Content-type: ' . $type );
}

function res_render( $file='', $vars='' ) {
  $file = config_get( 'views_dir', 'views/' ) . $file . '.php';
  if ( !file_exists( $file ) )
    die( 'View not found: ' . $file );
  if ( is_array( $vars ) )
    extract( $vars );
  ob_start();
  require( $file );
  return ob_get_clean();
}

function res_json( $data ) {
  res_type( 'application/json;charset=utf8' );
  return json_encode( $data );
}

function res_jsonp( $data, $callback='callback' ) {
  if ( !isset( $_GET[ $callback ] ) ) die( 'No JSONP Callback!' );
  res_type( 'text/javascript' );
  echo $_GET[ $callback ] . '(' . json_encode( $data ) .');';
}

function esc( $var ) {
  return htmlspecialchars( $var );
}

# Cookie

function cookie_set( $key, $val='', $exp=NULL, $path='/',$domain=NULL, $secure=NULL, $httponly=NULL ) {
  setcookie( $key, $val, time() + $exp, $path, $domain, $secure, $httponly );
}

function cookie_get( $key, $def='' ) {
  return req_cookie( $key, $def );
}

function cookie_has( $key ) {
  return isset( $_COOKIE[$key] );
}

function cookie_delete( $key, $path='/', $domain=null, $httponly=null ) {
  if ( cookie_has( $key ) )
    cookie_set( $key, null, time() - (3600 * 3650), $path, $domain, $httponly );
}

function cookie_reset() {
  $_COOKIE[] = array();
}



# Session

function ses_start() {
  session_start();
}

function ses_set( $key, $val ) {
   $_SESSION[$key] = $val;
}

function ses_get( $key, $def='' ) {
  return req_session( $key, $def );
}

function ses_has( $key ) {
  return isset( $_SESSION[$key] );
}

function ses_delete( $key ) {
  if ( ses_has( $key ) )
    unset( $_SESSION[$key] );
}

function ses_id( $newID='' ) {
  return ( !empty( $newID ) ) ? session_id( $newID ) : session_id();
}

function ses_reset() {
  session_destroy();
}

# Flash

function flash_set( $key, $val ) {
  if ( !ses_id() )
    ses_start();
  $_SESSION[ 'h_php_flash_msg' ][ $key ] = $val;
}
function flash_has( $key ) {
  if ( !ses_id() )
    ses_start();
  return isset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
}
function flash_get( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !flash_has( $key ) )
    return NULL;
  $val = $_SESSION[ 'h_php_flash_msg' ][ $key ];
  unset( $_SESSION[ 'h_php_flash_msg' ][ $key ] );
  return $val;
}
function flash_keep( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !flash_has( $key ) )
    return NULL;
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
