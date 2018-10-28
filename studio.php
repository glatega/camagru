<?php

	require("./connect.php");
	$db = new DB();
	echo "gareth : " . $db->user_exists("gareth"); 
	echo "valid : " . $db->user_valid("gareth", hash("whirlpool", "1237438641"));
	if ($db->user_exists("gareth")) {
		echo "AYYYY IT'S YA BOY!";
	} else {
		echo "USER DOESN'T EXIST BRUH";
	}
	if ($db->user_valid("gareth", hash("whirlpool", "1237438641"))) {
		echo "VALID";
	} else {
		echo "INVALID";
	}

?>
