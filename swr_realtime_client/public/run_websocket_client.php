<?php
include "/home/ec2-user/swr_websocket_client/resources/config.php";
use WebSocket\Client as Client;
use WebSocket\ConnectionException as ConnectionException;

kill_all_php_processes_but_this_one();

$client_options = array(
  'timeout' => 30,
  'persistent' => true
);
$client = new Client("", $client_options);

$json_start = array(
  "username" => "",
  "password" => "",
  "request_type" => ""
);

$client -> text(base64_encode(json_encode($json_start)));
while (true) {
  try {
    $message = $client->receive();    
    process_json_input($message);
  } 
  catch (ConnectionException $e) {    
    //print_r("Error: ".$e->getMessage());
  }
}
$client->close();