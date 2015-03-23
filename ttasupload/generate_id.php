<?php 
/**
 *
 *  This script generates a unique ID to use as a magento SKU
 *
 **/


// uncomment for debugging 
// ini_set('display_errors',1);
// error_reporting(E_ALL);

$token = "52eb285a49e57";

if ($_GET['token'] == $token) { //require a token!
	// PDO Connection to MySQL
	$conn = new PDO('mysql:host=205.186.146.64;dbname=remote', 'root', '3120MacT!');

	//Insert row
	$query = $conn->prepare("INSERT INTO  `remote`.`skus` (`id`)VALUES (NULL);");
	$query->execute();

	//Get last inserted row
	$query = $conn->prepare("SELECT ID from `remote`.`skus` ORDER BY ID DESC LIMIT 1");
	$query->execute();
	$result = $query->fetch();
	echo $result['ID'];
}

 ?>