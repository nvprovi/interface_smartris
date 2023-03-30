<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__.'/constants.php');
require(__DIR__.'/functions.php');
require(__DIR__.'/database.php');
require(__DIR__.'/vendor/autoload.php');

//$sqLite = new SQLite3(__DIR__."/local_storage.db");