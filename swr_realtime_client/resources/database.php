<?php

/* hosteurope vectura db */
$namelogin="";
$passlogin = "";
$db="";
$host="";
$mysqli_vectura = new mysqli($host, $namelogin, $passlogin, $db);
if ($mysqli_vectura -> connect_errno) {
  echo "Failed to connect to MySQL SFP: (" . $mysqli_vectura->connect_errno . ") " . $mysqli_vectura->connect_error;
  exit();
}
