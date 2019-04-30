<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";

class Recipe extends BaseConnection {

  // object properties
  public $id;
  public $name;
  public $description;
  public $fkey_creator_id;
  public $fkey_creator_username;
  public $created_at;
  public $viewable;
  public $picture_string;
  public $favorite;
  public $fkey_user_id;

  /*
    Constructs the object and connects it to the database
  */
  public function __construct() {
    parent::__construct(RECIPE_TABLE);
  }

  // read themes
  function index(){
    $rv = NULL;
    // // sanitize
    // query to insert record
    $query = "SELECT r.id as id, r.name as name, r.description as description,
                     r.created_at as created_at
              FROM Recipe r
              WHERE r.viewable=1;";

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

  // stores a recipe to the database $this->name, description, fkey_creator_id
  // created_at must be init
  public function store() {
    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->fkey_creator_id=htmlspecialchars(strip_tags(
                                              $this->fkey_creator_id
                                            ));
    $this->created_at=htmlspecialchars(strip_tags($this->created_at));

    try {
      // locks the table to avoid race cases
      $this->lockTable();
      // query to insert record
      $query = "INSERT INTO " . $this->table_name . "
                SET name='{$this->name}',
                    description='{$this->description}',
                    fkey_creator_id={$this->fkey_creator_id},
                    created_at='{$this->created_at}'";
      // prepare query
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
        $this->unlockTable(); // unlocks the table
        throw new Exception(WHEN_ADDING_TO_DATABASE);
      } else {
        $query = "SELECT MAX(id) as id FROM ".$this->table_name;
        // prepare query
        $st = $this->conn->prepare($query);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        $this->unlockTable(); // unlocks the table
        $this->addPicture();
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  // stores a recipe to the database $this->name, description, fkey_creator_id
  // created_at must be init
  public function update() {
    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->fkey_creator_id=htmlspecialchars(strip_tags(
                                              $this->fkey_creator_id
                                            ));
    $this->created_at=htmlspecialchars(strip_tags($this->created_at));

    try {
      // query to insert record
      $query = "UPDATE " . $this->table_name . "
                SET name='{$this->name}', description='{$this->description}'
                WHERE id={$this->id};";

      // prepare query
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
        throw new Exception(WHEN_EDITING_TO_DATABASE);
      } else {
        $this->addPicture();
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }





  // added the image to the database,
  // $this->string path, $this->id must be init
  private function addPicture() {
    $my_file = $this->getPicturePath();
    $handle = fopen($my_file, 'w');
    fwrite($handle, $this->picture_string);
    $query = "UPDATE " . $this->table_name . "
              SET picture_path='{$my_file}'
              WHERE id={$this->id}";
    $st = $this->conn->prepare($query);
    $st->execute();
  }

  // gets the picture path of the recipe
  // @param $id int, recipe id
  // @return the path of the picture, if no param is given returns the current
  //        if param given returns the path to that id
  private function getPicturePath($id=NULL) {
    if($id == NULL) {
      return PATH."/".$this->id;
    } else {
      return PATH."/".$id;
    };
  }

  // gets the hex content of the picture of a recipe
  // @param $id int, recipe id
  // @return the content of the picture, if no param is given returns
  //         the current if param given returns the path to that id
  public function getPicture($id=NULL) {
    $my_file = $this->getPicturePath($id);
    $handle = fopen($my_file, 'r');
    $this->picture_string = fread($handle,filesize($my_file));

  }

  // fetches the recipe for the user, $this->id must be init, sets the
  // memeber data objects of the class to the recipe requested
  public function show() {
    if( !empty($this->id) ) {
      $query = "SELECT r.id AS id, r.name AS name,
                       r.description AS description,
                       r.created_at AS created_at,
                       r.fkey_creator_id as fkey_creator_id,
                       r.viewable as viewable,
                       u.username as username
                FROM Recipe r
                LEFT JOIN User u ON u.id=r.fkey_creator_id
                WHERE r.id={$this->id};";
      //echo $query;die();
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
        $this->unlockTable(); // unlocks the table
        throw new Exception(QUERY_ERROR);
      } else {
        if($st->rowCount() > 0) {
          $row = $st->fetch(PDO::FETCH_ASSOC);
          $this->id = $row["id"];
          $this->name = $row["name"];
          $this->fkey_creator_id = $row["fkey_creator_id"];
          $this->fkey_creator_username = $row["username"];
          $this->description = $row["description"];
          $this->created_at = $row["created_at"];
          $this->viewable = $row["viewable"];
          $this->getPicture();
        } else {
          throw new Exception(NOT_FOUND);
        }
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // fetches the recipe for the user, $this->id must be init, sets the
  // memeber data objects of the class to the recipe requested
  public function showWorld() {
    if( !empty($this->id) ) {
      $query = "SELECT r.id AS id, r.name AS name,
                       r.description AS description,
                       r.created_at AS created_at,
                       r.fkey_creator_id as fkey_creator_id,
                       r.viewable as viewable
                FROM Recipe r
                WHERE r.id={$this->id};";

      //echo $query;die();
      $st = $this->conn->prepare($query);
      // execute query
      if(!$st->execute()) {
        $this->unlockTable(); // unlocks the table
        throw new Exception(QUERY_ERROR);
      } else {
        if($st->rowCount() > 0) {
          $row = $st->fetch(PDO::FETCH_ASSOC);
          $this->id = $row["id"];
          $this->name = $row["name"];
          $this->description = $row["description"];
          $this->created_at = $row["created_at"];
          $this->getPicture();
        } else {
          throw new Exception(NOT_FOUND);
        }
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // deletes the ingredients tied to this recipe, $this->id must be init
  public function clearIngredients() {
    $query = "DELETE FROM RecipeIngredients
              WHERE fkey_recipe_id = {$this->id};";
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      $this->unlockTable(); // unlocks the table
      throw new Exception(QUERY_ERROR);
    }
  }

  // deletes the steps tied to this recipe, $this->id must be init
  public function clearSteps() {
    $query = "DELETE FROM RecipeStep
              WHERE fkey_recipe_id = {$this->id};";
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      $this->unlockTable(); // unlocks the table
      throw new Exception(QUERY_ERROR);
    }
  }

  // sets the published value based on what the client sumbits
  public function publish() {
    $query = "UPDATE " . $this->table_name . "
              SET viewable={$this->viewable}
              WHERE id={$this->id};";
    // prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      throw new Exception(WHEN_EDITING_TO_DATABASE);
    }
  }

  // gets the number of maps that are tied to a recipe
  public function numberOfMaps() {
    $query = "SELECT COUNT(*) as num_recipes
            FROM RecipeUserMap
            WHERE fkey_recipe_id={$this->id};";

    $st = $this->conn->prepare($query);
            // execute query
    if(!$st->execute()) {
      throw new Exception(QUERY_ERROR);
    } else {
      $row = $st->fetch(PDO::FETCH_ASSOC);
      return $row["num_recipes"];
    }
  }

  public function carbonCopy() {
    $old_id = $this->id;
    $this->store();

    $query = "UPDATE RecipeUserMap
              SET fkey_recipe_id={$this->id}
              WHERE
              fkey_recipe_id={$old_id}
              AND
              fkey_user_id={$this->fkey_creator_id};";
    // prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      throw new Exception(WHEN_EDITING_TO_DATABASE);
    }

    $query = "INSERT INTO RecipeOrgin
              SET fkey_recipe_id={$this->id},
                  fkey_parent_id={$old_id};";
    // /echo $query;
    // prepare query
    $st = $this->conn->prepare($query);
    // execute query
    if(!$st->execute()) {
      throw new Exception(WHEN_EDITING_TO_DATABASE);
    }


  }



}
