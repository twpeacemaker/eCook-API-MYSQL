<?php
// required headers
include_once '../includes/header_info.php';

header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');


include_once '../controller-model/User.php';
include_once '../controller-model/RecipeUserMap.php';



// prepare user object
$user = new User();
// set username property of record to read
$user->username = $_POST['username'];
// read the details of user to be edited
$user->show();
if($user->username != null){
    $map = new RecipeUserMap();
    $map->fkey_user_id = $user->id;
    try {
      $array = $map->index();
      $status_array = array("success" => 1);
      $return_array = array("status" => $status_array, "content" => $array);
      echo json_encode($return_array);
    } catch(Exception $e) {
      // error occured when querying the Recipe Maps
      $status_array = array("success" => 0, "error_code" => $e->getMessage());
      $return_array = array("status" => $status_array);
      echo json_encode($return_array);
    }
} else {
  // tell the user user does not exist
  $status_array = array("success" => 0, "error_code" => INVALID_USERNAME);
  $return_array = array("status" => $status_array);
  echo json_encode($return_array);
}
?>
