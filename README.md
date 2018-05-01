# H.php

> The Minimalist PHP Framework!

## Table of Contents
* [Introduction](#intro)
* [Installation](#install)
* [Configuration](#config)
* [Routes](#routes)
* [Views](#views)
* [Database](#database)
* [Environment](#env)
* [Cookies](#cookie)
* [Session](#session)
* [Flash Messages](#flash)
* [Resources](#resources)



<a id="intro"></a>
### Introduction
> H.php is a Minimal and Lighweight PHP Function-based framework that is designed for you to quickly prototype and build Web Applications and APIs.

<a id="install"></a>
### Installation
H.php does'nt require long installation process, you will just have to copy `.htaccess` file to your app root and `H.php` file in your project then you will require it and begin your journey of `Minimalism`! e.g if `H.php` is in the `lib` folder :-
```php
  require 'lib/H.php';
```
Note: If your Web Server doesn't support rewrite, your app will still work e.g `index.php/about`.

<a id="config"></a>
### Configuration
H.php will need some constants you are using some feature like Views, Controllers and Database, below are the list of constants that you are required to `define`:

* CONTROLLERS_DIR : The path to the directory containing Controllers class files. NOTE: with a trailing slash e.g `controllers/`

* VIEWS_DIR : The path to the directory containing templates files. NOTE: with a trailing slash e.g `views/`

* DB_HOST : Database Host

* DB_NAME : Databse Name

* DB_USER : Databse Username

* DB_PASS : Databse Password

> TIP: You can define the constants e.g Site title etc. in `config.php` file then include it iny our `index.php`.

<a id="routes"></a>
### Routes
H.php utilize Regular Expression for it routing function `route()`. Below is the signature for this function :-

```php
  route( $method, $regex_path, $callable_handler );
```
> $method ( string ): The HTTP method for the route, you can use `ANY` to match any HTTP method and you can separate methods by `|` to match multiple methods e.g `GET | POST`. NOTE: it is case-insensitve for happiness so, `GET` is same as `get` or `Get` or `gEt`. :-)

> $regex_path ( string  | regex ): The pattern to match, NOTE: you don't need to write full regex, just the string e.g `/` or to match dynmaic routes `/user/(\w+)/` and optional parameters can be suffixed with `?` e.g `/user/(\w+)?/`. All the captures in this regexp will be passed in order to the `$handler` callable function and remember to use default argument for optional parameters e.g `function( $user=NULL ){ ... }`.

> $callable_handler ( callable ): It can be a anonymous function, variable function or any other valid callablea in PHP, you can read docs for PHP [call_user_func_array()](http://php.net/manual/en/function.call-user-func-array.php) to understand this better. Also you can use a string that separates Class and Method by `#`, your Class will be Auto-loaded from the path specified in `CONTROLLERS_DIR` constant e.g `BookController#addBook`.

Example:

```php
  # index.php

  define('CONTROLLERS_DIR', 'contollers/');

  route('ANY', '/', function(){
    return 'Hello World'; # you can also use `echo`
  });

  route('GET | POST', '/echo/(\w+)', function( $text ){
    echo $text; # a view should be used instead!
  });

  route('GET', '/books', 'BooksController#index');

  # controllers/BooksController.php

  class BooksController {
    public function index() {
      # do some magic here!
    }
  }
```

<a id="views"></a>
### Views
H.php comes with a simple View system that is easy to use and it also make use of PHP alternative structure so you don't need to learn a new template syntax. Below is a rundown of the functions available :-

```php
  # $name ( string ): The name of the template to include from the path defined in constant `VIEWS_DIR`. you also dont need to append `.php` extension.

  # $data ( associative array ): The data that get passed to the View.

  send_view( $name, $data );
```
> This function can be used to render PHP-HTML template to the browser.

```php
  esc( $var );
```
> This function can be used to HTML-escape a variable to output.

```php
  send_json( $data );
```
> This function can be used to send JSON data.

```php
  send_jsonp( $data );
```
> This function can be used to send JSONP script, `NOTE:` the request URL should include `callback` query param with the value of JavaScript function to call e.g `http://www.mysite.tld/api/jsonp/?callback=myFunc`.

Below are the guidelines on how to use native PHP for template:-
- Always use HTML with inline PHP. Never use blocks of PHP.
- Always use `esc( $var )` function to escape potentially dangerous variables.
- Always use the short echo syntax (`<?=`) when outputting variables. For other inline PHP code, use the full `<?php` tag.
- Always use the [PHP alternative syntax for control structures](http://php.net/manual/en/control-structures.alternative-syntax.php), which are designed to make templates more legible.
- Never use PHP curly brackets.
- Only put one statement in each PHP tag.
- Avoid using semicolons. They are not needed when there is only one statement per PHP tag.
- Never use the `for`, `while` or `switch` control structures. Instead use `if` and `foreach`.
- Avoid variable assignment.

`Culled from: ` PHP Plates template engine syntax guidelines.

Example:
```php
  # index.php

  define('VIEWS_DIR', 'views/');

  route('GET', '/user/(\w+)', function( $user ){
    return send_view( 'userView', array(
      'name' => $user,
    ));
  });

  # views/userView.php

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

# define constants or require config.php here

route('GET', '/posts', function(){
  $posts = db_select('SELECT * FROM posts');
  return send_view('postsView', array( 'posts' => $posts ) );
});

# views/postsView.php

  <?php if ( count( $posts ) ): ?>
    <?php foreach ( $posts as $post ): ?>
      <h1><?= $post['title'] ?></h1>
      <p><?= $post['body'] ?></p>
    <?php endforeach ?>
  <?php else: ?>
    <p>No posts in this blog!</p>
  <?php endif ?>

```


<a id="env"></a>
### Environment
H.php comes with bunch of functions that allows you to interact with Environment variables, Request variables and Response, below are the functions explanations:

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
  req_base( $str='' );

  # URL: localhost/my_app/about
  # returns `/my_app`
```

> The function returns the base URL for the project, it is useful for including CSS or Other static files. if $str is defined, it will be appended to the base e.g `req_base( '/css/styles.css' )` it will returns `/my_app/css/styles.css` if the base directory is `/my_app`.

```php
  add_header( $header, $code=null );
```

> The function can be used to add header, $header is the header string e.g `Content-Type: text/html` and $code is the status code for the response.

```php
  redirect_to( $url );
```

> The function can be used to redirect to $url, it can be used with `req_base()` to redirect using relative url.

```php
  req_back();
```

> This function can be used to redirect back to the previous page e.g after form submission.

```php
  req_status( $code=null );
```
> Set HTTP status code if $code is passed else return the current status code.

```php
  req_type( $content_type );
```
> Set the response Content type e.g `application/json`.

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

Flash Messages are temporary Session values that gets deleted after the next called of `get_flash( $key )`. Let's first explain the functions then an Example will follow:-

```php
  set_flash( $key, $value );
```

> This function can be used to set a Flash message.

```php
  get_flash( $key );
```

> This function returns the Flash message value if it exists then delete it.

```php
   has_flash( $key );
```

> This function can be used to check if a Flash message has been set.

```php
  keep_flash( $key );
```

> This function is the opposite of `get_flash`, it returns a Flash message value if it exists but it does not delete it so it can be used in the next View.

Example:-

```php
 # index.php

 # config and settings here

 route( 'POST', '/comment', function() {
   # insert something into database
   set_flash( 'message', 'Blah blah blah' ); # set a message
   req_back(); # go back
 }

 # demoView.php

 <?php if ( has_flash( 'message' ) ): ?>
   <p id="message--text"><?= get_flash( 'message' ) ?></p>
 <?php endif ?>

 # ...
```

<a id="resources"></a>
### Resources

Below are links to some resources and examples to get you started.

* [H.php Boilerplate Project](https://github.com/devHammed/H.php-Boilerplate)
* [Simple Chat Project](https://github.com/devHammed/H.php-Chat)

<hr>

Thanks you, this framework is open-source and you are free to send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.