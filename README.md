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



<a id="intro"></a>
### Introduction
> H.php is a Minimal and Lighweight PHP Function-based framework that is designed for you to prototype and create Web Applications and APIs quickly without going through those unnecessary setups and installations.

<a id="install"></a>
### Installation
H.php does'nt require any special installation process, you will just have to copy `.htaccess` file to your app root and `H.php` file in your project then you will require it and begin your journey of `Minimalism`! e.g if `H.php` is in the `lib` folder :-
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

> TIP: You can define the constants in `config.php` file then include it inyour `index.php`.

<a id="routes"></a>
### Routes
H.php utilize Regular Expression for it routing function `route()`. Below is the signature for this function :-

```php
  route( $method, $regex_path, $callable_handler );
```
> $method ( string ): The HTTP method for the route, you can use `ANY` to match any HTTP method and you can separate methods by `|` to match multiple methods e.g `GET | POST`

> $regex_path ( string  | regex ): The pattern to match, NOTE: you don't need to write full regex, just the string e.g `/` or to match dynmaic routes `/user/(\w+)/` and also you dont need to worry about URL trailing slash. all the captures in this regexp will be passed in order to the `$handler` callable function.

> $callable_handler ( callable ): It can be a anonymous function, variable function or any other valid callablea in PHP, you can read docs for PHP [call_user_func_array()](http://php.net/manual/en/function.call-user-func-array.php) to understand this better. Also you can use a string that separates Class and Method by `#`, your Class will be Auto-loaded from the path specified in `CONTROLLERS_DIR` constant e.g `BookController#addBook`.

Example:

```php
  # index.php

  define('CONTROLLERS_DIR', 'contollers/');

  route('ANY', '/', function(){
    echo 'Hello World';
  });

  route('GET | POST', '/echo/(\w+)', function( $text ){
    echo $text; # not secured!
  });

  route('GET', '/books', 'BooksController#index');
```

<a id="views"></a>
### Views
H.php Views is basically a Native PHP template system using the [Alternative syntax](http://php.net/manual/en/control-structures.alternative-syntax.php) of PHP. `send_view()` function is responsible for the rendering of Views. `esc( $string )` is a function that returns the HTML-escaped string of the argument passed to it, You can also use `send_json( $data )` to send JSON response to the client and you can also use `send_jsonp( $data )` to send JSONP response. below is the explanation for `send_view()` function:-

```php
  send_view( $name, $data );
```
> $name ( string ): The name of the template to include from the path defined in constant `VIEWS_DIR`. you also dont need to append `.php` extension.

> $data ( associative array ): The data that get passed to the View.

Example:
```php
  # index.php

  define('VIEWS_DIR', 'views/');

  route('GET', '/user/(\w+)', function( $user ){
    echo send_view( 'userView', array(
      'name' => $user,
    ));
  });

  # views/userView.php

  <?= esc( $name ); ?>
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
  echo send_view('postsView', array( 'posts' => $posts ) );
});

# views/postsView.php

if ( count( $posts ) ) {
  foreach ( $posts as $post ) {
  # do something with $post e.g $post['title']
  }
} else {
  echo 'No posts';
}

```


<a id="env"></a>
### Environment
H.php comes with bunch of functions that allows you to interact with Environment variables, below are the functions explanations:

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

> The function returns the base URL for the project, it is useful for including CSS or Other static files. if $str is defined, it will be appended to the base e.g `req_base( '/static/css/styles.css' )` it will returns `/my_app/static/css/styles.css` if the base directory is `/my_app`.

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
 
 if ( has_flash( 'message' ) ) {
   echo get_flash( 'message' );
 }
 
 # ...
```

<hr>

Thanks you, this framework is open-source and you are free to send pull requests.

Creator:- [Oyedele Hammed Horlah](https://devhammed.github.io)

> Released under MIT License.
