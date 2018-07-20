<?php

require 'app/bootstrap.php';

$app = new H\App();

$app->map( 'ANY', '/', function( $self ) {
  return 'Hello World!';
} );

$app->run();