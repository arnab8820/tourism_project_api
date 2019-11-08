<?php

require "connect.php";

if(!isset($_GET["lat"]) && !isset($_GET["lon"])){
    die(json_encode(array("error" => true, "message" => "Not all parameters specified")));
}

$sql = "select place_id, place_name, round(ST_Distance_Sphere(place_position, ST_GeomFromText(?, 4326))/1000) as distance from place order by distance limit 5";

$stmnt = $dbhandler -> prepare($sql);
if ($stmnt -> execute(array("POINT(".$_GET["lat"]." ".$_GET["lon"].")"))){
    $result= $stmnt->fetchAll(PDO::FETCH_ASSOC);
    echo(json_encode(array("error" => false, "message" => $result)));
}
