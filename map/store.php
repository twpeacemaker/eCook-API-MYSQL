<?php
// required headers
include_once '../includes/header_info.php';

// instantiate user object
include_once '../controller-model/User.php';
include_once '../controller-model/RecipeUserMap.php';
include_once '../errors.php';

$status_array = array();
// make sure data is not empty
if( !empty($_POST['username']) && !empty($_POST['recipe_id'])) {
    // NOTE: stoped here
    $user = new User();
    $user = $user->get_user($_POST['username']);
    // //NOTE: check if the user exists
    if(isset($user["id"])) {
      try {
        $map = new RecipeUserMap();
        $map->fkey_user_id = $user["id"];
        $map->fkey_recipe_id = $_POST['recipe_id'];
        $map->store();
        $status_array = array("success" => 1);
      } catch(Exception $e) {
        // commits and rolls back the mysql transaction
        $status_array = array("success" => 0, "error_code" => $e->getMessage());
      }
    } else {
      $status_array = array("success" => 0, "error_code" => INVALID_USERNAME);
    }
} else {
  $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
}
$return_array = array("status" => $status_array);
echo json_encode($return_array);
