<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require("./vendor/autoload.php");
require("./config/database.php");

class SERVER
{
	protected $connection;
	protected $DB_DSN = "mysql";
	protected $DB_HOST = "localhost";
	protected $DB_NAME = "camagru";
	protected $DB_USER = "root";
	protected $DB_PASSWORD = "admin1";

	public function __construct() {
		$this->connection = new PDO("$this->DB_DSN:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASSWORD);
	}

	protected function send_mail($email, $subject, $message) {
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
		$mail->SetFrom("camagrurmdaba@gmail.com", "Camagru Team");
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AddAddress($email);
		if ($mail->Send()) {
			return (1);
		}
		else {
			return (0);
		}
	}

	public function email_exists($email) {
		$sql = $this->connection->prepare("SELECT `id` FROM `accounts` WHERE `email` = :email");
		$sql->bindParam(":email", $email);
		$sql->execute();
		if ($sql->rowCount() > 0) {
			return (1);
		} else {
			return (0);
		}
	}
}

class CONNECTION extends SERVER
{
	public function __construct() {
		parent::__construct();
	}

	public function username_exists($username) {
		$sql = $this->connection->prepare("SELECT `id` FROM `accounts` WHERE `name` = :username");
		$sql->bindParam(":username", $username);
		$sql->execute();
		if ($sql->rowCount() > 0) {
			return (1);
		} else {
			return (0);
		}
	}

	public function email_exists($email) {
		$sql = $this->connection->prepare("SELECT `id` FROM `accounts` WHERE `email` = :email");
		$sql->bindParam(":email", $email);
		$sql->execute();
		if ($sql->rowCount() > 0) {
			return (1);
		} else {
			return (0);
		}
	}

	public function create_account($username, $password, $email) {
		$pw = hash('whirlpool', $password);
		$sql = $this->connection->prepare("INSERT INTO `accounts` (`name`, `pw`, `email`) VALUES (:username, :pw, :email)");
		$sql->bindParam(':username', $username);
		$sql->bindParam(':pw', $pw);
		$sql->bindParam(':email', $email);
		$sql->execute();

		$sql = $this->connection->prepare("SELECT `id` FROM `accounts` WHERE `name`=:username");
		$sql->bindParam(':username', $username);
		$sql->execute();

		$acc_id = ($sql->fetch(PDO::FETCH_ASSOC))["id"];
		$token = rand();

		$sql = $this->connection->prepare("INSERT INTO `authenticate` (`acc_id`, `token`) VALUES (:acc_id, :token)");
		$sql->bindParam(':acc_id', $acc_id);
		$sql->bindParam(':token', $token);
		$sql->execute();

		$sql = $this->connection->prepare("INSERT INTO `user_settings` (`acc_id`, `profile_pic`) VALUES (:acc_id, :profile_pic)");
		$sql->bindParam(':acc_id', $acc_id);
		$profile_pic = "imgs/resources/default_pumpkin.png";
		$sql->bindParam(':profile_pic', $profile_pic);
		$sql->execute();

		return ($this->send_verification_email($username, $email, $token));
	}
	
	private function send_verification_email($username, $email, $token) {
		$subject = "Email verification";
		// $url = "http://" . gethostbyname(gethostname()) . ":" . $_SERVER['SERVER_PORT'] . "/camagru/verify.php?user=" . $username . "&token=" . hash('whirlpool', $token);
		$url = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/camagru/verify.php?user=" . $username . "&token=" . hash('whirlpool', $token);

		$message = "<html><body><p>Welcome to the CAMAGRU community, <strong>" . $username . "</strong></p><br><br>";
		$message .= "Before continuing on to make amazing pictures, please verify your email address by <a href='" . $url . "'>clicking here.</a></body></html>";

		return (parent::send_mail($email, $subject, $message));
	}

	public function __destruct() {
		$this->connection = NULL;
	}
}

class USER extends SERVER
{
	public $id;
	public $username;

	public function __construct($column, $value) {
		parent::__construct();
		if ($column == "name") {
			$sql = $this->connection->prepare("SELECT `id`, `name` FROM `accounts` WHERE `name` = :value");
		} elseif ($column == "id") {
			$sql = $this->connection->prepare("SELECT `id`, `name` FROM `accounts` WHERE `id` = :value");
		}
		$sql->bindParam(":value", $value);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		$this->id = $account["id"];
		$this->username = $account["name"];
	}

