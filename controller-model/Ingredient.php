<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

class Ingredient extends BaseConnection {

  // object properties
  public $id;
  public $name;
  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(INGREDIENTS_TABLE);
  }

  // read themes
  function index(){
      // select all query
      $query = "SELECT * FROM ".$this->table_name.";";
      // prepare query statement
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
         throw new Exception(QUERY_ERROR);
      }
      return $st;
  }

  function show_and_store() {
    $name = $this->name; //keep cuz it gets over wrote in store
    $this->show();
    if(empty($this->id)) {
      $this->name = $name;
      $rv = $this->store();
      $this->show();
    }
  }

  function show() {
    $query = "SELECT i.id, i.name  FROM ".$this->table_name." i
              WHERE i.name = '{$this->name}';";

    // prepare query statement
    $st = $this->conn->prepare( $query );
    //execute query
    if(!$st->execute()) {
       throw new Exception(QUERY_ERROR);
    }
    // get retrieved row
    $row = $st->fetch(PDO::FETCH_ASSOC);

    //print_r($row);
    $this->id = $row["id"];
    $this->name = $row["name"];
  }

  function store() {
    $rv = NULL;
    // // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    // query to insert record
    $query = "INSERT INTO ".$this->table_name." SET name='{$this->name}';";
    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       throw new Exception(WHEN_ADDING_TO_DATABASE);
    }
  }
}
