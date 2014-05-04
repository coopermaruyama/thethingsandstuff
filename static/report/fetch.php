<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/

extract($_GET);
$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($per_page)) $per_page = 30;
if (!isset($page)) $page = 1;
if (!isset($order)) $order = 'ASC';
if (!isset($order_by)) $order_by = 'id';
if (!isset($filter)) $filter = '';
$pager = (intval($page) - 1) * intval($per_page);

$query = $conn->prepare("SELECT * FROM `revenue_report`.`revenue_records` WHERE `deleted`=0 $filter ORDER BY `revenue_records`.$order_by $order LIMIT $pager, $per_page");
$query->execute();
$result = $query->fetchAll();
foreach ($result as $key => $field) {
    $the_id = intval($field['id']);
    // Get Items
    $query = $conn->prepare("SELECT * FROM `revenue_report`.`revenue_record_items` WHERE `revenue_record_id`=$the_id");
    $query->execute();
    $items_result = $query->fetchAll();
    $result[$key]['items'] = $items_result;

    // Get Tax
    $city =$field['shipping_city'];
    $query = $conn->prepare("SELECT `rate` FROM `revenue_report`.`city_tax` WHERE `city_tax`.`city` LIKE '$city' LIMIT 1");
    $query->execute();
    $tax_result = $query->fetch();
    $tax_rate = floatval($tax_result["rate"])/100;
    $price = floatval($field["total_paid"]);
    $result[$key]['tax'] = $price * $tax_rate;
}
$headers = array('Content-Type' => 'application/json');
// echo $result[0]['customer_name'];

echo json_encode($result);

?>