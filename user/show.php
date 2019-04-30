<?php
include_once '../includes/header_info.php';
include_once '../errors.php';
include_once '../controller-model/User.php';

$user = new User();
if( !empty($_POST['username']) ) {
  //get the username from the postdata
  $user->username = $_POST['username'];
  try {
    //try to get the user
    $user->show();
    //successfully got the user and getting its properties to send to the client
    $user_arr = array(
        "id" =>  $user->id,
        "username" => $user->username,
        "password" => $user->password,
        "fkey_theme_id" => $user->fkey_theme_id,
        "theme" => $user->theme
    );
    // alert the client with success and the information about the user
    $status_array = array("success" => 1);
    $return_array = array("status" => $status_array, "content" => $user_arr);
    echo json_encode($return_array);
  } catch (Exception $e) {
    // alert the client with the error code is recieved
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
