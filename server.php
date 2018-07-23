<?php

$file_path = str_replace( '\\', '/', dirname( 'server.php' ) ) . strtok( $_SERVER[ 'REQUEST_URI' ],'?' );

# If file is static or exist, let the Server handle it

if ( file_exists( $file_path ) ) {
  return false;
}

# else let H.php handle it

require 'index.php';
