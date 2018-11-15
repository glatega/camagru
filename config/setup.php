<?php

class SETUP
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

}

?>
