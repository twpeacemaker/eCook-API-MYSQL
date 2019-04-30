<?php
include_once '../config/database.php';
/*
  CI: Holds
*/
class BaseConnection {
  // database connection and table name
  public $conn;
  // database connection and table name
  public $table_name;
  /*
    Constructs the object and connects it to the database
  */
  public function __construct($table_name) {
      $database = new Database();
      $db = $database->getConnection();
      $this->conn = $db;
      $this->table_name = $table_name;
  }

  // starts a mysql transaction
  public function start_transaction() {
    $query = "START TRANSACTION";
    // prepare query
    $st = $this->conn->prepare($query);
    $st->execute();
  }

  // commits a mysql transaction
  public function commit() {
    $query = "COMMIT;";
    // prepare query
    $st = $this->conn->prepare($query);
    $st->execute();
  }

  // commits and rolls back the mysql transaction
  public function discard_transaction() {
    $query = "COMMIT;
              ROLLBACK;";
    // prepare query
    $st = $this->conn->prepare($query);
    $st->execute();
  }

  // locks the mysql database to avoid race conditions
  public function lockTable() {
    $query = "LOCK TABLE Recipe WRITE";
    // prepare query
    $st = $this->conn->prepare($query);
    $st->execute();
  }

  // unlocks the mysql database
  public function unlockTable() {
    $query = "UNLOCK TABLES";
    // prepare query
    $st = $this->conn->prepare($query);
    $st->execute();
  }
}
