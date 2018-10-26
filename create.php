<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require("./connect.php");
	require("./vendor/autoload.php");
	session_start();
	$_SESSION = array();

	$db = new DB();
	$conn = $db->connect();

	$sql = "SELECT * FROM `accounts`";
	$accounts = $conn->query($sql);
	while($row = $accounts->fetch(PDO::FETCH_ASSOC))
	{
		if ($row["name"] == $_POST["name"])
		{
			$name_exists = 1;
		}
		if ($row["email"] == $_POST["email"])
		{
			$email_exists = 1;
		}
	}
	if (!($name_exists || $email_exists) && isset($_POST["name"]))
	{
		$insert = $conn->prepare("INSERT INTO `accounts` (`name`, `pw`, `email`) VALUES (:username, :pw, :email)");
		$insert->bindParam(':username', $username);
		$insert->bindParam(':pw', $password);
		$insert->bindParam(':email', $email);
		$username = $_POST["name"];
		$password = hash('whirlpool', $_POST["pw"]);
		$email = $_POST["email"];
		$insert->execute();

		$select = $conn->prepare("SELECT `id` FROM `accounts` WHERE `email`=:email");
		$select->bindParam(':email', $email);
		$select->execute();
		$acc_id = ($select->fetch(PDO::FETCH_ASSOC))["id"];
		$token = rand();

		$insert = $conn->prepare("INSERT INTO `authenticate` (`acc_id`, `token`) VALUES (:acc_id, :token)");
		$insert->bindParam(':acc_id', $acc_id);
		$insert->bindParam(':token', $token);
		$insert->execute();

		$hash = hash('whirlpool', $token);

		$site = "http://localhost:8080/camagru/verify.php?user=" . $username . "&token=" . $hash;
		$msg = "<html><body><p>Welcome to the CAMAGRU community, <strong>" . $username . "</strong></p><br><br>";
		$msg .= "Before continuing on to make amazing pictures, please verify your email address by <a href='" . $site . "'>clicking here.</a></body></html>";

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 1;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->IsHTML(true);
		$mail->Username = "camagrurmdaba@gmail.com";
		$mail->Password = "rootyroot";
		$mail->SetFrom("camagrurmdaba@gmail.com");
		$mail->Subject = "Email verification";
		$mail->Body = $msg;
		$mail->AddAddress($email);

		if ($mail->Send())
		{
			header('Location: ./mail.php?email=sent');
			exit;
		}
		else
		{
			$_SESSION["mail_fail"] = $mail->ErrorInfo;
			header('Location: ./mail.php?email=failed');
			exit;
		}
	}

?>
<html>
	<head>
		<title>Create account</title>
		<link rel="stylesheet" href="./css/login.css">
		<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	</head>
	<body>
		<div style="color:white">
			Create an account
		</div>
		<div class="centralize">
			<form id="login-form" method="post">
				<div class="input-title">Username</div>
				<?php
					if ($name_exists == 1)
					{
						echo "<p class='incorrect'>username already exists ლ(ಠ益ಠლ)</p>";
					}
				?>
				<input class="input-field" type="text" name="name" maxlength="42" required>
				<div class="input-title">Email</div>
				<?php
					if ($email_exists == 1)
					{
						echo "<p class='incorrect'>email already exists ಠ_ಥ</p>";
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
					<img class="cute_ghost" src="./imgs/cute_ghost_left.gif">
						<p>Login</p>
					<img class="cute_ghost" src="./imgs/cute_ghost_right.gif">
				</button>
			</div>
		</div>
	</body>
</html>
