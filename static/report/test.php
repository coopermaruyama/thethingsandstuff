<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/

extract($_GET);
$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$city ="Torrance";
$query = $conn->prepare("SELECT `rate` FROM `revenue_report`.`city_tax` WHERE `city_tax`.`city` LIKE '$city' LIMIT 1");
$query->execute();
$tax_result = $query->fetch();
$tax_rate = floatval($tax_result["rate"])/100;
$price = floatval($field["total_paid"]);
echo $price * $tax_rate;
// $result[$key]['tax'] = 

// echo json_encode($result);

?>