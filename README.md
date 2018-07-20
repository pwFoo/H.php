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
  composer create-project --prefer-dist devhammed/h-php project_dir "*"
```
> Where `project_dir` is the directory you want H.php to be installed, if it does not exists Composer will create it.

You can also download release archive from [here](https://github.com/devHammed/H.php/releases/latest) and extract into your project folder.

<a id="project-structure"></a>
## Project Structure
Below is the Project Structure of H.php and their explanation in parentheses:

```
  -- project_dir
    -- app ( application directory )
      -- Controllers (Controller files, optional)
      -- Models (Model files, optional)
      -- Views (View files, optional)
      bootstrap.php (Startup file, don't edit)
      config.php (Configuration constants, edit to suite your project)
    -- core (H.php framework files)
    -- vendor (Composer files if you are using it)
    index.php (Front controller)
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
  $app = new H\App();

  # define routes
  $app->get( '/', function( $self ) {
    return 'Hello World!';
  } );

  # run the application
  $app->run();
```
That is just the basic example to create H.php projects, run below command to start PHP Built-in server in the project directory.

```bash
  php -S localhost:8080 index.php
```
Now visit [localhost:8080](localhost:8080) to see the app running, you can use any Web Server but this is just to show that it can work even with the simplest Server implementations. You will learn more about routing and the `$self` parameter in the callback function in the next section.

<a id="routes"></a>
## Routes
You can define routes using HTTP verbs shorthand methods on the H\App instance or use the multiple HTTP verbs method.

### GET method
> You can add a route that handles GET HTTP requests with the H.php instance get() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->get( '/users/@id', function( $self ) {
    # User with $self->args['id']
  } );
```

### POST method
> You can add a route that handles POST HTTP requests with the H.php instance post() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->post( '/users', function( $self ) {
    # Create new User
  } );
```

### PUT method
> You can add a route that handles PUT HTTP requests with the H.php instance put() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->put( '/users/@id', function( $self ) {
    # Update User with $self->args['id']
  } );
```

### DELETE method
> You can add a route that handles DELETE HTTP requests with the H.php instance delete() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->delete( '/users/@id', function( $self ) {
    # Delete User with $self->args['id']
  } );
```

### PATCH method
> You can add a route that handles PATCH HTTP requests with the H.php instance delete() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->patch( '/users/@id', function( $self ) {
    # Patch User info with $self->args['id']
  } );
```

### OPTIONS method
> You can add a route that handles OPTIONS HTTP requests with the H.php instance options() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->options( '/users', function( $self ) {
    # Show currrent route information
  } );
```

### ANY method
> You can add a route that handles any HTTP requests with the H.php instance any() method. It accepts 2 arguments:
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->any( '/users/@id', function( $self ) {
    # Detect Request Method with $self->request->method()
    # Delete User with $self->args['id']
  } );
```

### Multiple methods
> You can add a route that handles multiple HTTP requests with the H.php instance map() method. It accepts 3 arguments:
- (string) The route method(s), separated by `|`
- (string) The route pattern
- (callable) The route callback
```php
  $app = new H\App();
  $app->map( 'GET | HEAD', '/users', function( $self ) {
    # Do some magic!
  } );
```

### Group routes
> You can add group routes with the same base path together using H.php instance group() method. It accepts 2 arguments:
- (string) The route pattern
- (array) The child routes, Array item key should contain method and path separated by `->` and Value should be the callback handler.
```php
  $app = new H\App();
  $app->group( '/users', array(
    'GET' => function( $self ) {
      # base route handler
    },
    'GET -> /@id' => function( $self ) {
      # Get User `$self->args['id]` details
    },
  ) );
```

### Dynamic routes
Each routing method described above accepts a URL pattern that is matched against the current HTTP request URI. The patterns can use named parameters to dynamically match HTTP request URI.
#### How To
A route named parameter should be prefixed with `@` e.g
```php
  $app = new H\App();
  $app->get( '/user/@id', function( $self ) {
    return $self->args['id'] . ' profile';
  } );
```
#### Optional Named parameter
A optional named parameter should be wrapped in parentheses, it also support nesting e.g
```php
  $app = new H\App();
  $app->get( '/users(/@id)', function( $self ) {
    # handles /users, /user/ and /users/1
  } );

  $app->get( '/archives(/@year(/@month))', function ( $self ) {
    # handles `/archives`, `/archives/2016` and `/archives/2016/03`
  } );
```
#### Custom Regular Expressions
H.php route named parameters accept any value by default but you can specify your own custom Regular Expressions for parameters, you just have to separate the named parameter and Regex with `:` e.g
```php
  $app = new H\App();
  $app->get( '/users/@id:[0-9]+', function( $self ) {
    # Get User `$self->args['id']` details
  } );
