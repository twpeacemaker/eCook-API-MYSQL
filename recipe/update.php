<?php
// required headers
include_once '../includes/header_info.php';

// instantiate user object
include_once '../controller-model/User.php';
include_once '../controller-model/RecipeUserMap.php';
include_once '../controller-model/Recipe.php';
include_once '../controller-model/RecipeIngredient.php';
include_once '../controller-model/Unit.php';
include_once '../controller-model/Ingredient.php';
include_once '../controller-model/Step.php';
include_once '../errors.php';

// make sure data is not empty
if( !empty($_POST['recipe_id']) && !empty($_POST['title']) &&
    !empty($_POST['description']) && !empty($_POST['image'])
    && !empty($_POST['username']) ) {
    // NOTE: stoped here
    $user = new User();
    $user = $user->get_user($_POST['username']);

    $recipe = new Recipe();
    $recipe->id = $_POST['recipe_id'];
    $recipe->show();

    if(isset($recipe->id)) {
      try {
        //check for addtional maps
        $recipe->name = $_POST['title'];
        $recipe->description = $_POST['description'];
        $recipe->picture_string = $_POST['image'];
        if($recipe->numberOfMaps() > 1 || $recipe->fkey_creator_id != $user["id"]) {
            $recipe->fkey_creator_id = $user["id"];
            $recipe->carbonCopy();
        }
        $recipe->update();

        $recipe->clearIngredients();
        //removes the tie to add the updated information
        if(!empty($_POST['NumIngredients'])) {
          //NOTE: if there in ingredients to add
          $n = (int)$_POST['NumIngredients'];
          for ($i = 0; $i < $n; $i++) {
            //handle ingredient
            $ingredient = new Ingredient();
            $ingredient->name = $_POST["iname".$i];
            $ingredient->show_and_store();
            //handle unit
            $unit = new Unit();
            $unit->short_name = $_POST["iunit".$i];
            $unit->show();
            //creats the recipeingredient
            $recipe_ingredient = new RecipeIngredient();
            $recipe_ingredient->amount = $_POST["iamount".$i];
            $recipe_ingredient->fkey_recipe_id = $recipe->id;
            $recipe_ingredient->fkey_ingredient_id = $ingredient->id;
            $recipe_ingredient->fkey_unit_id = $unit->id;
            $recipe_ingredient->store();
          }
        }
        //removes the tie to add the updated information
        $recipe->clearSteps();
        if(!empty($_POST['NumSteps'])) {
          //NOTE: if there in ingredients to add
          $n = (int)$_POST['NumSteps'];
          for ($i = 0; $i < $n; $i++) {
            $step = new Step();
            $step->description = $_POST["sdescription".$i];
            $step->fkey_recipe_id = $recipe->id;
            $step->store();
          }
        }

        $status_array = array("success" => 1);
        $return_array = array("status" => $status_array);
        echo json_encode($return_array);

      } catch(Exception $e) {

        // commits and rolls back the mysql transaction
        //$recipe->discard_transaction();
        $status_array = array("success" => 0, "error_code" => $e->getMessage());
        $return_array = array("status" => $status_array);
        echo json_encode($return_array);
      }

    } else {
      $status_array = array("success" => 0, "error_code" => INVALID_USERNAME);
      $return_array = array("status" => $status_array);
      echo json_encode($return_array);
    }
} else {
  $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
  $return_array = array("status" => $status_array);
  echo json_encode($return_array);
}
