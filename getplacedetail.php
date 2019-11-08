<?php
require "connect.php";


if(!isset($_GET["place_id"])&& !isset($_GET["lat"]) && !isset($_GET["lon"])){
    die(json_encode(array("error" => true, "message" => "Not all parameters given")));
}

$sql = "select place_name, place_description, ST_Latitude(place_position) as lat, ST_Longitude(place_position) ".
              "as lon, round(ST_Distance_Sphere(place_position, ST_GeomFromText(?, 4326))/1000) as ".
              "distance from place where place_id=?";

$stmnt = $dbhandler -> prepare($sql);
//echo ("<pre>");
if ($stmnt->execute(array("POINT(".$_GET["lat"]." ".$_GET["lon"].")", $_GET["place_id"]))){
    $result= $stmnt->fetchAll(PDO::FETCH_ASSOC);
    $sql = "select cat_name from interest_category where cat_id in (select cat_id from place_tags where place_id=?)";
    $stmnt = $dbhandler->prepare($sql);
    if ($stmnt->execute(array($_GET["place_id"]))){
        $temp = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($temp as $tag){
            $result[0]["tags"][] = $tag["cat_name"];
        }

    }
    //print_r($result);
    echo(json_encode(array("error" => false, "message" => $result)));
}
