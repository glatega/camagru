<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require("./vendor/autoload.php");

class SERVER
{
	protected $servername = "localhost";
	protected $dbname = "camagru";
	protected $admin_username = "root";
	protected $admin_password = "admin1";

	public function __construct() {
		
		try	{
			$conn = new PDO("mysql:host=$this->servername", $this->admin_username, $this->admin_password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)	{
			die("Connection failed: " . $e->getMessage());
		}

		$conn->query("CREATE DATABASE IF NOT EXISTS `camagru`");
		$conn = NULL;
		$conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->admin_username, $this->admin_password);
		
		$sql = "CREATE TABLE IF NOT EXISTS `accounts` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`name` VARCHAR(32) NOT NULL,
			`pw` VARCHAR(128) NOT NULL,
			`email` VARCHAR(64) NOT NULL,
			`creation_date` DATETIME DEFAULT NOW());";
		$conn->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `authenticate` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`acc_id` INT NOT NULL,
			`valid` INT DEFAULT 0,
			`token` int NOT NULL);";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `pictures` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`addr` VARCHAR(100) NOT NULL,
			`acc_id` INT NOT NULL,
			`creation_date` DATETIME DEFAULT NOW());";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `likes` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`acc_id` INT NOT NULL,
			`pic_id` INT NOT NULL ;";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `comments` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`acc_id` INT NOT NULL,
			`pic_id` INT NOT NULL,
			`message` VARCHAR(500) NOT NULL,
			`creation_date` DATETIME DEFAULT NOW());";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `masks` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`addr` VARCHAR(100) NOT NULL);";
		$conn->query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `user_settings` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`acc_id` INT NOT NULL,
			`profile_pic` VARCHAR(100) NOT NULL,
			`email_likes` INT DEFAULT 1,
			`email_comments` INT DEFAULT 1);";
		$conn->query($sql);
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
}

class CONNECTION extends SERVER
{
	public $connection;

	public function __construct() {
		parent::__construct();
		$this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->admin_username, $this->admin_password);
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
		$profile_pic = "imgs/default_pumpkin.png";
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

	public $connection;
	public $id;
	public $username;

	public function __construct($column, $value) {
		parent::__construct();
		$this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->admin_username, $this->admin_password);
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
}

class GALLERY extends SERVER
{
	public $connection;

	public function __construct() {
		parent::__construct();
		$this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->admin_username, $this->admin_password);
	}

	public function fetch_all_imgs($username) {
		$sql = $this->connection->prepare("SELECT `pictures`.`addr`, `pictures`.`creation_date`, `pictures`.`id` FROM `pictures` INNER JOIN `accounts` ON `accounts`.`id` = `pictures`.`acc_id` WHERE `accounts`.`name` = :user");
		$sql->bindParam(':user', $username);
		$sql->execute();
		$pictures = $sql->fetch(PDO::FETCH_ASSOC);
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
}

?>
