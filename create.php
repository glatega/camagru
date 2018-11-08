<?php

	require("./connect.php");
	session_start();

	$name_exists = 0;
	$email_exists = 0;
	if (isset($_POST["name"])) {
		$db = new CONNECTION();
		if ($db->username_exists($_POST["name"])) {
			$name_exists = 1;
		}
		if ($db->email_exists($_POST["email"])) {
			$email_exists = 1;
		}
		if (!($name_exists || $email_exists)) {
			if ($db->create_account($_POST["name"], $_POST["pw"], $_POST["email"])) {
				header('Location: ./mail.php?email=sent');
			} else {
				header('Location: ./mail.php?email=failed');
			}
			exit;
		}
	}

?>
<html>
	<head>
		<title>Camagru</title>
		<link rel="stylesheet" href="./css/style.css">
		<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	</head>
	<body>
		<div class="centralize">
			<form id="login-form" method="post">
				<div class="input-title">Username</div>
				<?php
					if ($name_exists == 1)
					{
						echo "<p class='incorrect'>username already taken ლ(ಠ益ಠლ)</p>";
					}
				?>
				<input class="input-field" type="text" name="name" maxlength="42" required>
				<div class="input-title">Email</div>
				<?php
					if ($email_exists == 1)
					{
						echo "<p class='incorrect'>email already in use ಠ_ಥ</p>";
					}
				?>
				<input class="input-field" type="email" name="email" maxlength="64" required>
				<div class="input-title">Password</div>
				<input class="input-field" type="password" name="pw" required><br>
				<input id="submit" type="submit" value="Create account" required>
			</form>
			<div class="create_account_btn">
				<p>or</p>
				<button class="create_acc_btn" onclick="window.location.href='./login.php'">
					<img class="cute_ghost" src="./imgs/resources/cute_ghost_left.gif">
						<p>Login</p>
					<img class="cute_ghost" src="./imgs/resources/cute_ghost_right.gif">
				</button>
			</div>
		</div>
	</body>
</html>
