<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require("./vendor/autoload.php");
require("./database.php");

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
		$mail->SetFrom("camagrurmdaba@gmail.com");
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
