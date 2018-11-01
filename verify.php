<?php

	require("./connect.php");
	session_start();

	if (isset($_GET["user"])) {
		$user = new USER("name", $_GET["user"]);
		if (!$user->is_valid()) {
			if ($user->token_matches($_GET["token"])) {
				$user->validate_account();
				echo "validated";
			} else {
				echo "There was an issue validating your account information";
			}
		}
	}

	// header('Location: ./login.php');

?>
