<?php
include_once '../includes/header_info.php';
include_once '../errors.php';

include_once '../controller-model/Recipe.php';
include_once '../controller-model/RecipeIngredient.php';
include_once '../controller-model/Step.php';
include_once '../controller-model/User.php';

$return_array = array();
// read the details of user to be edited
if( !empty($_POST['id']) ) {
  $recipe = new Recipe();
  $recipe->id = $_POST['id'];
  try {
    $recipe->show();
    // create array
    $recipe_arr = array(
        "id" =>  $recipe->id,
        "name" => $recipe->name,
        "description" => $recipe->description,
        "created_at" => $recipe->created_at,
        "fkey_creator_username" => $recipe->fkey_creator_username,
        "viewable" => $recipe->viewable,
        "favorite" => $recipe->favorite,
        "image" => $recipe->picture_string
    );
    $recipe_ingredients = new RecipeIngredient();
    $recipe_arr["ingredients"]=$recipe_ingredients->get_for_recipe($recipe->id);
    $steps = new Step();
    $recipe_arr["steps"] = $steps->get_for_recipe($recipe->id);
    // set response code - 200 OK
    $status_array = array("success" => 1);
    $return_array = array("status" => $status_array, "content" => $recipe_arr);
  } catch (Exception $e) {
    // tell the user user does not exist
    $status_array = array("success" => 0, "error_code" => $e->getMessage());
    $return_array = array("status" => $status_array);
  }
} else {
  $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
  $return_array = array("status" => $status_array);
}
echo json_encode($return_array);
