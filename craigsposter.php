<?php  
/**
 * @author Cooper Maruyama
 * API for getting image gallery of products with a certain SKU
 *
 **/
$secret_token = 'PTOI1lDGTnUW3HLpToUn';
$host = 'localhost';
$username = 'root';
$password = '3120MacT!';
$link =  mysql_connect($host, $username, $password);

# set GET varaibles
if (isset($_GET['token'])) {$token = $_GET['token'];}
if (isset($_GET['sku'])) {$sku = $_GET['sku'];}


if ($token == $secret_token) {
	mysql_select_db('magento');
	
	## get the Magento ID using SKU
	$result = mysql_query("SELECT entity_id FROM `catalog_product_entity` WHERE `sku` = $sku LIMIT 1");
	if (!$result) {
		die("Invalid query: " . mysql_error());
	}
	$row = mysql_fetch_assoc($result);
	$magento_id = $row['entity_id'];


	## using the magento id, fetch the urls for the images
	$images_result = mysql_query("SELECT * FROM `catalog_product_entity_media_gallery` WHERE `entity_id` = $magento_id");
	$total_rows = mysql_num_rows($images_result);
	if ($images_result) {
		$output_string = "";
		$i = 1;
		while ($row = mysql_fetch_array($images_result)) {
			$position_result = mysql_query("SELECT postition FROM `catalog_product_entity_media_gallery` WHERE `value_id` = " . $row['value_id']);
			$position_row = mysql_fetch_row($position_result);
			$position = intVal($position_row[0]); 

			if ($position == 1) {
				if ($i < $total_rows) {
					$output_string = $row['value'] . "," . $output_string;
				} else {
					$output_string = $row['value'];
				}
			} else {
				$output_string .= $row['value'];
				if ($i < $total_rows) {
					$output_string .= ",";
				}
			}
			$i++;
		}
		echo $output_string;
	}
}


 ?>