<?php

namespace H;

use PDO;

class Database {

  private $dsn = DB_DSN;
  private $dbpass;
  private $dbuser;
  private $dbh;

  function __construct() {
    $this->dbpass = defined( 'DB_USER' ) ? DB_USER : NULL;
    $this->dbpass = defined( 'DB_PASS' ) ? DB_PASS : NULL;
    $options = array(
      PDO::ATTR_PERSISTENT => 1,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try {
      $this->dbh = new \PDO( $this->$dsn, $this->dbuser, $this->dbpass, $options );
    } catch ( PDOException $e ) {
      die( $e->getMessage() );
    }
  }

  function run( $sql ) {
    return $this->dbh->prepare( $sql );
  }

}