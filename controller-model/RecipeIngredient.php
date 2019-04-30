<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

class RecipeIngredient extends BaseConnection {

  // object properties
  public $fkey_recipe_id;
  public $fkey_ingredient_id;
  public $fkey_unit_id;
  public $amount;
  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(RECIPEINGREDIENTS_TABLE);
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

  function store() {
    $rv = NULL;
    // // sanitize
    $this->fkey_recipe_id=htmlspecialchars(strip_tags($this->fkey_recipe_id));
    $this->fkey_ingredient_id=htmlspecialchars(strip_tags($this->fkey_ingredient_id));;
    $this->fkey_unit_id=htmlspecialchars(strip_tags($this->fkey_unit_id));
    $this->amount=htmlspecialchars(strip_tags($this->amount));

    // query to insert record
    $query = "INSERT INTO ".$this->table_name." SET fkey_recipe_id={$this->fkey_recipe_id},fkey_ingredient_id={$this->fkey_ingredient_id},fkey_unit_id={$this->fkey_unit_id},amount={$this->amount} ;";
    //prepare query

    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       $rv = WHEN_ADDING_TO_DATABASE;
    }
  }

  public function get_for_recipe($id) {
    $query = "select i.name as ingredient_name, u.short_name as unit_name, amount from RecipeIngredients ri JOIN Units u ON ri.fkey_unit_id=u.id JOIN Ingredients i ON ri.fkey_ingredient_id=i.id WHERE fkey_recipe_id={$id};";
    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    $st->execute();
    $recipe_ingredints_array = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
      array_push($recipe_ingredints_array, $row);
    }
    //echo "here".$id;
    //print_r($recipe_ingredints_array);
    return $recipe_ingredints_array;
  }
}
