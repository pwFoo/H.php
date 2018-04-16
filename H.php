<?php
 /**
  * H.php, The Minimalist PHP Framework!
  * @package H
  * @author Oyedele Hammed (devHammed)
  * @license http://opensource.org/licenses/mit-license.php  MIT License
  * @version 1.0
  */

/**
 * Route HTTP Requests
 * <code>
 *  route( 'GET', '/', function(){
 *    echo 'Hello World';
 *  });
 * </code>
 * @param str $type HTTP Method, Multiple methods can be separated by `|` and `ANY` can be used to match any Method.
 * @param str $regex The route regular expresions to match against current URL
 * @param callable $fn The route handler to call, passing a string separated with `#` e.g `indexController#index` will load contoller from `CONTROLLERS_DIR` path
 */

function route( $type, $regex, $fn ) {
  $type = explode( '|', $type );
  $type = array_map( 'trim', $type );
  $path = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '/';
  $req_valid = ( in_array( 'ANY', $type ) || in_array( req_method(), $type ) );
  if ( preg_match( '~^' . $regex . '/?$~', $path, $args ) && $req_valid ) {
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

/**
 * Renders a Native PHP template
 * @param str $file Name of the Template, just specify the name without `.php` and it will be loaded from `VIEWS_DIR`
 * @param array $vars The data to pass to the template file to use
 * @return str The compiled template
 * @example `echo send_view('helloView', array('name' => 'John Doe' ));`
 */

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

/**
 * Send JSON response
 * @param array|str|object The data to JSON encode.
 * @return str The JSON encoded data
 * @example `echo send_json( array('user' => 'Jane Doe') );`
 */

function send_json( $val ) {
  return json_encode( $val );
}

/**
 * Check if a $var is defined then escape HTML entities else escape $def
 * @param mixed $var The variable to check
 * @param mixed $def (Optional) The variable to return if $var is empty or null
 * @return mixed $var || $def
 */

function esc( $var, $def='' ) {
  return ( !empty( $var ) ) ? htmlspecialchars( $var ) : htmlspecialchars( $def );
}

/**
 * Run a SQL query on a database
 * @param str $sql The SQL to run, support placeholsers
 * @param array $bind The bindings to placeholders in $sql
 */

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

/**
 * Wrapper for `SELECT`, run the $sql and returns $rows array
 * @param str $sql SELECT SQL to run
 * @param array $bind Placeholders bindings array
 * @return array $rows array containing results
 */

function db_select( $sql, $bind=array() ) {
  $res = db_run( $sql, $bind );
  $res->setFetchMode( PDO::FETCH_ASSOC );
  $rows = array();
  while ( $row = $res->fetch() )
    $rows[] = $row;
  return $rows;
}

/**
 * Handles $_GET request
 * @param str $key The $_GET value
 * @param mixed $def default value to return if $key is not defined
 * @return mixed
 */

function req_get( $key, $def='' ) {
  return isset( $_GET[$key] ) ? $_GET[$key] : $def;
}

/**
 * Handles $_POST request
 * @param str $key The $_POST value
 * @param mixed $def default value to return if $key is not defined
 * @return mixed
 */

function req_post( $key, $def='' ) {
  return isset( $_POST[$key] ) ? $_POST[$key] : $def;
}

/**
 * Handles PUT request
 * @param str $key The PUT request key
 * @param mixed $def default value to return if $key is not defined
 * @return mixed
 */

function req_put( $key, $def='' ) {
    return req_method('PUT') ? ( req_raw( $key ) ) : $def;
}

/**
 * Handles PATCH request
 * @param str $key The PATCH request key
 * @param mixed $def default value to return if $key is not defined
 * @return mixed
 */

function req_patch( $key, $def='' ) {
  return req_method('PATCH') ? ( req_raw( $key ) ) : $def;
}

/**
 * Raw Request Body
 * @param str $key (optional) the request body key
 * @param mixed $def (optional) default value to return if $key is not defined
 * @return mixed
 */

function req_raw( $key='', $def='' ) {
  $input = file_get_contents('php://input');
  if ( empty( $key ) )
    return $input;
  parse_str( $input, $raw );
  return isset( $raw[$key] ) ? $raw[$key] : $def;
}

/**
 * Handles $_COOKIE
 * @param str $key The $_COOKIE key
 * @param mixed $def default value to return if $key is not defined
 * @return mixed $_COOKIE[$key] || $def
 */

function req_cookie( $key, $def='' ) {
  return isset( $_COOKIE[$key] ) ? $_COOKIE[$key] : $def;
}

/**
 * Handles $_FILES
 * @param str $key The file name/key
 * @return mixed $_FILES[$key] || null
 */

function req_file( $key ) {
  return isset( $_FILES[$key] ) ? $_FILES[$key] : null;
}

/**
 * Handles $_SESSION
 * @param str $key The file name/key
 * @return mixed $_SESSION[$key] || null
 */

function req_session( $key, $def='' ) {
  return isset( $_SESSION[$key] ) ? $_SESSION[$key] : $def;
}

/**
 * Returns the Request method or compare it with `$key` if specified
 * @param str $key (optional) compare the current request method
 * @return str $key == $method || getenv('request_method')
 */

function req_method( $key='' ) {
  $method = isset( $_POST['_method'] ) ? ucwords( $_POST['_method'] ) : getenv( 'request_method' );
  return ( !empty( $key ) ) ? ( $method === $key ) : $method;
}

/**
 * Wrapper for $_SERVER variables
 * @param str $key Environment variable key
 * @return mixed get_env( $key )
 */

function req_env( $key ) {
  return getenv( $key );
}

/**
 * Return base URL for the app, it will concat `$str` with the base URL if specified.
 * @param str $str string to append to the base URL to form a complete URL
 * @return str $str
 */

function req_base( $str='/' ) {
  return dirname( req_env('SCRIPT_NAME') ) . $str;
}

/**
 * Add a header to the list of headers that will be sent to the client
 * @param str $str The header content e.g `Content-Type: text/html`
 * @param int $code The status code to add when sending the header
 */

function add_header( $str, $code=null ) {
  header( $str , true, $code );
}

/**
 * Redirect to $url, you can use it with `req_base` to redirect within the app
 * @param str $url URL to redirect to ( Absolute / Relative )
 */

function redirect_to( $url ) {
  header( 'Location: ' . $url );
}

/**
 * Set the cookie, it is basically a wrapper for `setcookie`
 */

function cookie_set( $key, $val='', $exp=null, $path='/',$domain=null, $secure=null, $httponly=null ) {
  setcookie( $key, $val, time() + $exp, $path, $domain, $secure, $httponly );
}

/**
 * Returns $key if it exists in $_COOKIE array else returns $def
 */

function cookie_get( $key, $def='' ) {
  return req_cookie( $key, $def );
}

/**
 * Check if a `$key` exists in the $_COOKIE array
 */

function cookie_has( $key ) {
  return isset( $_COOKIE[$key] );
}

/**
 * Delete a cookie ( if exists ) from $_COOKIE array, remember to specify all what you add when setting the cookie
 */

function cookie_delete( $key, $path='/', $domain=null, $httponly=null ) {
  if ( cookie_has( $key ) )
    cookie_set( $key, null, time() - (3600 * 3650), $path, $domain, $httponly );
}

/**
 * Empty the $_COOKIE array
 */

function cookie_reset() {
  $_COOKIE[] = array();
}

/**
 * Start a Session if not started
 */

function ses_start() {
  if ( !ses_id() )
    session_start();
}

/**
 * Set `$key` with the value `$val` in the Session
 */

function ses_set( $key, $val ) {
   $_SESSION[$key] = $val;
}

/**
 * Returns `$key` value if it exists in $_SESSION array else return `$def`
 */

function ses_get( $key, $def='' ) {
  return req_session( $key, $def );
}

/**
 * Check if `$key` is in $_SESSION array
 */

function ses_has( $key ) {
  return isset( $_SESSION[$key] );
}

/**
 * Delete a Session value if $key exists
 */

function ses_delete( $key ) {
  if ( ses_has( $key ) )
    unset( $_SESSION[$key] );
}

/**
 * Set or Get the Session ID
 */

function ses_id( $new='' ) {
  return ( !empty( $new ) ) ? session_id( $new ) : session_id();
}

/**
 * Destroy the current Session
 */

function ses_reset() {
  session_destroy();
}

/**
 * Set a Flash message that would be display in the next call of `get_flash`
 * @param str $key The key of the message
 * @param mixed $val The value of the message
 */

function set_flash( $key, $val ) {
  if ( !ses_id() )
    ses_start();
  $_SESSION['h_flash_msg'][$key] = $val;
}

/**
 * Check if there is a Flash message with Key `$key`
 */

function has_flash( $key ) {
  return isset( $_SESSION['h_flash_msg'][$key] );
}

/**
 * Get the value of the Flash message $key then delete it.
 */

function get_flash( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !has_flash( $key ) )
    return null;
  $val = $_SESSION['h_flash_msg'][$key];
  unset( $_SESSION['h_flash_msg'][$key] );
  return $val;
}

/**
 * Get the value of the Flash message $key but dont delete it.
 */

function keep_flash( $key ) {
  if ( !ses_id() )
    ses_start();
  if ( !has_flash( $key ) )
    return null;
  return $_SESSION['h_flash_msg'][$key];
}
