<?php
include_once '../includes/header_info.php';
include_once '../errors.php';
include_once '../controller-model/User.php';

$user = new User();
//check to assure post data is formed correctly
if( !empty($_POST['username']) && !empty($_POST['password']) ) {
  try {
    //set the post data to the appr member data objects
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    // attempt to validate the login
    $user->validateLogin();
    // successfully validated the login
    $user_arr = array(
        "id" =>  $user->id,
        "username" => $user->username,
        "password" => $user->password,
        "fkey_theme_id" => $user->fkey_theme_id,
        "theme" => $user->theme
    );
    // send the client the news!!!
    $status_array = array("success" => 1);
    $return_array = array("status" => $status_array, "content" => $user_arr);
    echo json_encode($return_array);
  } catch(Exception $e) {
    // attempt to validate the login failed
    $status_array = array("success" => 0, "error_code" => $e->getMessage());
    $return_array = array("status" => $status_array);
    echo json_encode($return_array);
  }
} else {
  // data is incomplete , alert the client
  $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
  $return_array = array("status" => $status_array);
  echo json_encode($return_array);
}
?>
