<?php

class SERVER
{
	protected $servername = "localhost";
	protected $dbname = "camagru";
	protected $username = "root";
	protected $password = "admin1";

	public function __construct() {
		
		try	{
			$conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)	{
			die("Connection failed: " . $e->getMessage());
		}

		$conn->query("CREATE DATABASE IF NOT EXISTS `camagru`");
		$conn = NULL;
		$conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
		
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
			`pic_id` INT NOT NULL,
			`creation_date` DATETIME DEFAULT NOW());";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `comments` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`acc_id` INT NOT NULL,
			`pic_id` INT NOT NULL,
			`creation_date` DATETIME DEFAULT NOW());";
		$conn->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `superposable` (
			PRIMARY KEY (id),
			`id` INT AUTO_INCREMENT,
			`addr` VARCHAR(100) NOT NULL);";
		$conn->query($sql);
	}
}

class USER extends SERVER
{

	public $connection;

	public function __construct() {
		parent::__construct();
		$this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
	}

	public function account_exists($name) {
		$sql = $this->connection->prepare("SELECT * FROM `accounts` WHERE `name` = :name");
		$sql->bindParam(":name", $name);
		$sql->execute();
		if ($sql->rowCount()) {
			return (1);
		} else {
			return (0);
		}
	}

	public function is_valid($name) {
		$sql = $this->connection->prepare("SELECT `authenticate`.`valid` FROM `authenticate` INNER JOIN `accounts` ON `authenticate`.`acc_id` = `accounts`.`id` WHERE `accounts`.`name`=:name");
		$sql->bindParam(":name", $name);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		return ($account["valid"]);
	}

	public function correct_password($name, $password) {
		$sql = $this->connection->prepare("SELECT `pw` FROM `accounts` WHERE `name` = :name");
		$sql->bindParam(":name", $name);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		if (hash("whirlpool", $password) == $account["pw"]) {
			return (1);
		} else {
			return (0);
		}
	}

	public function token_matches($name, $token) {
		$sql = $this->connection->prepare("SELECT `accounts`.`name`, `authenticate`.`token` FROM `accounts` INNER JOIN `authenticate` ON `accounts`.`id` = `authenticate`.`acc_id` WHERE `name`=:name");
		$sql->bindParam(":name", $name);
		$sql->execute();
		$account = $sql->fetch(PDO::FETCH_ASSOC);
		if (hash("whirlpool", $account["token"]) == $token) {
			return (1);
		} else {
			return (0);
		}
	}

	public function validate_account($name) {
		$sql = $this->connection->prepare("UPDATE `authenticate` INNER JOIN `accounts` ON `accounts`.`id` = `authenticate`.`acc_id` SET `authenticate`.`valid` = 1 WHERE `accounts`.`name` = :user");
		$sql->bindParam(':user', $name);
		$sql->execute();
	}

	public function __destruct() {
		$this->connection = NULL;
	}
}

?>
