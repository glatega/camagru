<?php
require("./account_bar.php");

$headers = getallheaders();
if ($headers["Content-type"] == "application/json") {
    $info = json_decode(file_get_contents("php://input"), true);
    $user->save_comment($info["image"], $info["comment"]);
}
?>
