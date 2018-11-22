<?php
require("./account_bar.php");

if (isset($_GET["id"])) {
	$user->delete_picture($_GET["id"]);
	echo "deleting image ".$_GET["id"];
}
?>
