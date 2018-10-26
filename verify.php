<?php

	require("./connect.php");
	session_start();
	$db = new DB();
	$conn = $db->connect();

	$select = $conn->prepare("SELECT `accounts`.`name`, `authenticate`.`token`, `authenticate`.`valid` FROM `accounts` INNER JOIN `authenticate` ON `accounts`.`id` = `authenticate`.`acc_id` WHERE `name`=:user");
	$select->bindParam(':user', $_GET["user"]);
	$select->execute();
	
	$account = $select->fetch(PDO::FETCH_ASSOC);

	if (!$account["valid"] && (hash("whirlpool", $account["token"]) == $_GET["token"]))
	{
		$insert = $conn->prepare("UPDATE `authenticate` INNER JOIN `accounts` ON `accounts`.`id` = `authenticate`.`acc_id` SET `authenticate`.`valid` = 1 WHERE `accounts`.`name` = :user");
		$insert->bindParam(':user', $_GET["user"]);
		$insert->execute();
	}
	header('Location: ./login.php');

?>
