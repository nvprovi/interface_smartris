<?php
$resourceFolder = "../../resources/";
require($resourceFolder.'config.php');

if(!isset($_SERVER['REQUEST_METHOD'])){echo"no requesttype";exit();}
$requestType = strtoupper($_SERVER['REQUEST_METHOD']);
if($requestType == 'GET'){
  echo"i am alive";
  exit();
}

$postBody = file_get_contents('php://input');
if(!is_json($postBody)){
  header("HTTP/1.1 406 Not Acceptable");
  echo"wrong input format";
  exit();
}
//header("HTTP/1.1 200 OK");
echo"post received";
process_json_input($postBody);