```

### $self Parameter
> You will notice that a parameter `$self` is been passed to route callback functions. This Object contains the following:
- (object) `response`
- (object) `request`
- (object) `database`
- (object) `session`
- (object) `cookie`
- (object) `config`
- (object) `flash`
- (object) `hash`
- (array) `args` (route parameters)

You can also access this Object through `app` property of H.php instance e.g `$app->app`.

### 404 handler
> You can add a route that handles 404 HTTP requests with the H.php instance notFound() method, if you didn't add the handler the default message will be `404 Error - Page not found!`. It accepts 1 arguments:
- (callable) The 404 callback
```php
  $app = new H\App();
  $app->get( '/', function( $self ) {
    return 'Hello World';
  } );
  $app->notFound( function( $self ) {
    return '404';
  } );
```

Congratulations for making it this far, we are now going to walk through the Objects in `$self` parameters and their methods!

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

- @param (string) `$key` FILES parameter key
- @returns (string | bool)

### header( $key )
This method returns Client Header value for `$key` else returns NULL
- @param (string) `$key` Header key e.g `X-API-Key`.
- @returns (string | null)

### hasHeader( $key )
Returns TRUE if `$key` exists in the Headers array else FALSE
- @param (string) `$key` Header key
- @returns (bool)

### env( $key, $def=NULL )
This method returns SERVER value for `$key` else returns NULL
- @param (string) `$key` SERVER key e.g `SCRIPT_NAME`.
- @returns (string | null)

### base( $str=NULL )
This method returns the base path for the projects, if `$str` is not null it get appended to the base path.
- @param (string) `$str` Path to append to the base
- @returns (string)

### site( $str=NULL )
This method returns the full path for the projects, if `$str` is not null it get appended to the base path.
- @param (string) `$str` Path to append to the full path
- @returns (string)


<a id="response"></a>
### Response
H.php comes with functions to send response and views to the client.

```php
  res_addHeader( $header, $code=NULL );
```

> The function can be used to add header, $header is the header string e.g `Content-Type: text/html` and $code is the status code for the response.

```php
  res_redirect( $url );
```

> The function can be used to redirect to $url, it can be used with `req_base()` to redirect using relative url.

```php
  res_back();
```

> This function can be used to redirect back to the previous page e.g after form submission.

```php
  res_status( $code=NULL );
```
> Set HTTP status code if $code is passed else return the current status code.

```php
  res_type( $content_type );
```
> Set the response Content type e.g `application/json`.

```php
  # $name ( string ): The filename of the template to be included from the directory path defined in configuration `VIEWS_DIR`. you dont need to append `.php` extension.

  # $vars ( associative array ): The data that get passed to the View.

  res_render( $name, $vars=NULL );
```
> This function can be used to render native PHP templates to the browser.

```php
  res_renderStr( $tpl, $vars=NULL );
```
> This works almost like `res_render()` but it renders template from `$tpl` string instead of a file.

```php
  esc( $var );
```
> This function can be used to HTML-escape a variable to output.

```php
  res_json( $data );
```
> This function can be used to send JSON data.

```php
  res_jsonp( $data,  $callback='callback' );
```
> This function can be used to send JSONP script. NOTE: the request URL should include `$callback` query param with the value of JavaScript function to call e.g `http://www.mysite.tld/api/jsonp/?callback=myFunc`.

