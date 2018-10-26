<?php

class SERVER
{
	protected $servername = "localhost";
	protected $dbname = "camagru";
	protected $username = "root";
	protected $password = "admin1";

	public function __construct()
	{
		
		try
		{
			$conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
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

class DB extends SERVER
{
	function connect()
	{
		return new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
	}
}

?>
