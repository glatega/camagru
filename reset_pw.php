<?php require("./connect.php"); ?>
<html>
	<head>
		<title>Camagru</title>
		<link rel="stylesheet" href="./css/style.css">
		<link href='https://fonts.googleapis.com/css?family=Bigelow Rules' rel='stylesheet'>
		<link href='https://fonts.googleapis.com/css?family=Black And White Picture' rel='stylesheet'>
	</head>
	<body>
		<div class="settings" style="transform: translateY(-50%) translateX(-50%); position: relative; top: 50%; left: 50%;">
			<div class="input-title">Password</div>
			<input class="input-field" type="password" id="pw1" required><br>
			<div class="input-title">Confirm Password</div>
			<input class="input-field" type="password" id="pw2" required><br>
			<div id="save_details" onclick="savePW()">
				<p id="save_text" style="text-align: center">Save</p>
			</div>
		</div>
		<script>
		function savePW() {
			pw1 = document.getElementById("pw1");
			pw2 = document.getElementById("pw2");
			if (pw1.value == pw2.value) {
				var json = {
					field: "pw_save",
					account: "<?php echo $_GET["user"]; ?>",
					token: "<?php echo $_GET["token"]; ?>",
					pw: pw1.value
				}

				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'update_details.php', true);
				xhr.setRequestHeader('Content-type', 'application/json');
				xhr.onreadystatechange = function (data) {
					if (xhr.readyState == 4 && xhr.status == 200) {
						if (xhr.responseText = "successful") {
							alert("Your new password has been saved!");
						} else if (xhr.responseText = "unsuccessful") {
							alert("Something went wrong. Unable to update password.");
						}
					}
				}
				xhr.send(JSON.stringify(json));
			} else {
				alert("Passwords do not match!");
				pw1.value = "";
				pw2.value = "";
				pw1.focus();
			}
		}
		</script>
	</body>
</html>
