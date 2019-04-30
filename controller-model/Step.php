<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";


class Step extends BaseConnection {

  // object properties
  public $id;
  public $fkey_recipe_id;
  public $description;

  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(RECIPESTEP_TABLE);
  }

  function store() {
    $rv = NULL;
    // // sanitize
    $this->fkey_recipe_id=htmlspecialchars(strip_tags($this->fkey_recipe_id));
    $this->description=htmlspecialchars(strip_tags($this->description));
    // query to insert record
    $query = "INSERT INTO ".$this->table_name."
              SET description='{$this->description}',
                  fkey_recipe_id={$this->fkey_recipe_id};";
    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       throw new Exception(WHEN_ADDING_TO_DATABASE);
    }
  }

  public function get_for_recipe($id) {
    $query = "SELECT id, description
              FROM ".$this->table_name."
              WHERE fkey_recipe_id={$id};";
    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       throw new Exception(QUERY_ERROR);
    }
    $recipe_steps_array = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
      array_push($recipe_steps_array, $row);
    }
    return $recipe_steps_array;
  }
}
