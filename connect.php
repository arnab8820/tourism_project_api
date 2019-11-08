<?php
$dbhost = "fdb21.awardspace.net";           //database hostname
$dbusername = "2715305_tourism";       //database username
$dbpassword = "app3secret";       //database password
$database = "2715305_tourism";         //database name to be used
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
try{
    $dbhandler = new PDO("mysql:host=".$dbhost.";dbname=".$database, $dbusername, $dbpassword);
    $dbhandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbhandler->query("SET CHARACTER SET utf8;");
}catch (Exception $e){
    //write_log($_SERVER["SERVER_ADDR"], "Server", $e->getMessage());
    echo $e->getMessage();
    die("There was an error");
}