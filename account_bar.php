<?php
	session_start();

	require("./connect.php");

	if (isset($_SESSION["account"])) {
		$user = new USER("name", $_SESSION["account"]);
		$settings = $user->get_acc_settings();
	} else {
		header("Location: ./login.php");
		exit;
	}
?>
<script>
	var menu = 0;
	function clickSettings() {
		if (menu == 0) {
			menu = 1;
			document.getElementById("menu_icon").setAttribute("style", "transform: rotate(90deg);");
			document.getElementById("settings_drop").setAttribute("style", "display: block;");
		} else {
			menu = 0;
			document.getElementById("menu_icon").setAttribute("style", "transform: rotate(0deg);");
			document.getElementById("settings_drop").setAttribute("style", "display: none;");
		}
	}
	function gotoPage($page) {
		window.location.href = "./" + $page;
	}
</script>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" href="./css/style.css">
	<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
	<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	<link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
</head>
<body>
<table id="bar">
	<td id="bar_pp">
		<img height="75px" width="75px" src="./<?php echo $settings["profile_pic"]; ?>">
	</td>
	<td id="bar_name">
		<?php echo $_SESSION["account"]; ?>
	</td>
	<td id="bar_settings" onclick="clickSettings()">
		<img id="menu_icon" height="50px" width="50px" src="./imgs/menu_icon_h.svg">
	</td>
</table>
<div id="settings_drop">
	<div onclick="gotoPage('settings.php')">
		<img class="menu_img" height="25px" width="25px" src="./imgs/settings_gears.svg">
		Settings
	</div>
	<div onclick="gotoPage('logout.php')" id="logout">
		<img class="menu_img" height="25px" width="25px" src="./imgs/logout.svg">
		Logout
	</div>
</div>
