<?php

	require("./connect.php");
	session_start();
	$_SESSION = array();

	$db = new DB();
	$conn = $db->connect();

	$sql = "SELECT * FROM `accounts`";
	$accounts = $conn->query($sql);

	$select = $conn->prepare("SELECT `authenticate`.`valid` FROM `authenticate` INNER JOIN `accounts` ON `authenticate`.`acc_id` = `accounts`.`id` WHERE `accounts`.`name`=:name");
	$select->bindParam(':name', $_POST["name"]);
	$select->execute();
	$valid = ($select->fetch(PDO::FETCH_ASSOC))["valid"];

	$error = 0;
	while($row = $accounts->fetch(PDO::FETCH_ASSOC))
	{
		if ($row["name"] == $_POST["name"])
		{
			$_SESSION["account"] = $_POST["name"];
			if ((hash('whirlpool', $_POST['pw']) == $row["pw"]))
			{
				if ($valid == 1)
				{
					header('Location: ./home.phtml');
					exit;
				}
				else
				{
					header('Location: ./unknown.html');
					exit;
				}
			}
			else
			{
				$error = 2;
			}
		}
	}
	if (isset($_POST["name"]) && $error == 0)
		$error = 1;

?>
<html>
	<head>
		<title>Camagru login</title>
		<link rel="stylesheet" href="./css/login.css">
		<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	</head>
	<body>
		<div class="centralize">
			<form id="login-form" method="post" autocomplete="off">
				<div class="input-title">Username
				</div>
				<?php
					if ($error == 1)
					{
						echo "<p class='incorrect'>incorrect username ಠ╭╮ಠ</p>";
					}
				?>
				<input class="input-field" type="text" name="name" maxlength="42"
					<?php
						if (isset($_SESSION["account"]))
						{
							echo 'value="'.$_SESSION["account"].'"';
						}
					?>
				required>
				<div class="input-title">Password</div>
				<?php
				if ($error == 2)
				{
					echo "<p class='incorrect'>incorrect password (ಥ﹏ಥ)</p>";
				}
				?>
				<input class="input-field" type="password" name="pw" required <?php
						if (isset($_SESSION["account"]))
						{
							echo 'autofocus';
						}
					?>
				><br>
				<input id="submit" type="submit" value="Login" required><br>
			</form>
			<div class="create_account_btn">
				<p>or</p>
				<button class="create_acc_btn" onclick="window.location.href='./create.php'">
					<img class="cute_ghost" src="./imgs/cute_ghost_left.gif">
						<p>Create an account</p>
					<img class="cute_ghost" src="./imgs/cute_ghost_right.gif">
				</button>
			</div>
		</div>
	</body>
</html>
