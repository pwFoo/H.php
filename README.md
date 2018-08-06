# H.php

> The Minimalist PHP Framework!

## Table of Contents
* [Introduction](#intro)
* [Installation](#install)
* [Project Structure](#project-structure)
* [Hello World](#hello-world)
* [Routes](#routes)
* [Request](#request)
* [Response](#response)
* [View](#view)
* [Database (DB)](#database)
* [Configuration](#configuration)
* [Cookies](#cookie)
* [Session](#session)
* [Flash Messages](#flash)
* [Hash](#hash)
* [Resources / Projects](#resources)



<a id="intro"></a>
## Introduction
H.php is a minimal PHP framework that is designed for you to quickly prototype and create idiomatic Web applications and APIs.

<a id="install"></a>
## Installation
H.php can be installed using `Composer` using using below command:-

```bash
  composer create-project --prefer-dist devhammed/h-php project_dir
```
> Where `project_dir` is the directory you want H.php to be installed, if it does not exists Composer will create it.

You can also download release archive from [here](https://github.com/devHammed/H.php/releases/latest) and extract into your project folder.

<a id="project-structure"></a>
## Project Structure
Below is the Project Structure of H.php and their explanation in parentheses:

```
  -- project_dir
    -- app ( application directory )
      -- controllers (Controller files, optional)
      -- models (Model files, optional)
      -- views (View files, optional)
      bootstrap.php (Startup file, don't edit)
      config.php (Configuration constants, edit to suite your project)
    -- core (H.php framework files)
    -- vendor (Composer files if you are using it)
    index.php (Front controller)
    server.php (For PHP Builtin server)
    .htaccess
    nginx-server.conf
```
NOTE: H.php will autoload all this files for you, you only need to include `app/bootstrap.php` file in your `index.php`. And for Nginx users, you don't need `.htaccess` but instead copy the contents of `nginx-server.conf` file to your server configurations.

<a id="hello-world"></a>
## Hello World
After getting familiar with H.php project structure, if you check `index.php` file. you will see this:-

```php
  # require bootstrap.php file
  require 'app/bootstrap.php';

  # create new application
  $app = new \H\App();

  # define routes
  $app->get( '/', function( $h ) {
    return 'Hello World!';
  } );

  # run the application
  $app->run();
```

That is just the basic example to create H.php projects, run below command to start PHP Built-in server in the project directory.

```bash
  php -S localhost:8080 server.php
```

Now visit [localhost:8080](localhost:8080) to see the app running, `server.php` is a PHP script that mimics URL Rewrite functionality like Apache `.htaccess` or Nginx Server Configurations for the PHP Builtin Server. you can use any Web Server but this is just to show that it can work even with the simplest Server implementations. You will learn more about routing and the `$h` parameter in the callback function in the next section.

<a id="routes"></a>
## Routes
You can define routes using HTTP verbs shorthand methods on the H\App instance or use the multiple HTTP verbs method.

### GET method
You can add a route that handles GET HTTP requests with the H.php instance get() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->get( '/users/@id', function( $h ) {
    # Display User $h->args['id'] details
  } );
```

### POST method
You can add a route that handles POST HTTP requests with the H.php instance post() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->post( '/users', function( $h ) {
    # Create new User, access POST data with $h->request->post() method
  } );
```

### PUT method
You can add a route that handles PUT HTTP requests with the H.php instance put() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->put( '/users/@id', function( $h ) {
    # Update User $h->args['id'] details
  } );
```

### DELETE method
You can add a route that handles DELETE HTTP requests with the H.php instance delete() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->delete( '/users/@id', function( $h ) {
    # Delete User $h->args['id']
  } );
```

### PATCH method
You can add a route that handles PATCH HTTP requests with the H.php instance delete() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->patch( '/users/@id', function( $h ) {
    # Patch User $h->args['id'] details
  } );
```

### OPTIONS method
You can add a route that handles OPTIONS HTTP requests with the H.php instance options() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->options( '/users', function( $h ) {
    # Show currrent route options
  } );
```

### ANY method
You can add a route that handles any HTTP requests with the H.php instance any() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->any( '/users/@id', function( $h ) {
    # Detect Request Method with $h->request->method()
  } );
```

### Multiple methods
You can add a route that handles multiple HTTP requests with the H.php instance map() method. It accepts 3 arguments:

- (string) The route HTTP method(s), separated by `|`.
- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->map( 'GET | HEAD', '/users', function( $h ) {
    # Do some magic here!
  } );
```

### Group routes
You can add group routes with the same base path together using H.php instance group() method. It accepts 2 arguments:

- (string) The route pattern
- (array) The child routes, Array item key should contain method and path separated by `->` and Value should be the callback handler.

```php
  $app = new \H\App();
  $app->group( '/users', array(
    'GET|OPTIONS' => function( $h ) {
      # base route handler
    },
    'GET -> /@id' => function( $h ) {
      # Get User `$h->args['id]` details
    },
  ) );
```

### Dynamic routes
Each routing method described above accepts a URL pattern that is matched against the current HTTP request URI. The patterns can use named parameters to dynamically match HTTP request URI.

#### How To
A route named parameter should be prefixed with `@` e.g

```php
  $app = new \H\App();
  $app->get( '/user/@id', function( $h ) {
    return $h->args['id'] . ' profile';
  } );
```
#### Optional Named parameter
A optional named parameter should be wrapped in parentheses, it also support nesting e.g

```php
  $app = new \H\App();
  $app->get( '/users(/@id)', function( $h ) {
    # handles /users, /users/ and /users/1
  } );

  $app->get( '/archives(/@year(/@month))', function ( $h ) {
    # handles `/archives`, `/archives/2016` and `/archives/2016/03`
  } );
```
#### Custom Regular Expressions
H.php route named parameters accept any value by default but you can specify your own custom Regular Expressions for parameters, you just have to separate the named parameter and Regex with `:` e.g

```php
  $app = new \H\App();
  $app->get( '/users/@id:[0-9]+', function( $h ) {
    # Get User `$h->args['id']` details
  } );
```

### Callback handler
Callback handlers must be of PHP callable type and below formats are acceptable according to the PHP standards documentation. for Classes static or instance methods, their signature should be like Closure functions and for stress-free experience you should add your classes in `app/controllers` directory so that they can be autoloaded into your app.

```php
  # Closure functions
  $app->get( '/', function( $h ) {
    # closure
  } );

  # Class static methods
  $app->get( '/', array( 'Class', 'staticMethod' ) );

  # Class instance methods
  $obj = new MyClass();
  $app->get( '/', array( $obj, 'method' ) );

  # and other callable types...
```

### $h Parameter
You will notice that a parameter `$h` is been passed to route callback handlers, this object includes all the core objects in H.php that can be used to create a powerful APIs and Web applications. it contains the following methods / properties:

- (object) `response` (Response object)
- (object) `request` (Request object)
- (object) `db` (Database object)
- (object) `session` (Session object)
- (object) `cookie` (Cookie object)
- (object) `config` (Configuration object)
- (object) `flash` (Flash Messages object)
- (object) `hash`(Hash object)
- (array) `args` (Current Route parameters)
- (string) `url` (Current Request URL)

You can also access this Object through `h` property of H.php instance e.g `$app->h`.

### 404 handler
You can add a route that handles 404 HTTP requests with the H.php instance notFound() method, if you didn't add the handler the default message will be `404 Error - Page not found!`. It accepts 1 arguments:

- (callable) The 404 callback

```php
  $app = new \H\App();
  $app->get( '/', function( $h ) {
    return 'Hello World';
  } );
  $app->notFound( function( $h ) {
    return '404';
  } );
```

Congratulations for making it this far, we are now going to walk through the Objects in `$h` parameters and their methods!

<a id="request"></a>
## Request
H.php comes with a minimal Request Object that does just enough, lets walk through its methods and properties.

### get( $key, $def=NULL )
This method returns GET Request parameter value for `$key`.

- @param (string) `$key` GET parameter key
- @param (mixed) `$def` A default value.
- @returns (mixed) returns `$key` if isset else returns `$def`.

### post( $key, $def=NULL )
This method returns POST Request parameter value for `$key`.

- @param (string) `$key` POST parameter key
- @param (mixed) `$def` A default value.
- @returns (mixed) returns `$key` if isset else returns `$def`.

### put( $key, $def=NULL )
This method returns PUT Request parameter value for `$key`.

- @param (string) `$key` PUT parameter key
- @param (mixed) `$def` A default value.
- @returns (mixed) returns `$key` if isset else returns `$def`.

### patch( $key, $def=NULL )
This method returns PATCH Request parameter value for `$key`.

- @param (string) `$key` PATCH parameter key
- @param (mixed) `$def` A default value.
- @returns (mixed) returns `$key` if isset else returns `$def`.

### raw( $key=NULL, $def=NULL )
This method returns raw Request body and parse it if `$key` is not null else returns the raw body.

- @param (string) `$key` raw parameter key
- @param (mixed) `$def` A default value.
- @returns (mixed) returns raw Body if `$key` is NULL else if `$key` is not in parsed Body it returns `$def` else returns `$key` value in parsed Body.

### files( $key )
This method returns FILES parameter value for `$key`.

- @param (string) `$key` FILES parameter key
- @returns (mixed) returns `$key` if isset else returns NULL.

### method( $key='' )
This method returns Request method if `$key` is NULL else compare `$key` to current Request method and returns TRUE or FALSE.
It is possible to fake or override the HTTP request method. This is useful if, for example, you need to mimic a PUT request using a traditional web browser that only supports GET or POST requests.
There are two ways to override the HTTP request method.

1. You can include a `_method` parameter in a POST request’s body. The HTTP request must use the `application/x-www-form-urlencoded` Content type.
2. You can also override the HTTP request method with a custom `X-Http-Method-Override` HTTP request header. This works with any HTTP request content type.

- @param (string) `$key` The HTTP method to compare against the current method
- @returns (string | bool)

### header( $key )
This method returns Client Header value for `$key` else returns NULL

- @param (string) `$key` Header key e.g `X-API-Key`.
- @returns (string|null)

### hasHeader( $key )
Returns TRUE if `$key` exists in the Headers array else FALSE

- @param (string) `$key` Header key
- @returns (bool)

### env( $key, $def=NULL )
This method returns SERVER value for `$key` else returns NULL

- @param (string) `$key` SERVER key e.g `SCRIPT_NAME`.
- @returns (string|null)

### base( $str=NULL )
This method returns the base path for the projects, if `$str` is not null it get appended to the base path.

- @param (string) `$str` Path to append to the base
- @returns (string)

### site( $str=NULL )
This method returns the full path for the projects, if `$str` is not null it get appended to the base path.

- @param (string) `$str` Path to append to the full path
- @returns (string)

<a id="response"></a>
## Response
H.php comes with a minimal Response Object that does just enough, lets walk through its methods and properties.

### addHeader( $str, $code=null )
This method is can be used to send HTTP Headers to the Client.

- @param (string) `$str` HTTP Header e.g `Location: /new-path`
- @param (int) `$code` HTTP Response Code e.g `302`
- @returns `null`

### redirect( $url )
This method can be used to Redirect the client, it uses `addHeader()` under the hood.

- @param (string) `$url` The URL to redirect to.
- @returns `null`

### statusCode( $code )
This method can be used to indicate the HTTP Response Code of the current Request e.g `401`

- @param (int) `$code` The Response Code
- @returns (bool)

### back()
This method can be used to Redirect the User back to the previous URL. it uses HTTP Referer Header if set else it reload the current page.

### type( $type )
This method can be used to set the Response Content Type Header e.g `application/json`.

- @param (string) HTTP Response type
- @returns null

### json( $data )
This method can be used to send Array as JSON data to the client. it also use `type()` method to set the Response Content type o you don't need to.

- @param (array) `$data` The Array to send as JSON.
- @returns (string) The JSON data.

### jsonp( $data, $func='callback' )
This method is a secured and simple function to send JSONP data.

- @param (array) `$data` The Array to send as JSON
- @param (string) `$func` The JSONP callback function Query string key, default: `callback`.
- @returns (string) The JavaScript containing JSONP data and Function call.

<a id="view"></a>
## View
H.php does not actually come with a View Class but you can take advantage of Output Buffering and Control Structure Alternative Syntax to create powerful template system, you are also free to plug in any Template System of your choice. Below is an example code making use of it.

```php
  require 'app/bootstrap.php';

  $app = new \H\App();
  $app->get( '/', function( $h ) {
    ob_start();
    # declare your variables here
    include 'app/views/index.php';
    return ob_get_clean();
  } );
  $app-run();
```

<a id="database"></a>
## Database (DB)
H.php comes with a PDO-based Database CRUD class. to use this class make sure to edit below constants in `app/config.php` file.

- DB_NAME
- DB_USER
- DB_PASS
- DB_HOST

The DB class uses MySQL/MariaDB DSN for PDO by default but you can set your own custom DSN using `DB_DSN` constant to suit your Database, below are some DSN for popular Databases :-

Now, i hope you have understand how H.php DB class works under the hood. Lets walk through the methods available in this Class.

### run( $sql, $bind=array() )
This method can be used to used to run Raw SQL queries and it is also the base methods for other methods in DB class.

- @param (string) `$sql` The SQL Query
- @param (array) `$bind` The Query Bindings (if used)
- @returns (PDOStatement) The result Object for further operations

### select( $sql, $bind=array() )
This method is a wrapper around `run()` for the SELECT query.

- @param (string) `$sql` The SQL SELECT query
- @param (array) `$bind` the Query bindings (if used)
- @returns (array) The result rows Associative Array

### insert( $table, $bind=array() )
This method is a wrapper around `run()` for the INSERT operation.

- @param (string) `$table` The Table to insert data into
- @param (array) `$bind` Key and Value data to insert
- @returns (int) The last Insert ID.

### update( $table, $bind=array(), $where=null )
This method is a wrapper around `run()` for the UPDATE operation.

- @param (string) `$table` The table to Update
- @param (array) `$bind` Key and Value data to Update
- @param (string) `$where` The Update operation WHERE clause
- @returns (int) The last Insert ID

### delete( $table, $where=null, $limit=null )
This method is a wrapper around `run()` for the DELETE operation.

- @param (string) `$table` The table to delete data from
- @param (string) `$where` The Delete operation WHERE clause
- @param (int) `$limit` How many rows to delete, defaults to Delete all.

<a id="configuration"></a>
## Configuration
H.php also comes with Config object to handle your Configurations and Plugins in more eloquent way than using Global constants. lets walk through it methods.

### set( $key, $val )
Add new Configuration or Plugin.

- @param (string) `$key` The Identifier for this Configuration or Plugin
- @param (mixed) `$val` The value, it can be of any type.

### get( $key, $def=null )
Get the value of a Configuration or Plugin.

- @param (string) `$key` The Identifier for the Configuration or Plugin
- @param (mixed/null) `$def` The default value to return if Configuration have not been set, defaults to `null`.
- @returns (mixed) The value for `$key` if it have been set else returns `$def`.

### has( $key )
This method can be used to check if a Configuration have been set, some of the other methods used this internally e.g get / remove.

- @param (string) `$key` The Identifier for the Configuration or Plugin
- @returns (bool) True / False depending on the status of `$key`.

### remove( $key )
Remove Configuration from Object if it has been set.

- @param (string) `$key` The Identifier for the Configuration or Plugin

### reset()
Be very careful when using this method, it can be used to empty the Configurations array.

<a id="cookies"></a>
## Cookies

> Coming soon...

Thanks, did you want to contribute? fork this repository and send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.
