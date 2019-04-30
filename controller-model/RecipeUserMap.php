<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

include_once "Recipe.php";
include_once "User.php";

class RecipeUserMap extends BaseConnection {
  // object properties
  public $fkey_user_id;
  public $fkey_recipe_id;
  public $favorite;

  /*
    Constructs the object and connects it to the database
  */
  public function __construct(){
      parent::__construct(RECIPEUSERMAP_TABLE);
  }

  public function index() {
    $rv = NULL;
    // // sanitize
    $this->fkey_user_id=htmlspecialchars(strip_tags($this->fkey_user_id));
    // query to insert record
    $query = "SELECT r.id as id, r.name as name, m.favorite as favorite,
                    r.description as description, r.created_at as created_at
              FROM RecipeUserMap m
              LEFT JOIN User u ON m.fkey_user_id=u.id
              LEFT JOIN Recipe r ON m.fkey_recipe_id=r.id
              WHERE m.fkey_user_id={$this->fkey_user_id}";

    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       throw new Exception(QUERY_ERROR);
    }
    $recipes = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)){
      $recipe = new Recipe();
      $recipe->getPicture($row["id"]);
      $row["image"] = $recipe->picture_string;
      $recipes[] = $row;
    }
    return $recipes;
  }

  public function store() {
    $rv = NULL;
    // sanitize
    $this->fkey_user_id=htmlspecialchars(strip_tags($this->fkey_user_id));
    $this->fkey_recipe_id=htmlspecialchars(strip_tags($this->fkey_recipe_id));
    // query to insert record
    // assert, will  not let duplicate, PK is fkey_user_id + fkey_recipe_id
    $query = "INSERT INTO ".$this->table_name."
              SET fkey_user_id='{$this->fkey_user_id}',
                  fkey_recipe_id={$this->fkey_recipe_id};";
    //prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
       throw new Exception(WHEN_ADDING_TO_DATABASE);
    }
  }

  // fetches the recipe for the user, $this->id must be init, sets the
  // memeber data objects of the class to the recipe requested
  public function show() {
    if( !empty($this->id) && !empty($this->fkey_user_id)) {
      $query = "SELECT r.id AS id, r.name AS name,
                       m.favorite AS favorite,
                       r.description AS description,
                       r.created_at AS created_at,
                       r.fkey_creator_id as fkey_creator_id,
                       u.username as fkey_creator_username,
                       r.viewable as viewable
                FROM RecipeUserMap m
                LEFT JOIN Recipe r ON m.fkey_recipe_id=r.id
                LEFT JOIN User u ON r.fkey_creator_id=u.id
                WHERE r.id={$this->id} AND m.fkey_user_id={$this->fkey_user_id};";

      //echo $query;
      // die();
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
        $this->unlockTable(); // unlocks the table
        throw new Exception(QUERY_ERROR);
      } else {
        if($st->rowCount() > 0) {


          $row = $st->fetch(PDO::FETCH_ASSOC);
          $recipe = new Recipe();
          $recipe->id = $row["id"];
          $recipe->name = $row["name"];
          $recipe->fkey_creator_id = $row["fkey_creator_id"];
          $recipe->description = $row["description"];
          $recipe->created_at = $row["created_at"];
          $recipe->fkey_creator_username = $row["fkey_creator_username"];
          $recipe->viewable = $row["viewable"];
          $recipe->favorite = $row["favorite"];
          $recipe->getPicture();
          return $recipe;
        } else {
          throw new Exception(NOT_FOUND);
        }
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // public function show() {
  //   // query to read single record
  //   $query = "SELECT m.fkey_user_id, m.fkey_recipe_id,  m.favorite
  //             FROM ".$this->table_name." m
  //             WHERE
  //             m.fkey_user_id = '{$this->fkey_user_id}'
  //             AND
  //             m.fkey_recipe_id = '{$this->fkey_recipe_id}';";
  //
  //   // prepare query statement
  //   $st = $this->conn->prepare( $query );
  //   // execute query
  //   $st->execute();
  //   // get retrieved row
  //   $row = $st->fetch(PDO::FETCH_ASSOC);
  //   // set values to object properties
  //   $this->fkey_user_id = $row["fkey_user_id"];
  //   $this->fkey_recipe_id = $row["fkey_recipe_id"];
  //   $this->favorite = $row["favorite"];
  // }


  public function delete() {
    $query = "DELETE FROM ".$this->table_name."
              WHERE fkey_user_id = {$this->fkey_user_id} AND
                    fkey_recipe_id = {$this->fkey_recipe_id};";
    //echo $query;
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      $this->unlockTable(); // unlocks the table
      throw new Exception(QUERY_ERROR);
    }
  }

  // sets the published value based on what the client sumbits
  public function favorite() {
    $query = "UPDATE " . $this->table_name . "
              SET favorite={$this->favorite}
              WHERE fkey_user_id = {$this->fkey_user_id} AND
                    fkey_recipe_id = {$this->fkey_recipe_id};";

    //echo $query;
    // prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      throw new Exception(WHEN_EDITING_TO_DATABASE);
    }
  }
}
