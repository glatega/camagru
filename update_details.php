<?php
require("./connect.php");

$headers = getallheaders();
if ($headers["Content-type"] == "application/json") {
	$info = json_decode(file_get_contents("php://input"), true);
	$user = new USER("name", $info["account"]);
	if ($info["field"] == "name") {
		$result = $user->is_new_name_taken($info["value"]);
		if ($result) {
			echo "invalid";
		} else {
			echo "valid";
		}
	} else if ($info["field"] == "email") {
		$result = $user->is_new_email_taken($info["value"]);
		if ($result) {
			echo "invalid";
		} else {
			echo "valid";
		}
	} else if ($info["field"] == "save") {
		$user->update_details($info["name"], $info["comments"], $info["likes"]);
		if ($info["new_email"] == "yes") {
			$user->update_email($info["email"]);
		}
	} else if ($info["field"] == "pw_change") {
		$user->send_PW_reset_email();
	} else if ($info["field"] == "pw_save") {
		$success = $user->save_new_PW($info["pw"], $info["token"]);
		if ($success) {
			echo "successful";
		} else {
			echo "unsuccessful";
		}
	}
}
?>
