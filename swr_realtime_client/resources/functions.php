<?php

function is_json($string) {
  if(is_null($string)){return false;}
  json_decode($string);
  return (json_last_error() == JSON_ERROR_NONE);
}

function process_json_input($message){
  if(!is_json($message)){return false;}
  $data = json_decode($message, true);
  if(!isset($data['data']) OR !is_array($data['data']) OR empty($data['data'])){return false;}
  $time_created = isset($data['created']) ? strtotime($data['created']) : time();
  foreach($data['data'] as $vehicle_info){
    update_vehicle_information($time_created, $vehicle_info);
  }
}

function create_vehicle_id($kennzeichen){
  return strtolower(str_replace(array("-"), array(""), $kennzeichen));
}

function update_vehicle_information($time_created, $vehicle_info){
  global $mysqli_vectura;
  $time_received = time();
  $vehicle_info['vehicle'] = create_vehicle_id($vehicle_info['vehicle']);
  $vehicle_info['location']['latitude'] = isset($vehicle_info['location']['latitude']) ? doubleval($vehicle_info['location']['latitude']) : NULL;
  $vehicle_info['location']['longitude'] = isset($vehicle_info['location']['longitude']) ? doubleval($vehicle_info['location']['longitude']) : NULL;
  $mysqli_vectura->query("INSERT INTO `realtime_data_swr` (
      `vehicle_id`, `line`, `route`, `occupancy_total`, `latitude`, `longitude`, `time_created`, `time_received`
    ) VALUES (
      '".$vehicle_info['vehicle']."',
      '".$vehicle_info['line']."',
      '".$vehicle_info['route']."',
      '".$vehicle_info['fillAbs']."',
      '".$vehicle_info['location']['latitude']."',
      '".$vehicle_info['location']['longitude']."',
      '".$time_created."',
      '".$time_received."'
    )
    ON DUPLICATE KEY UPDATE 
      `line` = '".$vehicle_info['line']."',
      `route` = '".$vehicle_info['route']."',
      `occupancy_total` = '".$vehicle_info['fillAbs']."',
      `latitude` = '".$vehicle_info['location']['latitude']."',
      `longitude` = '".$vehicle_info['location']['longitude']."',
      `time_created` = '".$time_created."',
      `time_received` = '".$time_received."'
  ");
}

function get_process_id(){
  return getmypid();
}

function store_process_id(){
  global $sqLite;
  $pid = get_process_id();
  $sqLite->exec("UPDATE cronjob_check SET timestamp = $pid WHERE check_type='current_process_id'");
}



function cronjob_database_status(){
  global $sqLite;
  $sqLite->exec("UPDATE cronjob_check SET timestamp = '".time()."' WHERE check_type='last_executed'");
  $sqLite->exec("UPDATE cronjob_check SET timestamp = 1 WHERE check_type='connect_active'");
}

function cronjob_get_connect_status(){
  global $sqLite;
  $result = $sqLite->query("SELECT timestamp FROM cronjob_check WHERE check_type='connect_active'");
  $row = $result -> fetchArray();
  if($row['timestamp'] == 1){return true;}
  return false;
}


function get_last_process_id(){
  global $sqLite;
  $result = $sqLite->query("SELECT timestamp FROM cronjob_check WHERE check_type='current_process_id'");
  $row = $result -> fetchArray();
  if($row['timestamp'] != 0){return $row['timestamp'];}
  return false;
}

function check_process_runnning(){
  $exists = false;
  exec("ps -A | grep -i php | grep -v grep", $pids);
  $last_pid = get_last_process_id();
  foreach($pids as $entry){
    $entry_array = explode(" ", $entry);
    if($entry_array[0] != 0 AND $entry_array[0] == $last_pid){return true;}
  }
  return false;
}

function kill_all_php_processes(){
  exec("sudo kill $(ps aux | grep '[p]hp' | awk '{print $2}')");
  return true;
}

function kill_all_php_processes_but_this_one(){
  $current_id = get_process_id();
  exec("ps -A | grep -i php | grep -v grep", $pids);
  foreach($pids as $entry){
    $entry = trim($entry);
    $entry_array = explode(" ", $entry);
    echo"checking ".$entry_array[0]." "; 
    if($entry_array[0] != 0 AND $entry_array[0] == $current_id){
      echo " - keeping\r\n";  
      continue;
    }
    exec("sudo kill ".$entry_array[0]);
    echo " - killing\r\n";
  }
  return true;
}