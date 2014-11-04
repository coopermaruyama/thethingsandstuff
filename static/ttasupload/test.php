<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (mail("cm@convertify.io", "hello", "asdasd")) {
	echo "sent";
 } else {
 	echo "not sent";
 }

 ?>
