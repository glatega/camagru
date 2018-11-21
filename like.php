<?php
require("./connect.php");

$headers = getallheaders();
if ($headers["Content-type"] == "application/json") {
    $info = json_decode(file_get_contents("php://input"), true);
    $user = new USER("name", $info["user"]);
	$user->like_update($info["image"], $info["like"]);
}
?>
