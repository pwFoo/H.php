<?php

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