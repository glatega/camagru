<?php
// require("./connect.php");

// if (isset($_POST["user"])) {
//     $user = new USER("name", $_POST["user"]);
//     $sql = $user->connection->prepare("INSERT INTO `pictures`(`addr`, `acc_id`) VALUES (:name, :id)");
//     $sql->bindParam(":id", $user->id);
//     $sql->bindParam(":name", $user->username);
//     $sql->execute();
// }
$headers = getallheaders();
if ($headers["Content-type"] == "application/json") {
    $stuff = json_decode(file_get_contents("php://input"), true);
    print_r($stuff);
}
?>