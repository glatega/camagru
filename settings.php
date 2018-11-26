<?php
	require("./account_bar.php");
	$details = $user->get_account_details();
?>
<div class="settings">
	<div class="input-title">Username</div>
	<p id="valid_name" valid="true" class='incorrect'>Change your username below</p>
	<input id="name_change" class="input-field" type="text" name="name" maxlength="42" required <?php echo "value='".$details["name"]."'"; ?> oninput="checkIfTaken('name')">
	<div class="input-title">Email</div>
	<p id="valid_email" valid="true" class='incorrect'>Change your email below</p>
	<input  id="email_change" class="input-field" type="email" name="email" maxlength="64" original="<?php echo $details["email"]; ?>" required <?php echo 'value="'.$details["email"].'"'; ?> oninput="checkIfTaken('email')">

	<div id="checkboxes">
		<input id="commentable" type="checkbox" onclick="checkThisOut(this)" <?php if ($details["commentable"]) { echo "checked"; } ?>> Send me emails for new comments<br>
		<input id="likeable" type="checkbox" onclick="checkThisOut(this)" <?php if ($details["likeable"]) { echo "checked"; } ?>> Send me emails for new likes<br>
	</div>

	<div id="save_details" onclick="saveClick()">
		<p id="save_text">Save</p>
	</div>

	<div id="pw_details" onclick="resetPW()">
		<p id="pw_text">Reset Password</p>
	</div>
</div>

<script>

	function resetPW() {
		var json = {
			field: "pw_change",
			account: "<?php echo $_SESSION["account"]; ?>"
		}

		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'update_details.php', true);
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.onreadystatechange = function (data) {
			if (xhr.readyState == 4 && xhr.status == 200) {
				alert("You have been sent an email to change your password");
			}
		}
		xhr.send(JSON.stringify(json));
	}

	function saveClick() {
		$valid_name = document.getElementById("valid_name").getAttribute("valid");
		$valid_email = document.getElementById("valid_email").getAttribute("valid");
		if ($valid_name == "true" && $valid_email == "true") {
			
			$name = document.getElementById("name_change").value;
			$email = document.getElementById("email_change").value;
			$orig_email = document.getElementById("email_change").getAttribute("original");
			$comments = document.getElementById("commentable").hasAttribute("checked") ? 1 : 0;
			$likes = document.getElementById("likeable").hasAttribute("checked") ? 1 : 0;

			$email_changed = $email == $orig_email ? "no" : "yes";

			var json = {
				field: "save",
				account: "<?php echo $_SESSION["account"]; ?>",
				name: $name,
				email: $email,
				comments: $comments,
				likes: $likes,
				new_email: $email_changed
			}

			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'update_details.php', true);
			xhr.setRequestHeader('Content-type', 'application/json');
			xhr.onreadystatechange = function (data) {
				if (xhr.readyState == 4 && xhr.status == 200) {
					alert("Your changes have been saved!");
				}
			}
			xhr.send(JSON.stringify(json));

		} else {
			alert("Make sure your username and email are valid!");
		}
	}

	function checkThisOut($this) {
		if ($this.hasAttribute("checked")) {
			$this.removeAttribute("checked");
		} else {
			$this.setAttribute("checked", '');
		}
	}

	function checkIfTaken($field) {
		if ($field == "name") {
			$valid_name = document.getElementById("valid_name");
			$name = document.getElementById("name_change").value;

			if ($name.length > 0) {
				var json = {
					account: "<?php echo $_SESSION["account"]; ?>",
					field: "name",
					value: $name
				}

				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'update_details.php', true);
				xhr.setRequestHeader('Content-type', 'application/json');
				xhr.onreadystatechange = function (data) {
					if (xhr.readyState == 4 && xhr.status == 200) {
						$response = xhr.responseText;
						if ($response == "valid") {
							$valid_name.innerHTML = "username is valid";
							$valid_name.setAttribute("valid", "true");
						} else if ($response == "invalid") {
							$valid_name.innerHTML = "username already taken";
							$valid_name.setAttribute("valid", "false");
						}
					}
				}
				xhr.send(JSON.stringify(json));
			} else {
				$valid_name.innerHTML = "please enter a username";
				$valid_name.setAttribute("valid", "false");
			}

		} else if ($field == "email") {
			$valid_email = document.getElementById("valid_email");
			$email = document.getElementById("email_change").value;

			if ($email.length > 0) {
				$viable = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test($email) ? 1 : 0;

				if ($viable) {

					var json = {
						account: "<?php echo $_SESSION["account"]; ?>",
						field: "email",
						value: $email
					}

					var xhr = new XMLHttpRequest();
					xhr.open('POST', 'update_details.php', true);
					xhr.setRequestHeader('Content-type', 'application/json');
					xhr.onreadystatechange = function (data) {
						if (xhr.readyState == 4 && xhr.status == 200) {
							$response = xhr.responseText;
							if ($response == "valid") {
								$valid_email.innerHTML = "email is available";
								$valid_email.setAttribute("valid", "true");
							} else if ($response == "invalid") {
								$valid_email.innerHTML = "email already in use";
								$valid_email.setAttribute("valid", "false");
							}
						}
					}
					xhr.send(JSON.stringify(json));
				}
			} else {
				$valid_email.innerHTML = "please enter an email";
				$valid_email.setAttribute("valid", "false");
			}

		}
	}

</script>

<?php require("./footer.php"); ?>
