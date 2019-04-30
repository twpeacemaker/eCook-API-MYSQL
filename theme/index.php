<?php
// required headers
include_once '../includes/header_info.php';

// include database and object files
include_once '../config/database.php';
include_once '../controller-model/Theme.php';



// initialize object
$theme = new Theme();

$st = $theme->index();
$num = $st->rowCount();
// check if more than 0 record found
if($num>0) {
    // themes array
    $themes_arr=array();
    $themes_arr["records"]=array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $theme_item=array(
            "id" => $id,
            "theme_name" => $theme_name
        );
        array_push($themes_arr["records"], $theme_item);
    }
    // set response code - 200 OK
    http_response_code(200);
    // show themes data in json format
    echo json_encode($themes_arr);
} else {
    // set response code - 404 Not found
    http_response_code(404);
    // tell the theme no themes found
    echo json_encode(
        array("error" => "No themes found.")
    );
}
