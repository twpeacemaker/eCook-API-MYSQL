<?php
include_once '../includes/header_info.php';
include_once '../errors.php';

include_once '../controller-model/Recipe.php';
include_once '../controller-model/User.php';

// read the details of user to be edited
//print_r($_POST);
if( !empty($_POST['recipe_id']) && ($_POST['viewable'] == 1 || $_POST['viewable'] == 0) && !empty($_POST['username']) ) {
  $user = new User;
  $user->username = $_POST['username'];
  $user->show();
  try {
    $recipe = new Recipe();
    $recipe->id = $_POST['recipe_id'];
    $recipe->fkey_user_id = $user->id;
    $recipe->show();

    if($user->id == $recipe->fkey_creator_id) {
      // is the value th client wasnt the viewable to be set to
      $recipe->viewable = $_POST['viewable'];
      $recipe->publish();
      $status_array = array("success" => 1);
      $return_array = array("status" => $status_array);
      echo json_encode($return_array);
    } else {
      throw new Exception(NONE_OWNER);
    }
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
