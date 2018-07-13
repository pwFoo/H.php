# H.php

![H.php Logo](H.png)

> The Minimalist PHP Framework!

## Table of Contents
* [Introduction](#intro)
* [Installation](#install)
* [Tests](#tests)
* [Configuration](#configuration)
* [Routes](#routes)
* [Request](#request)
* [Response](#response)
* [Database](#database)
* [Cookies](#cookie)
* [Session](#session)
* [Flash Messages](#flash)
* [Hash](#hash)
* [Resources / Projects](#resources)



<a id="intro"></a>
### Introduction
> H.php is a Minimal and Lightweight PHP Function-based MVC-ish framework that is designed for you to prototype and build Web Applications and APIs without stress.

<a id="install"></a>
### Installation
Download latest release zip archive [here](https://github.com/devHammed/H.php/releases/latest) then extract and copy `.htaccess` file to your project root and `H.php` file in your project then you will require the file. If you are running PHP Built-in server you don't need the `.htaccess`, H.php will take care of the route handling when you start the server with the command `php -S localhost:PORT index.php` this would ensure that `index.php` handles all the Requests, you can also add `-t` option to set custom Document root. For Nginx users, check `nginx` file and add the contents to your server configurations.

<a id="tests"></a>
### Tests
To run Tests for the functions in this framework check `tests.php` and follow the instructions in the file header comments.

<a id="configuration"></a>
### Configuration
H.php comes with Configuration helper functions that can be used to set in-memory/runtime configurations.

```php
    config_set( $key, $value );
```
> This function can be used for setting configurations and the `$key` is case-insensitive e.g `DB_NAME` is same as `db_name`. NOTE: to use features like Database, Including View files, you should set below configurations:

* `views_dir` : the directory to load views from. default: `views/`.
* `views_extension`: View files extension e.g . default: `.php`
* `db_host` : your database host. default: `localhost`.
* `db_name` : your database name. default: `none`.
* `db_user` : your database username. default: `root`.
* `db_pass` : your database password. default: `none`.

```php
    config_get( $key, $default=NULL );
```
> This function can be used to get a configuration value and the `$key` is case-insensitive e.g `DB_NAME` is same as `db_name`. returns `$default` if `$key` have not been set.

```php
    config_has( $key );
```
> This function can be used to check if a configuration have been set the `$key` is case-insensitive e.g `DB_NAME` is same as `db_name`. returns `TRUE` or `FALSE` based on the result of the checking.

```php
    config_delete( $key );
```
> This function deletes a configuration value if it have been set only the `$key` is case-insensitive e.g `DB_NAME` is same as `db_name`.

```php
    config_reset();
```
> This function resets the configuration array.

<a id="routes"></a>
### Routes
H.php utilize simple and secured mechanism for it routing function `route()`. Below is the documentation for this function :-

```php
  route( $verb, $path, $callable_func );
```
> $verb ( string ): The HTTP method for the route, you can use `ANY` to match any HTTP method and you can separate methods by `|` to handle multiple methods e.g `GET | POST`. NOTE: it is case-insensitive that is, `GET` is same as `get` or `Get` or `gEt`.

> $path ( string  | regexp ): The URL path to match against, to match dynamic routes prefix parameter's name with `@` e.g `/user/@name` and optional route path or parameters should be wrap in parentheses  e.g `/user(/@name)` and `/profile(/edit)`, you should always check if the optional part is provided before using it else you get `INDEX` error. All the dynamic parameters will be passed as a single Associative and Indexed Array to the handler function. You can add your own custom Regular Expressions for a Dynamic parameter by separating parameter name and Regular Expressions with `:` e.g `/user/@id:[0-9]+` or adding the Regex itself e.g `/user/[0-9]+` and remember you can only access it through it Index e.g `$args[0]`.

> $callable_func ( callable ): It can be a anonymous function, variable function, array object or any other valid callable in PHP, you can read docs for PHP [call_user_func()](http://php.net/manual/en/function.call-user-func-array.php) to understand this better.

Example:

```php
  # index.php
  include 'myControllers/BooksController.php';

  # Index Route that handle any method

  route( 'ANY', '/', function(){
    return 'Hello World'; # you can also use `echo`
  } );

  # Multiple method and Dynamic parameter

  route( 'GET | POST', '/echo/@text', function( $args ){
    config_set( 'page_title', 'Echo World' );
    return config_get( 'page_title' ) . ' ' . $args['text']; # a view can be used instead!
  } );

  # Optional parameter

  route( 'GET', '/optional(/@optional_arg)', function( $args ) {
    if ( !isset( $args['optional_arg'] ) ) {
      return 'Optional: not provided!';
    }
    return 'Optional: provided';
  } );

  # Optional Parameter that includes a required parameter

  route( 'GET', '/optional(/@opt_arg_with_path)/edit', function( $args ) {
    if ( !isset( $args['optional_arg_with_path'] ) ) {
      return 'Optional: not provided!';
    }
    return 'Optional: provided';
  } );

  # Optional Named Dynamic parameter with custom regex

  route( 'GET', '/optional(/@optional_arg_regex:[0-9]+)', function( $args ) {
    if ( !isset( $args['optional_arg_regex'] ) ) {
      return 'Optional: not provided!';
    }
    return 'Optional: provided';
  } );

  # Named Dynamic parameter with custom regex

  route( 'GET', '/@my_regex:[0-9]+', function( $args ) {
    return $args['my_regex'];
  } );

  # Indexed Dynamic parameter with custom regex

  route( 'GET', '/user/[0-9]+', function( $args ) {
    return $args[0];
  } );

  # Route using Class method

  route( 'GET', '/books', array( new BooksController(), 'index' ) );

  # myControllers/BooksController.php

  class BooksController {
    public function index() {
      # do some magic here!
    }
  }
```

Another interesting H.php function is the `route_base()` that allows you to group routes with the same base path together.

```php
  route_base( $base, $routes=array() )
```
> This function can be used to group routes or endpoints together. parameters explanations:-

> `$base` The routes group endpoint e.g `/api/v1`
> `$routes` Arrays of of each sub-routes in `Key-Value` pairs, the `Key` will look like something like `METHOD -> /my_path`. Notice: it is almost same as the `route( $method, $regex_path, $callable_handler );`, just that you will separate `$method` and `$regex_path` with `->` in the same string and the `Value` will be the `$callable_handler`.

>You can do anything possible with `route()`
Below is an example / use-case when you need want to update API version without bringing down the first version.

```php
  # index.php

  # Greeter v1 routes
  route_base( '/api/v1', array(
    'GET -> /greet/@name' => function( $args ) {
      return 'Hello ' . $args['name'] . ' from the Old Greeter API';
    }
  ) );

  # Greeter v2 routes, Added support for POST and PUT and also a new greetings message
  route_base( '/api/v2', array(
    'GET|POST|PUT -> /greet/@name' => function( $args ) {
      return 'Greetings ' . $args['name'] . ', from the New Greeter API';
    }
  ) );
```

<a id="request"></a>
### Request
H.php comes with functions that you can use to interact with Client Request Headers and Variables.

```php
  req_get( $key, $def=NULL );
```
> The function will return the value for $key in $_GET array else it will return $def.

```php
  req_post( $key, $def=NULL );
```
> The function will return the value for $key in $_POST array else it will return $def.

```php
  req_put( $key, $def=NULL );
```
> The function will return the value for $key in PUT request array else it will return $def.

```php
  req_patch( $key, $def=NULL );
```
> The function will return the value for $key in PATCH request array else it will return $def.

```php
  req_raw( $key=NULL, $def=NULL );
```

> The function will return the value for $key in raw request body else return $def, if none is defined then the function returns unparsed request body.

```php
  req_file( $key, $def=NULL );
```
> The function will return the value for $key in $_FILES array else it will return $def.

```php
  req_method( $compare );
```

> The function returns the Request Method and if $compare is passed it will compare it and returns true/false. NOTE: To emulate PUT/PATCH/DELETE in HTML forms, use a form with `method="POST"` and you will add a hidden input with name `_method` and the method as the value. You can also method override with `X-HTTP-METHOD-OVERRIDE` header.

```php
  req_env( $variable );
```

> This function returns the environment variable in $_SERVER array.

```php
    req_header( $key );
```
> This function can be used to get a Client Request Headers e.g `X-Requested-With`, `Content-Type`, `X-API-Key` etc.

```php
  req_base( $str='' );

  # URL: localhost/my_app/about
  # returns `/my_app`
```

> The function returns the base URL for the project, it is useful for including CSS or Other static files. if $str is defined, it will be appended to the base e.g `req_base( '/css/styles.css' )` it will returns `/my_app/css/styles.css` if the base directory is `/my_app`. if the project is not in a sub-directory, it will return empty string.

```php
  req_site( $str='' );

  # URL: localhost/my_app/about | returns: http(s)://localhost
  # URL: www.mylivewebsite.tld/about | returns: http(s)://www.mylivewebsite.com
```
> This function returns the current domain and protocol, it is useful when you want to get full website URL without hard-coding it. if `$str` is defined, it will be appended to the returned URL, by default `$str` is empty and it is recommended to use it with `req_base()` to get accurate full URL, especially in localhost where you have not setup Virtual host.


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