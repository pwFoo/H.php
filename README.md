# H.php

> The Minimalist PHP Framework!

## Table of Contents
* [Introduction](#intro)
* [Installation](#install)
* [Configuration](#config)
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
> H.php is a Minimal and Lighweight PHP Function-based MVC-ish framework that is designed for you to prototype and build Web Applications and APIs without stress.

<a id="install"></a>
### Installation
Download zip archive [here](https://github.com/devHammed/H.php/archive/v1.2.0.zip) then extract and you will just have to copy `.htaccess` file to your app root and `H.php` file in your project then you will require it! e.g if `H.php` is in the `lib` folder :-
```php
  require 'lib/H.php';
```
Note: If your Web Server doesn't support rewrite, your app will still work e.g `index.php/about`.

<a id="config"></a>
### Configuration
H.php comes with Configuration helper functions that can be used to set in-memory configurations.

```php
    config_set( $key, $value );
```
> This function can be used for setting configurations and the `$key` is case-insensitive e.g `DB_NAME` is same as `db_name`. NOTE: to use features like Database, Autoloading classes and View, you should set below configurations:
* `controllers_dir`: the directory to load controller classes from. default: controllers/.
* `views_dir` : the directory to load views from. default: views/.
* `db_host` : your database host. default: localhost.
* `db_name` : your database name. default: none.
* `db_user` : your database username. default: root.
* `db_pass` : your database password. default: none.

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
H.php utilize Regular Expression for it routing function `route()`. Below is the signature for this function :-

```php
  route( $method, $regex_path, $callable_handler );
```
> $method ( string ): The HTTP method for the route, you can use `ANY` to match any HTTP method and you can separate methods by `|` to match multiple methods e.g `GET | POST`. NOTE: it is case-insensitve for your happiness so, `GET` is same as `get` or `Get` or `gEt`. :smile:

> $regex_path ( string  | regex ): The pattern to match, NOTE: you don't need to write full regex, just the string e.g `/` or to match dynmaic routes `/user/(\w+)/` and optional parameters can be suffixed with `?` e.g `/user/(\w+)?/`. All the captures in this regexp will be passed in order to the `$handler` callable function and remember to use default argument for optional parameters e.g `function( $user=NULL ){ ... }`.

> $callable_handler ( callable ): It can be a anonymous function, variable function or any other valid callablea in PHP, you can read docs for PHP [call_user_func_array()](http://php.net/manual/en/function.call-user-func-array.php) to understand this better. Also you can use a string that separates Class and Method by `#`, your Class will be Auto-loaded from the path specified in `CONTROLLERS_DIR` configuration e.g `BookController#addBook`.

Example:

```php
  # index.php

  config_set( 'CONTROLLERS_DIR', 'myContollers/' );

  route( 'ANY', '/', function(){
    return 'Hello World'; # you can also use `echo`
  } );

  route( 'GET | POST', '/echo/(\w+)', function( $text ){
    config_set( 'page_title', 'Echo World' );
    return config_get( 'page_title' ) . ' ' . $text; # a view can be used instead!
  } );

  route( 'GET', '/books', 'BooksController#index' );

  # myControllers/BooksController.php

  class BooksController {
    public function index() {
      # do some magic here!
    }
  }
```

<a id="request"></a>
### Request
H.php comes with functions that you can use to interact with Client Request Headers and Variabless.

```php
  req_get( $key, $def='' );
```
> The function will return the value for $key in $_GET array else it will return $def.

```php
  req_post( $key, $def='' );
```
> The function will return the value for $key in $_POST array else it will return $def.

```php
  req_put( $key, $def='' );
```
> The function will return the value for $key in PUT request array else it will return $def.

```php
  req_patch( $key, $def='' );
```
> The function will return the value for $key in PATCH request array else it will return $def.

```php
  req_raw( $key='', $def='' );
```

> The function will return the value for $key in raw request body else return $def, if none is defined then the function returns unparsed request body.

```php
  req_cookie( $key, $def='' );
```
> The function will return the value for $key in $_COOKIE array else it will return $def.

```php
  req_session( $key, $def='' );
```
> The function will return the value for $key in $_SESSION array else it will return $def.

```php
  req_file( $key, $def='' );
```
> The function will return the value for $key in $_FILES array else it will return $def.

```php
  req_method( $compare );
```

> The function returns the Request Method and if $compare is passed it will compare it and returns true/false. NOTE: To emulate PUT/PATCH/DELETE in HTML forms, use a form with `method="POST"` and you will add a hidden input with name `_method` and the method as the value.

```php
  req_env( $variable );
```

> This function returns the environment variable in $_SERVER array.

```php
    req_header( $key );
```
> This function can be used to get a Client Request Header e.g `X-Requested-With`, `Content-Type` etc.

```php
  req_base( $str='' );

  # URL: localhost/my_app/about
  # returns `/my_app`
```

> The function returns the base URL for the project, it is useful for including CSS or Other static files. if $str is defined, it will be appended to the base e.g `req_base( '/css/styles.css' )` it will returns `/my_app/css/styles.css` if the base directory is `/my_app`.

```php
  req_site( $str='/' );

  # URL: localhost/my_app/about | returns: http(s)://localhost/
  # URL: www.mylivewebsite.tld/about | returns: http(s)://www.mylivewebsite.com/
```
> This function returns the current domain and protocol, it is useful when you want to get full website URL without hard-coding it. if `$str` is defined, it will be appended to the returned URL, by default `$str` is `/` and it is recommended to use it with `req_base()` to get accurate full URL, especially in localhost.


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
  # $name ( string ): The name of the template to include from the path defined in consfiguration `VIEWS_DIR`. you also dont need to append `.php` extension.

  # $data ( associative array ): The data that get passed to the View.

  res_render( $name, $data );
```
> This function can be used to render native PHP templates to the browser.

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
- Always add `<?php if ( !defined( 'H_PHP' ) ) die( 'Permission denied!' ) ?>` at the top of your view file to prevent direct access to it.
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

  route( 'GET', '/user/(\w+)', function( $user ){
    return res_render( 'userView', array(
      'name' => $user,
    ) );
  } );

  # myViews/userView.php
  <?php if ( !config_has( 'h_php_init' ) ) die(); ?>

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
  <?php if ( !config_has( 'h_php_init' ) ) die(); ?>

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

>`$algo` is the algoritm to use when generating hash, it can be `PASSWORD_DEFAULT` or `PASSWORD_BCRYPT` or `PASSWORD_ARGON2I`. it defaults to `PASSWORD_DEFAULT` which will use the latest secured algorithm in PHP.

> `$opts` are options to use when generating, Read more at [PHP password_hash](http://php.net/manual/en/function.password-hash.php).

>This function can be used to hash a password before saving to the database.

```php
  hash_check( $str, $hash );
```
> This function can be used to verify a `$hash` against `$str`, returns `TRUE` if it is valid else returns `FALSE`. This is useful for verifying password entered by user againt the hashed version in the database. Read more at [PHP password_verify](http://php.net/manual/en/function.password-verify.php).

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

Thanks, did you want to contribute? fork this repo and send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.