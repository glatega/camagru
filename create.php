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
				header('Location: ./mail.php');
				exit;
			}
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
				<p id="pwcomplexity" style="visibility: inherit;" class='incorrect'>Password must contain at least one uppercase letter, lowercase letter and number</p>
				<input class="input-field" type="password" name="pw" oninput="passwordComplexity(this.value)" required><br>
				<input id="submit" type="submit" value="Create account" disabled="disabled" required>
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
	<script>
	function passwordComplexity($pw) {
		$upper = /[A-Z]{1,}/.test($pw) ? 1 : 0;
		$lower = /[a-z]{1,}/.test($pw) ? 1 : 0;
		$number = /[0-9]{1,}/.test($pw) ? 1 : 0;
		$warning = document.getElementById("pwcomplexity");
		$submit = document.getElementById("submit");
		if ($upper && $lower && $number) {
			$warning.setAttribute("style", "visibility: hidden;");
			$submit.removeAttribute("disabled");
		} else {
			$warning.setAttribute("style", "visibility: inherit;");
			$submit.setAttribute("disabled", "disabled");
		}
	}
	</script>
</html>
