<?php

	require("./connect.php");
	session_start();
	$user = new USER();
	
	if (!$user->is_valid($_GET["user"])) {
		if ($user->token_matches($_GET["user"], $_GET["token"])) {
			$user->validate_account($_GET["user"]);
		} else {
			//There was an issue validating your account information
		}
	}

	header('Location: ./login.php');

?>
