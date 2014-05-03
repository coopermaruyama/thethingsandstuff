<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/

extract($_POST);
$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($action == "unlock_row" && isset($row_id)) {
    $query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET locked=0 WHERE `revenue_records`.`id`=$row_id");
    $query->execute();
    echo "success";
} else if ($action == "lock_row" && isset($row_id)) {
    $query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET locked=1 WHERE `revenue_records`.`id`=$row_id");
    $query->execute();
    echo "success";
} else if ($action == "update_column" && isset($row_id) && isset($column_name) && isset($new_value)) {
	$query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET `$column_name`='$new_value' WHERE `revenue_records`.`id`=$row_id");
	$query->execute();
	echo "success";

} else if ( $action =="destroy" && isset($row_id) ) {
	$query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET `deleted`='1' WHERE `revenue_records`.`id`=$row_id");
	$query->execute();
	echo "success";
} else if ( $action == "new_record" && isset($record_type) && isset($customer_name) && isset($total_paid) ) {
	$query = $conn->prepare("INSERT INTO `revenue_report`.`revenue_records` (`id`, `magento_order_id`,`manual_entry`, `order_date`, `order_type`, `total_paid`,`locked`, `deleted`,`customer_name`) VALUES (NULL, NULL, '1', CURRENT_TIMESTAMP, '$record_type', $total_paid, '0', '0', '$customer_name')");
	$query->execute();
	$row_id = intval($conn->lastInsertId());

	$query = $conn->prepare("SELECT * FROM `revenue_report`.`revenue_records` WHERE `revenue_records`.`id`=$row_id LIMIT 1");
	$query->execute();
	$row = $query->fetch();

	$result = array(
		"result" => "success",
		"record" => $row
	);
	print_r(json_encode($result));
}

?>