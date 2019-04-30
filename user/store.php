<?php
include_once '../includes/header_info.php';
include_once '../errors.php';
include_once '../controller-model/User.php';

// make sure post data is not empty
if( !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['fkey_theme_id']) ){
    $user = new User();
    // set user property values to be stored in the database
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->fkey_theme_id = $_POST['fkey_theme_id'];
    try {
      // attempt to create the user
      $user->store();
      // attempt was successful, alert the client
      $status_array = array("success" => 1);
      $return_array = array("status" => $status_array);
      echo json_encode($return_array);
    } catch(Exception $e) {
      // attempt was not successful, alert the client
      $status_array = array("success" => 0, "error_code" => $e->getMessage());
      $return_array = array("status" => $status_array);
      echo json_encode($return_array);
    }
} else{
    // data is incomplete , alert the client
    $status_array = array("success" => 0, "error_code" => POST_DATA_MALFORMED);
    $return_array = array("status" => $status_array);
    echo json_encode($return_array);
}
