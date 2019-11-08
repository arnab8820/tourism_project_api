<?php

require "connect.php";

if(!isset($_GET["user_id"])&& !isset($_GET["lat"]) && !isset($_GET["lon"])){
    die(json_encode(array("error" => true, "message" => "Not all parameters given")));
}

$sql = "insert into visited (userid, lat, lon) values (?, ?, ?)";

$stmnt = $dbhandler -> prepare($sql);
if ($stmnt -> execute(array($_GET["user_id"], $_GET["lat"], $_GET["lon"]))){
    echo(json_encode(array("success" => true, "message" => "Inserted successfully")));
}
