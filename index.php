<?php

# require bootstrap.php file
require 'app/bootstrap.php';

# create new application
$app = new \H\App();

# define routes
$app->get( '/', function( $h ) {
  echo $h->url;
  return 'Hello World!';
} );

# run the application
$app->run();