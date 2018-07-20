<?php

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