<?php

namespace H;

use PDO;

class Database {

  function run( $sql, $bind=array() ) {
    $dbuser = defined( 'DB_USER' ) ? DB_USER : null;
    $dbpass = defined( 'DB_PASS' ) ? DB_PASS : null;
    $dbname = defined( 'DB_NAME' ) ? DB_NAME : null;
    $dbhost = defined( 'DB_HOST' ) ? DB_HOST : null;
    $dsn = defined( 'DB_DSN' ) ? DB_DSN : ( 'mysql:host=' . $dbhost . ';dbname=' . $dbname );
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try {
      $dbh = new \PDO( $dsn, $dbuser, $dbpass, $options );
    } catch ( PDOException $e ) {
      die( $e->getMessage() );
    }
    try {
      $res = $dbh->prepare( $sql );
      $res->execute( $bind );
      return $res;
    } catch ( PDOException $e ) {
      die( 'Error executing SQL query!' );
    }
  }

  function select( $sql, $bind=array() ) {
    $res = array();
    $rows = $this->run( $sql, $bind );
    while ( $row = $rows->fetch( PDO::FETCH_ASSOC ) ) {
      $res[] = $row;
    }
    return $res;
  }

  function insert( $table, $bind=array() ) {
    ksort( $bind );
    $field_names = implode( '`, `', array_keys( $bind ) );
    $field_values = ':' . implode( ', :', array_keys( $bind ) );
    $sql = 'INSERT INTO ' . $table . '(`' . $field_names . '`) VALUES (' . $field_values . ')';
    return $this->run( $sql, $bind );
  }

  function update( $table, $bind, $where=null ) {
    ksort( $bind );
    $fields = null;
    foreach ( $bind as $k => $v ) {
      $fields .= '`' . $k . '`=:' . $k . ',';
    }
    $sql = 'UPDATE ' . $table . ' SET ' . trim( $fields ) . (! is_null( $where ) ? (' WHERE ' . $where) : '');
    return $this->run( $sql, $bind );
  }

  function delete( $table, $where=null, $limit=null ) {
    $sql = 'DELETE FROM ' . $table;
    $sql .= ! is_null( $where ) ? (' WHERE ' . $where) : '';
    $sql .= ! is_null( $limit ) ? (' LIMIT ' . $limit) : '';
    return $this->run( $sql );
  }

}