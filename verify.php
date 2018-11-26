<?php require("./connect.php"); ?>
<html>
	<head>
		<title>Camagru</title>
		<link rel="stylesheet" href="./css/style.css">
		<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	</head>
	<body>
	<style>
	#countdown {
		color: white;
	}
	</style>
		<div id="mail_div">
			<p id="mail_msg">

				<?php
					session_start();

					if (isset($_GET["user"])) {
						$user = new USER("name", $_GET["user"]);
						if (!$user->is_valid()) {
							if ($user->token_matches($_GET["token"])) {
								$user->validate_account();
								echo "Your account has been validated!<br>Redirecting you to the login screen in <b id='countdown'>5</b>";
							} else {
								echo "There was an issue validating your account information";
							}
						}
					}
				?>

			</p>
		</div>
	</body>
	<script>
	document.addEventListener("DOMContentLoaded", function() {
		countdown = document.getElementById("countdown");
		if (countdown) {
			count = setInterval(function(){
				num = Number(countdown.innerHTML);
				if (num == 0) {
					clearInterval(count);
					window.location.replace('login.php');
				} else {
					countdown.innerHTML = num - 1;
				}
			}, 1000);
		}
	});
	</script>
</html>
