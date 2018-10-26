<?php

include("./connect.php");

$servername = "localhost";
$username = "root";
$password = "admin1";

// Create connection
// $conn = mysqli_connect($servername, $username, $password);
$conn = new PDO("mysql:host=$servername", $username, $password);

// Create database if it hasn't been already
$sql = "CREATE DATABASE IF NOT EXISTS `camagru`";
$conn->query($sql);

// Refresh connection
$conn = new PDO("mysql:host=$servername", $username, $password);

// Category Table
$sql = "CREATE TABLE IF NOT EXISTS `accounts` (
	PRIMARY KEY (id),
	`id` INT AUTO_INCREMENT,
	`name` VARCHAR(42) NOT NULL,
	`pw` VARCHAR(64) NOT NULL,
	`creation_date` DATETIME DEFAULT NOW());";
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

?>
