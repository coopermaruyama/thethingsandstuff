<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
print_r(base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/var/uploadify/1.png")));
 ?>