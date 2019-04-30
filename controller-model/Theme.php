<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

class Theme extends BaseConnection {


  // object properties
  public $id;
  public $theme_name;

  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(THEME_TABLE);
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
}
