<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

class Unit extends BaseConnection {

  // object properties
  public $id;
  public $name;
  public $short_name;

  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(UNITS_TABLE);
  }

  // read themes
  function index(){
      // select all query
      $query = "SELECT * FROM ".$this->table_name.";";
      // prepare query statement
      $st = $this->conn->prepare($query);
      // execute query
      $st->execute();
      return $st;
  }

  function show() {
    // query to read single record
    $query = "SELECT u.id, u.name, u.short_name FROM ".$this->table_name." u
              WHERE u.short_name = '{$this->short_name}';";

    // prepare query statement
    $st = $this->conn->prepare( $query );

    // execute query
    $st->execute();

    // get retrieved row
    $row = $st->fetch(PDO::FETCH_ASSOC);

    // set values to object properties
    $this->id = $row["id"];
    $this->name = $row["name"];
    $this->short_name = $row["short_name"];
  }
}
