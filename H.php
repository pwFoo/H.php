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
  $path = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '/';
  $regex = '~^' . $regex . '/?$~';
  $req_valid = ( in_array( 'ANY', $type ) || in_array( req_method(), $type ) );

  if ( preg_match( $regex, $path, $args ) && $req_valid ) {
    if ( is_string( $fn ) && strpos( $fn, '#' ) ) {
      $parts = explode( '#', $fn );
      $ctrl = $parts[0];
      $method = $parts[1];
      $ctrl_path = CONTROLLERS_DIR . $ctrl . '.php';
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


# View

function send_view( $file='', $vars='' ) {
  $file = VIEWS_DIR . $file . '.php';
  if ( !file_exists( $file ) )
    die( 'View not found: ' . $file );
  if ( is_array( $vars ) )
    extract( $vars );
  ob_start();
  require( $file );
  return ob_get_clean();
}

function send_json( $data ) {
  req_type( 'application/json;charset=utf8' );
  return json_encode( $data );
}

function send_jsonp( $data ) {
  req_type( 'text/javascript' );
  echo $_GET['callback'] . '(' . json_encode( $data ) .');';
}

function esc( $var ) {
  return htmlspecialchars( $var );
}

# Database

function db_run( $sql, $bind=array() ) {
  $options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  $sql = trim( $sql );
  try {
    $dbh = new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options );
  } catch ( PDOException $e ) {
    die( $e->getMessage() );
  }
  try {
    $res = $dbh->prepare( $sql );
    $res->execute( $bind );
    return $res;
  } catch (PDOException $e) {
    die( $e->getMessage() );
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

# Environment

function req_get( $key, $def='' ) {
  return isset( $_GET[$key] ) ? $_GET[$key] : $def;
}

function req_post( $key, $def='' ) {
  return isset( $_POST[$key] ) ? $_POST[$key] : $def;
}

function req_put( $key, $def='' ) {
    return req_method('PUT') ? ( req_raw( $key ) ) : $def;
}

function req_patch( $key, $def='' ) {
  return req_method('PATCH') ? ( req_raw( $key ) ) : $def;
}

function req_raw( $key='', $def='' ) {
  $input = file_get_contents('php://input');
  if ( empty( $key ) )
    return $input;
  parse_str( $input, $raw );
  return isset( $raw[$key] ) ? $raw[$key] : $def;
}

function req_cookie( $key, $def='' ) {
  return isset( $_COOKIE[$key] ) ? $_COOKIE[$key] : $def;
}

function req_file( $key ) {
  return isset( $_FILES[$key] ) ? $_FILES[$key] : null;
}

function req_session( $key, $def='' ) {
  return isset( $_SESSION[$key] ) ? $_SESSION[$key] : $def;
}

function req_method( $key='' ) {
  $method = isset( $_POST['_method'] ) ? ucwords( $_POST['_method'] ) : getenv( 'request_method' );
  return ( !empty( $key ) ) ? ( $method === $key ) : $method;
}

function req_env( $key='' ) {
  return getenv( $key );
}

function req_base( $str='/' ) {
  return dirname( req_env('SCRIPT_NAME') ) . $str;
}

function add_header( $str, $code=null ) {
  header( $str , true, $code );
}

function redirect_to( $url ) {
  add_header( 'Location: ' . $url );
}

function req_status( $code=null ) {
  return http_response_code( $code );
}

function req_back() {
  redirect_to( req_env('HTTP_REFERER') );
}

function req_type( $type ) {
  add_header( 'Content-type: ' . $type );
}


# Cookie

function cookie_set( $key, $val='', $exp=null, $path='/',$domain=null, $secure=null, $httponly=null ) {
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

function ses_id( $new='' ) {
  return ( !empty( $new ) ) ? session_id( $new ) : session_id();
}

function ses_reset() {
  session_destroy();
}

# Flash

function set_flash( $key, $val ) {
  if ( !ses_id() )
    ses_start();
  $_SESSION['h_flash_msg'][$key] = $val;
}
function has_flash( $key ) {
  return isset( $_SESSION['h_flash_msg'][$key] );
}
function get_flash( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !has_flash( $key ) )
    return null;
  $val = $_SESSION['h_flash_msg'][$key];
  unset( $_SESSION['h_flash_msg'][$key] );
  return $val;
}
function keep_flash( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !has_flash( $key ) )
    return null;
  return $_SESSION['h_flash_msg'][$key];
}