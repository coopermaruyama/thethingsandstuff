<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/

extract($_POST);
$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




$the_id = $record_id;

// Get Record
$query = $conn->prepare("SELECT * FROM `revenue_report`.`revenue_records` WHERE `id`=$the_id");
$query->execute();
$record = $query->fetch();

// Get Items
$query = $conn->prepare("SELECT * FROM `revenue_report`.`revenue_record_items` WHERE `revenue_record_id`=$the_id");
$query->execute();
$items_result = $query->fetchAll();
$record['items'] = $items_result;

$total_paid = 0;
foreach ($record["items"] as $key => $field) {
   $total_paid = $total_paid + floatval($field["price"]);
}

// Get Tax
$city = $record['shipping_city'];
$query = $conn->prepare("SELECT `rate` FROM `revenue_report`.`city_tax` WHERE `city_tax`.`city` LIKE '$city' LIMIT 1");
$query->execute();
$tax_result = $query->fetch();
$tax_rate = floatval($tax_result["rate"])/100;
$price = floatval($total_paid);
$record['tax'] = $price * $tax_rate;

$headers = array('Content-Type' => 'application/json');
// echo $result[0]['customer_name'];

echo json_encode($record);

?>