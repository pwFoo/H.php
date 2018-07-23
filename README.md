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
* [Database](#database)
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
    nginx
```
NOTE: H.php will autoload all this files for you, you only need to include `app/bootstrap.php` file in your `index.php`. And for Nginx users, you don't need `.htaccess` but instead copy the contents of `nginx` file to your server configurations.

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
    # User with $h->args['id']
  } );
```

### POST method
You can add a route that handles POST HTTP requests with the H.php instance post() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->post( '/users', function( $h ) {
    # Create new User
  } );
```

### PUT method
You can add a route that handles PUT HTTP requests with the H.php instance put() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->put( '/users/@id', function( $h ) {
    # Update User with $h->args['id']
  } );
```

### DELETE method
You can add a route that handles DELETE HTTP requests with the H.php instance delete() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->delete( '/users/@id', function( $h ) {
    # Delete User with $h->args['id']
  } );
```

### PATCH method
You can add a route that handles PATCH HTTP requests with the H.php instance delete() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->patch( '/users/@id', function( $h ) {
    # Patch User info with $h->args['id']
  } );
```

### OPTIONS method
You can add a route that handles OPTIONS HTTP requests with the H.php instance options() method. It accepts 2 arguments:

- (string) The route pattern
- (callable) The route callback

```php
  $app = new \H\App();
  $app->options( '/users', function( $h ) {
    # Show currrent route information
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
    # Delete User with $h->args['id']
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
    # Do some magic!
  } );
```

### Group routes
You can add group routes with the same base path together using H.php instance group() method. It accepts 2 arguments:

- (string) The route pattern
- (array) The child routes, Array item key should contain method and path separated by `->` and Value should be the callback handler.

```php
  $app = new \H\App();
  $app->group( '/users', array(
    'GET' => function( $h ) {
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
    # handles /users, /user/ and /users/1
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

### $h Parameter
You will notice that a parameter `$h` is been passed to route callback functions, this object includes all the core objects in H.php that can be used to create a powerful APIs and Web applications. it contains the following methods / properties:

- (object) `response`
- (object) `request`
- (object) `db`
- (object) `session`
- (object) `cookie`
- (object) `config`
- (object) `flash`
- (object) `hash`
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
H.php comes with a minimal Request Object that does just enough, lets walk through its methods.

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

> Will add docs for other Objects soon.

Thanks, did you want to contribute? fork this repository and send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.