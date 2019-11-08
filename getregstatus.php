<?php
require "connect.php";

$sql = "select firstname, hitcount from user where userid=?";
$stmnt = $dbhandler->prepare($sql);
if($stmnt->execute(array($_GET["userid"]))) {
    $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) == 1) {
        echo(json_encode(array("registered" => true, "hitcount" => $result[0]["hitcount"])));
    } else {
        echo(json_encode(array("registered" => false)));
    }
}