	public function fetch_account() {
		$sql = $this->connection->prepare("SELECT * FROM `accounts` WHERE `id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		return ($account);
	}

	public function get_acc_settings() {
		$sql = $this->connection->prepare("SELECT * FROM `user_settings` WHERE `id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		return ($account);
	}

	public function is_valid() {
		$sql = $this->connection->prepare("SELECT `authenticate`.`valid` FROM `authenticate` INNER JOIN `accounts` ON `authenticate`.`acc_id` = `accounts`.`id` WHERE `accounts`.`id`=:id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		return ($account["valid"]);
	}

	public function correct_password($password) {
		$sql = $this->connection->prepare("SELECT `pw` FROM `accounts` WHERE `id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		if (hash("whirlpool", $password) == $account["pw"]) {
			return (1);
		} else {
			return (0);
		}
	}

	public function token_matches($token) {
		$sql = $this->connection->prepare("SELECT `authenticate`.`token` FROM `accounts` INNER JOIN `authenticate` ON `accounts`.`id` = `authenticate`.`acc_id` WHERE `accounts`.`id`=:id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		if (hash("whirlpool", $account["token"]) == $token) {
			return (1);
		} else {
			return (0);
		}
	}

	public function validate_account() {
		$sql = $this->connection->prepare("UPDATE `authenticate` INNER JOIN `accounts` ON `accounts`.`id` = `authenticate`.`acc_id` SET `authenticate`.`valid` = 1 WHERE `accounts`.`id` = :id");
		$sql->bindParam(':id', $this->id);
		$sql->execute();
	}

	public function __destruct() {
		$this->connection = NULL;
	}

	public function get_last_pic_id() {
		$sql = $this->connection->prepare("SELECT COUNT(*) AS `total` FROM `pictures` WHERE `acc_id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		return ($result['total']);
	}

	public function upload_picture($picture_name) {
		$sql = $this->connection->prepare("INSERT INTO `pictures`(`addr`, `acc_id`) VALUES (:picture_name, :id)");
		$sql->bindParam(":id", $this->id);
		$sql->bindParam(":picture_name", $picture_name);
		$sql->execute();
	}

	public function liked_pictures() {
		$sql = $this->connection->prepare("SELECT `likes`.`pic_id` FROM `likes` WHERE `likes`.`acc_id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$results = array();
		while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
			array_push($results, $result["pic_id"]);
		}
		return ($results);
	}

	public function like_update($img_id, $action) {
		if ($action == "add") {
			$sql = $this->connection->prepare("INSERT INTO `likes`(`acc_id`, `pic_id`) VALUES (:id, :img)");
			$sql->bindParam(":id", $this->id);
			$sql->bindParam(":img", $img_id);
			$sql->execute();
			$this->send_like_email($img_id, $this->id);
		} else if ($action == "subtract") {
			$sql = $this->connection->prepare("DELETE FROM `likes` WHERE `likes`.`acc_id` = :id AND `likes`.`pic_id` = :img");
			$sql->bindParam(":id", $this->id);
			$sql->bindParam(":img", $img_id);
			$sql->execute();
		}
	}

	public function save_comment($img_id, $comment) {
		$sql = $this->connection->prepare("INSERT INTO `comments`(`acc_id`, `pic_id`, `message`) VALUES (:id, :pic_id, :comment)");
		$sql->bindParam(":id", $this->id);
		$sql->bindParam(":pic_id", $img_id);
		$sql->bindParam(":comment", $comment);
		$sql->execute();
		$this->send_comment_email($img_id, $this->id);
	}

	public function send_comment_email($img_id, $user_id) {
		$sql = $this->connection->prepare("SELECT `pictures`.`acc_id`, `accounts`.`name`, `accounts`.`email`, `user_settings`.`email_comments` FROM `pictures` INNER JOIN `accounts` ON `pictures`.`acc_id` = `accounts`.`id` INNER JOIN `user_settings` ON `user_settings`.`acc_id` = `accounts`.`id` WHERE `pictures`.`id` = :img_id");
		$sql->bindParam(":img_id", $img_id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$img_users_id = $result["acc_id"];
		$img_users_name = $result["name"];
		$comment_status = $result["email_comments"];
		if ($img_users_id != $user_id && $comment_status == 1) {
			$email = $result["email"];
			$subject = "New comment";
			$message = "Hey ".$img_users_name.", one of your pictures just got commented on by ".$this->username;
			parent::send_mail($email, $subject, $message);
		}
	}

	public function send_like_email($img_id, $user_id) {
		$sql = $this->connection->prepare("SELECT `pictures`.`acc_id`, `accounts`.`name`, `accounts`.`email`, `user_settings`.`email_likes` FROM `pictures` INNER JOIN `accounts` ON `pictures`.`acc_id` = `accounts`.`id` INNER JOIN `user_settings` ON `user_settings`.`acc_id` = `accounts`.`id` WHERE `pictures`.`id` = :img_id");
		$sql->bindParam(":img_id", $img_id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$img_users_id = $result["acc_id"];
		$img_users_name = $result["name"];
		$like_status = $result["email_likes"];
		if ($img_users_id != $user_id && $like_status == 1) {
			$email = $result["email"];
			$subject = "New like";
			$message = "Hey ".$img_users_name.", ".$this->username." just liked one of your pictures!";
			parent::send_mail($email, $subject, $message);
		}
	}

	public function delete_picture($img_id) {
		$sql = $this->connection->prepare("DELETE FROM `pictures` WHERE `id` = :img_id");
		$sql->bindParam(":img_id", $img_id);
		$sql->execute();
		$sql = $this->connection->prepare("DELETE FROM `comments` WHERE `pic_id` = :img_id");
		$sql->bindParam(":img_id", $img_id);
		$sql->execute();
		$sql = $this->connection->prepare("DELETE FROM `likes` WHERE `pic_id` = :img_id");
		$sql->bindParam(":img_id", $img_id);
		$sql->execute();
	}

	public function fetch_my_imgs() {
		$sql = $this->connection->prepare("
		SELECT `pictures`.`addr`, `pictures`.`creation_date`, `pictures`.`id`, `accounts`.`name`, COMMENTS.`comments`, LIKES.`likes`
			FROM `pictures`
				INNER JOIN `accounts`
					ON `accounts`.`id` = `pictures`.`acc_id`
				LEFT JOIN 
					(SELECT `pic_id`, COUNT(*) AS `comments` FROM `comments` GROUP BY `comments`.`pic_id`) AS COMMENTS
						ON COMMENTS.`pic_id` = `pictures`.`id`
				LEFT JOIN 
					(SELECT `pic_id`, COUNT(*) AS `likes` FROM `likes` GROUP BY `likes`.`pic_id`) AS LIKES
						ON LIKES.`pic_id` = `pictures`.`id`
			WHERE `pictures`.`acc_id` = :id
			ORDER BY `pictures`.`creation_date`
			DESC
		");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$pictures = array();
		while ($picture = $sql->fetch(PDO::FETCH_ASSOC)) {
			array_push($pictures, $picture);
		}
		return ($pictures);
	}

	public function get_account_details() {
		$sql = $this->connection->prepare("SELECT `accounts`.`name`, `accounts`.`email`, `user_settings`.`email_comments` AS 'commentable', `user_settings`.`email_likes` AS 'likeable' FROM `accounts` INNER JOIN `user_settings` ON `accounts`.`id` = `user_settings`.`acc_id` WHERE `accounts`.`id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$details = $sql->fetch(PDO::FETCH_ASSOC);
		return ($details);
	}

	public function is_new_name_taken($name) {
		$sql = $this->connection->prepare("SELECT COUNT(*) AS 'rows' FROM `accounts` WHERE `name` = :username AND `id` != :id");
		$sql->bindParam(":username", $name);
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$results = $sql->fetch(PDO::FETCH_ASSOC);
		return ($results["rows"]);
	}

	public function is_new_email_taken($email) {
		$sql = $this->connection->prepare("SELECT COUNT(*) AS 'rows' FROM `accounts` WHERE `email` = :email AND `id` != :id");
		$sql->bindParam(":email", $email);
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$results = $sql->fetch(PDO::FETCH_ASSOC);
		return ($results["rows"]);
	}

	public function update_details($name, $comment, $like) {
		$sql = $this->connection->prepare("UPDATE `accounts` SET `name`=:username WHERE `id` = :id");
		$sql->bindParam(":username", $name);
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$sql = $this->connection->prepare("UPDATE `user_settings` SET `email_comments`=:email_c, `email_likes`=:email_l WHERE `id` = :id");
		$sql->bindParam(":email_c", $comment);
		$sql->bindParam(":email_l", $like);
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$this->username = $name;
	}

	public function save_new_PW($pw, $token) {
		$sql = $this->connection->prepare("SELECT `token` FROM `authenticate` WHERE `acc_id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$dbtoken = $result["token"];

		if (hash("whirlpool", $dbtoken) == $token) {
			$hashed_pw = hash("whirlpool", $pw);
			$sql = $this->connection->prepare("UPDATE `accounts` SET `pw`=:pw WHERE `id` = :id");
			$sql->bindParam(":pw", $hashed_pw);
			$sql->bindParam(":id", $this->id);
			$sql->execute();
			return (1);
		} else {
			return (0);
		}
	}

	public function update_email($email) {
		$sql = $this->connection->prepare("SELECT `token` FROM `authenticate` WHERE `acc_id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$token = $result["token"];
		$sql = $this->connection->prepare("UPDATE `authenticate` SET `valid`='0' WHERE `acc_id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$sql = $this->connection->prepare("UPDATE `accounts` SET `email`=:email WHERE `id` = :id");
		$sql->bindParam(":email", $email);
		$sql->bindParam(":id", $this->id);
		$sql->execute();

		$subject = "Email verification";
		$url = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/camagru/verify.php?user=" . $this->username . "&token=" . hash('whirlpool', $token);
		$message = "<html><body><p>Hey <strong>" . $this->username . "</strong></p><br><br>";
		$message .= "To verify your new email address just <a href='" . $url . "'>click here.</a></body></html>";
		parent::send_mail($email, $subject, $message);
	}

	public function send_PW_reset_email() {
		$sql = $this->connection->prepare("SELECT `authenticate`.`token`, `accounts`.`email` FROM `accounts` INNER JOIN `authenticate` ON `accounts`.`id` = `authenticate`.`acc_id` WHERE `accounts`.`id` = :id");
		$sql->bindParam(":id", $this->id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$token = $result["token"];
		$email = $result["email"];

		$subject = "Password reset";
		$url = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/camagru/reset_pw.php?user=" . $this->username . "&token=" . hash('whirlpool', $token);
		$message = "<html><body><p>Hey <strong>" . $this->username . "</strong></p><br><br>";
		$message .= "To reset your password <a href='" . $url . "'>click here.</a></body></html>";
		parent::send_mail($email, $subject, $message);
	}

}

class GALLERY extends SERVER
{
	public function fetch_all_imgs() {
		$sql = $this->connection->prepare("
		SELECT `pictures`.`addr`, `pictures`.`creation_date`, `pictures`.`id`, `accounts`.`name`, COMMENTS.`comments`, LIKES.`likes`
			FROM `pictures`
				INNER JOIN `accounts`
					ON `accounts`.`id` = `pictures`.`acc_id`
				LEFT JOIN 
					(SELECT `pic_id`, COUNT(*) AS `comments` FROM `comments` GROUP BY `comments`.`pic_id`) AS COMMENTS
						ON COMMENTS.`pic_id` = `pictures`.`id`
				LEFT JOIN 
					(SELECT `pic_id`, COUNT(*) AS `likes` FROM `likes` GROUP BY `likes`.`pic_id`) AS LIKES
						ON LIKES.`pic_id` = `pictures`.`id`
			ORDER BY `pictures`.`creation_date`
			DESC
		");
		$sql->execute();
		$pictures = array();
		while ($picture = $sql->fetch(PDO::FETCH_ASSOC)) {
			array_push($pictures, $picture);
		}
		return ($pictures);
	}

	public function fetch_comments($img_id) {
		$sql = $this->connection->prepare('SELECT `pictures`.`addr`, `pictures`.`acc_id` AS "img_acc", `pictures`.`creation_date` AS "img_time", `comments`.`acc_id` AS "msg_acc", `comments`.`creation_date` AS "msg_time", `comments`.`message` FROM `comments` INNER JOIN `pictures` ON `comments`.`pic_id` = `pictures`.`id` WHERE `pictures`.`id` = :img_id');
		$sql->bindParam(':img_id', $img_id);
		$sql->execute();
		$pictures = $sql->fetch(PDO::FETCH_ASSOC);
		return ($pictures);
	}

	public function img_likes($img_id) {
		$sql = $this->connection->prepare('SELECT `likes`.`pic_id`, `accounts`.`name` FROM `likes` INNER JOIN `accounts` ON `accounts`.`id` = `likes`.`acc_id` WHERE `likes`.`pic_id` = :img_id');
		$sql->bindParam(':img_id', $img_id);
		$sql->execute();
		$likes = $sql->fetch(PDO::FETCH_ASSOC);
		return ($likes);
	}

	public function get_img_comments($img_id) {
		$sql = $this->connection->prepare('SELECT `comments`.`message`, `accounts`.`name` FROM `comments` INNER JOIN `accounts` ON `accounts`.`id` = `comments`.`acc_id` WHERE `comments`.`pic_id` = :img_id ORDER BY `comments`.`creation_date` ASC');
		$sql->bindParam(':img_id', $img_id);
		$sql->execute();
		$comments = array();
		while ($comment = $sql->fetch(PDO::FETCH_ASSOC)) {
			array_push($comments, $comment);
		}
		return ($comments);
	}
}

?>
