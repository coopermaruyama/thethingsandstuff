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
	if (preg_match("/^item/", $column_name) && isset($rr_id) && $rr_id != "" ) { // need to update an item
		if ( preg_match("/^item[0-9]+_price/", $column_name) ) {
			$item_column_name = "price";
			$column_name = preg_replace("/_price/", "", $column_name);
		} else {
			$item_column_name = "item";
		}
		$item_number = preg_replace("/item/", "", $column_name);
		// $item_number = intval($item_number) - 1;
		$the_query = "SELECT * FROM `revenue_report`.`revenue_record_items` WHERE `revenue_record_id` = $row_id AND `item_number` = $item_number";
		
		$query = $conn->prepare($the_query);
		$query->execute();
		$result = $query->fetch();
		if (!$result) { //no item found, make a new one
			$the_query = "INSERT INTO `revenue_report`.`revenue_record_items` (`revenue_record_id`, `item_number`, `$item_column_name`) VALUES ($row_id, $item_number, '$new_value')";
			// echo $the_query;
			$query = $conn->prepare( $the_query );
		} else {
			$item_row_id = $result["id"];
			$the_query = "UPDATE `revenue_report`.`revenue_record_items` SET `$item_column_name`='$new_value' WHERE id = $rr_id";
			$query = $conn->prepare( $the_query );
			// echo $the_query;
		}
		if ($query->execute()) {
			echo "success";
		} else {
			print_r($query->errorInfo());
		}
	} else {
		$query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET `$column_name`='$new_value' WHERE `revenue_records`.`id`=$row_id");
		if ($query->execute()) {
			echo "success";
		} else {
			print_r($query->errorInfo());
		}
	}

} else if ( $action =="destroy" && isset($row_id) ) {
	$query = $conn->prepare("UPDATE `revenue_report`.`revenue_records` SET `deleted`='1' WHERE `revenue_records`.`id`=$row_id");
	$query->execute();
	echo "success";
} else if ( $action == "new_record" && isset($record_type, $total_paid, $customer_name) ) {
	$query = $conn->prepare("INSERT INTO `revenue_report`.`revenue_records` (`id`, `magento_order_id`,`manual_entry`, `order_date`, `order_type`, `total_paid`,`locked`, `deleted`,`customer_name`, `service_date`) VALUES (NULL, NULL, '1', CURRENT_TIMESTAMP, '$record_type', $total_paid, '0', '0', '$customer_name', CURRENT_TIMESTAMP)");
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