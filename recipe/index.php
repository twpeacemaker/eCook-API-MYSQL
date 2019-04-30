<?php
include_once '../includes/header_info.php';
include_once '../errors.php';

include_once '../controller-model/Recipe.php';

// initialize object
// check if more than 0 record found

$recipe = new Recipe();
try {
  $array = $recipe->index();
  $status_array = array("success" => 1);
  $return_array = array("status" => $status_array, "content" => $array);
  echo json_encode($return_array);
} catch(Exception $e) {
  // error occured when querying the Recipe Maps
  $status_array = array("success" => 0, "error_code" => $e->getMessage());
  $return_array = array("status" => $status_array);
  echo json_encode($return_array);
}
