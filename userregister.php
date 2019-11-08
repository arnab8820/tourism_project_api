<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "Facebook/autoload.php";
require "connect.php";
session_start();

$interest = array();

$fb = new Facebook\Facebook([
    'app_id' => '403651563616178',
    'app_secret' => 'e26d832b1b6ffc0830f5c50154f78662',
]);

echo("<pre>");

//getting user detail
try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('me?fields=id,first_name,last_name', "EAAFvHnQP97IBAF9u6juNb0uCZAYQyGlOj1NDIdFRU4076y8ZBMMELh3UZCZAmqR7g9PUvPxwsoOUZBnX3McstnVNZBUwLZCsKFyxslCZAgNpZBvkcVEvC6NUclu5oT9wHuFLpFI7QYysWgEvEYpZCUvQM9VyJeJMydLVZBrKMBUZAxb9ZCcTANSIYftA3Q7bFACludZCWqd2PacaLw5AU4xjkDWZBpToSJ7XuQk0uvO7TMCIro1cUa1z2h8hMLI");
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
$usernode=json_decode($response->getBody());
$userid = $usernode->id;
echo "user id: ".$userid;
print_r($usernode);

//getting posted images detail
try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('me/photos?fields=place&limit=10000&type=uploaded', "EAAFvHnQP97IBAF9u6juNb0uCZAYQyGlOj1NDIdFRU4076y8ZBMMELh3UZCZAmqR7g9PUvPxwsoOUZBnX3McstnVNZBUwLZCsKFyxslCZAgNpZBvkcVEvC6NUclu5oT9wHuFLpFI7QYysWgEvEYpZCUvQM9VyJeJMydLVZBrKMBUZAxb9ZCcTANSIYftA3Q7bFACludZCWqd2PacaLw5AU4xjkDWZBpToSJ7XuQk0uvO7TMCIro1cUa1z2h8hMLI");
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

//getting available interest categories
$categories = array();
$sql = "select * from interest_category";
$stmnt = $dbhandler->prepare($sql);
if ($stmnt->execute()){
    $temp = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($temp as $item){
        $categories[$item["cat_name"]]= $item["cat_id"];
    }
}
print_r($categories);

//determinig user interests among the available categories
$inttotal=0;
$graphedge = $response->getGraphEdge();
foreach ($graphedge as $graphNode){
    $result = json_decode($graphNode->getProperty('place'));
    if($result != ""){

        $keyword = explode(" ", str_replace(",", "", $result->name));
        foreach ($keyword as $key){
            if(isset($categories[strtolower($key)])){
                if(isset($interest[strtolower($key)])){
                    $interest[strtolower($key)]++;
                    $inttotal += 1;
                } else{
                    $interest[strtolower($key)]=1;
                    $inttotal += 1;
                }
            }
        }

    }
}

//generating database query to insert user's choices
arsort($interest);
$sql = "insert into user_interest (user_id, cat_id, intensity) VALUES ";
foreach ($interest as $key => $value){
    $sql = $sql."(".$userid.", '".$categories[$key]."', ".round(($value/$inttotal)*100)."),";
    echo $key." -> ".round(($value/$inttotal)*100)."<br>";
}

$sql = rtrim($sql, ",");
$stmnt = $dbhandler->prepare($sql);
$stmnt->execute();