Below are the guidelines on how to use native PHP for template:-
- Always use HTML with inline PHP. Never use blocks of PHP.
- Always use `esc( $var )` function to escape potentially dangerous variables.
- Always add `<?php if ( ! defined( 'H_PHP' ) ) die( 'Permission denied!' ) ?>` at the top of your view file to prevent direct access to it.
- Always use the short echo syntax (`<?=`) when outputting variables. For other inline PHP code, use the full `<?php` tag.
- Always use the [PHP alternative syntax for control structures](http://php.net/manual/en/control-structures.alternative-syntax.php), which are designed to make templates more legible.
- Never use PHP curly brackets.
- Only put one statement in each PHP tag.
- Avoid using semicolons. They are not needed when there is only one statement per PHP tag.
- Never use the `for`, `while` or `switch` control structures. Instead use `if` and `foreach`.
- Avoid variable assignment.

Example:
```php
  # index.php

  config_set( 'VIEWS_DIR', 'myViews/' );

  route( 'GET', '/user', function() {
    return res_render( 'listUsersView', array(
      'users' => 'John Doe' //some data here
    ))
  } );

  route( 'GET', '/user/@name', function( $args ) {
    return res_render( 'userView', array(
      'name' => $args['name'],
    ) );
  } );

  # myViews/userView.php
  <?php if ( ! defined( 'H_PHP' ) ) die( 'Permission Denied!' ); ?>

  <?= esc( $name ) ?>
```

<a id="database"></a>
### Database
H.php comes with two functions that allows you to interact with a database using PDO connection. Both of the two functions supports Parameter placeholders, you can read more on PDO to understand this better.

```php
  db_run( $sql, $bind=array() ); // => $res
```
> `db_run` allows you to run SQL queries on your Database and it PDO response object.

```php
  db_select( $sql, $bind=array() ); // => array || null
```
> `db_select` is designed only to run SQL SELECT query and it returns array containing the results.

Example:
```php
# index.php

# define configurations here

route( 'GET', '/posts', function(){
  $posts = db_select( 'SELECT * FROM posts' );
  return res_render( 'postsView', array( 'posts' => $posts ) );
} );

# views/postsView.php
  <?php if ( ! defined( 'H_PHP' ) ) die( 'Permission Denied!' ); ?>

  <?php if ( count( $posts ) ): ?>
    <?php foreach ( $posts as $post ): ?>
      <h1><?= $post['title'] ?></h1>
      <p><?= $post['body'] ?></p>
    <?php endforeach ?>
  <?php else: ?>
    <p>No posts in this blog!</p>
  <?php endif ?>

```

<a id="cookie"></a>
### Cookie
H.php also comes with Cookie utilities:-

```php
  cookie_set( $key, $value='', $expires=null, $path='/', $domain=null, $secure=null, $httponly=null );
```

> This function is used to set Cookie, it just a wrapper around PHP `setcookie()` function so no complicated explanation is required. NOTE: As per standard, you must set Cookie before sending output to the browser.

```php
  cookie_get( $key, $def='' );
```

> This function can be used to get Cookie if it is defined else it will return `$def`.

```php
  cookie_has( $key );
```

> This function can be used to check if a Cookie has been set.

```php
  cookie_delete( $key, $path='/', $domain=null, $httponly=null );
```

> This function is used to delete a Cookie if it has been set. NOTE: you should add $path, $domain and $httponly if you added them when setting the Cookie else you should only pass `$key`.

```php
  cookie_reset();
```

> This function can be used to clear all Cookies.

<a id="session"></a>
### Session

H.php comes with bunch of Session utilities:-

```php
  ses_start();
```

> Starts a session

```php
  ses_set( $key, $value );
```

> Set a Session value

```php
  ses_get( $key, $def='' );
```

> Returns Session value is it exist else returns $def.

```php
  ses_has( $key );
```

> Check if a Session value has been set.

```php
  ses_delete( $key );
```

> Deletes Session value if it has been set.

```php
  ses_id( $new='' );
```

> Get the current Session ID or Set it if `$new` is passed.

```php
  ses_reset();
```

> Destroys the current Session.

<a id="flash"></a>
### Flash Messages

Flash Messages are temporary Session values that gets deleted after the next called of `flash_get( $key )`. Let's first explain the functions then an Example will follow:-

```php
  flash_set( $key, $value );
```

> This function can be used to set a Flash message.

```php
  flash_get( $key );
```

> This function returns the Flash message value if it exists then delete it.

```php
   flash_has( $key );
```

> This function can be used to check if a Flash message has been set.

```php
  flash_keep( $key );
```

> This function is the opposite of `flash_get`, it returns a Flash message value if it exists but it does not delete it so it can be used in the next View.

Example:-

```php
 # index.php

 # config and settings here

 route( 'POST', '/comment', function() {
   # insert something into database
   flash_set( 'message', 'Blah blah blah' ); # set a message
   res_back(); # go back
 }

 # demoView.php

 <?php if ( flash_has( 'message' ) ): ?>
   <p id="message--text"><?= flash_get( 'message' ) ?></p>
 <?php endif ?>

 # ...
```

<a id="hash"></a>
### Hash
H.php also has functions that can be used to hash passwords or strings, the functions are basically wrappers around PHP `password_hash` and `password_verify` functions.

```php
  hash_make( $str, $algo=PASSWORD_DEFAULT, $opts=NULL );
```
> This function can be used to generate secure hash for `$str`.

>`$algo` is the algorithm to use when generating hash, it can be `PASSWORD_DEFAULT` or `PASSWORD_BCRYPT` or `PASSWORD_ARGON2I`. it defaults to `PASSWORD_DEFAULT` which will use the latest secured algorithm in PHP.

> `$opts` are options to use when generating, Read more at [PHP password_hash](http://php.net/manual/en/function.password-hash.php).

>This function can be used to hash a password before saving to the database.

```php
  hash_check( $str, $hash );
```
> This function can be used to verify a `$hash` against `$str`, returns `TRUE` if it is valid else returns `FALSE`. This is useful for verifying password entered by user against the hashed version in the database. Read more at [PHP password_verify](http://php.net/manual/en/function.password-verify.php).

```php
  hash_needsRehash( $hash, $algo=PASSWORD_DEFAULT, $opts=NULL );
```
> This function checks if the given hash matches the given options and algorithm. returns `TRUE` if matched else returns `FALSE`. Read more at [PHP password_needs_rehash](http://php.net/manual/en/function.password-needs-rehash.php).

```php
  hash_random( $length=32 );
```
> This function returns random md5 hash string, it can be used to generate Coupon/Promotional code, Referral code or unique file names. you can specify `$length` of the the hash but it returns 32 characters by default.


<a id="resources"></a>
### Resources / Projects

Below are links to some resources and examples to get you started.

* [H.php Boilerplate Project](https://github.com/devHammed/H.php-Boilerplate)
* [Simple Chat Project](https://github.com/devHammed/H.php-Chat)
* [Simple Link Shortener](https://github.com/devHammed/H.php-Shorty)

<hr>

Thanks, did you want to contribute? fork this repository and send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.