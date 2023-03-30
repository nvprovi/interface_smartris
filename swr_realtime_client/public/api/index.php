<?php

$path_array = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI']) : array();

$path_array['path'] = isset($path_array['path']) ? str_replace(array('/bc_vectura_swr_websocket_client/swr_realtime_client/public/api/', '/api/'), "", $path_array['path']) : "";

if($path_array['path'] == 'swr_realtime_client'){
  require('../../api_path/swr_realtime_client.php');
  exit();
}

header("HTTP/1.1 404 Not Found");
echo"path not recognized";
