<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/

extract($_POST);
$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($action == "unlock" && isset($row_id)) {
    $query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET locked=0 WHERE `revenue_records`.`id`=$row_id");
    $query->execute();
    echo "success";
} else if ($action == "lock" && isset($row_id)) {
    $query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET locked=1 WHERE `revenue_records`.`id`=$row_id");
    $query->execute();
    echo "success";
}

?>