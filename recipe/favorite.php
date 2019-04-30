<?php
include_once '../includes/header_info.php';
include_once '../errors.php';

include_once '../controller-model/RecipeUserMap.php';
include_once '../controller-model/User.php';

// read the details of user to be edited
//print_r($_POST);
if( !empty($_POST['recipe_id']) && ($_POST['favorite'] == 1 || $_POST['favorite'] == 0) && !empty($_POST['username']) ) {
  $user = new User;
  $user->username = $_POST['username'];
  $user->show();
  try {
    $recipe_map = new RecipeUserMap();
    $recipe_map->fkey_recipe_id = $_POST['recipe_id'];
    $recipe_map->fkey_user_id = $user->id;
    $recipe_map->favorite = $_POST['favorite'];
    $recipe_map->favorite();
    
    $status_array = array("success" => 1);
    $return_array = array("status" => $status_array);
    echo json_encode($return_array);

  } catch (Exception $e) {
    $status_array = array("success" => 0, "error_code" => $e->getMessage());
    $return_array = array("status" => $status_array);
    echo json_encode($return_array);
  }
} else {
  $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
  $return_array = array("status" => $status_array);
  echo json_encode($return_array);
}
