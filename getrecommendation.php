<?php
require "connect.php";

if(!isset($_GET["userid"])&&!isset($_GET["lat"])&&!isset($_GET["lon"])) {
    die(json_encode(array("error" => true, "message" => "All parameters not provided")));
}

$ret = array();
//echo "<pre>";
$sql = "select place_id, place_name, place_description, round(ST_Distance_Sphere(place_position, ST_GeomFromText(?, 4326))/1000) as distance from place where place.place_id in".
"(select distinct place_tags.place_id from place_tags where place_tags.cat_id in".
      "(select cat_id from user_interest where user_id = ?)) order by distance limit 1";

$stmnt = $dbhandler->prepare($sql);
if ($stmnt->execute(array("POINT(".$_GET["lat"]." ".$_GET["lon"].")", $_GET["userid"]))){
    $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    $ret["error"] = false;
    $ret["nextstop"] = $result[0];
} else{
    $ret["error"] = true;
    $ret["message"] = "Something went wrong";
}
//print_r($result);

$sql = "select place_id, place_name, place_description, round(ST_Distance_Sphere(place_position, ST_GeomFromText(?, 4326))/1000) as distance from place where place_id in".
"(select distinct place_tags.place_id from place_tags where place_tags.cat_id in".
"(select cat_id from user_interest where user_id = ?)) limit 10;";

$stmnt = $dbhandler->prepare($sql);
if($stmnt->execute(array("POINT(".$_GET["lat"]." ".$_GET["lon"].")", $_GET["userid"]))){
    $result2 = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    $ret["error"] = false;
    $ret["recomendation"] = $result2;
} else {
    $ret["error"] = true;
    $ret["message"] = "Something went wrong";
}
echo(json_encode($ret));
//2088607857911331