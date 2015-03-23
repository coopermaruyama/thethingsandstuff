<?php
ini_set('display_errors',true);
error_reporting(E_ALL);


 $to = 'cm@convertify.io'; $subject = 'Test email using PHP'; $message = 'This is a test email message'; $headers = 'From: webmaster@example.com' . "\r\n" . 'Reply-To: webmaster@example.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion(); mail($to, $subject, $message, $headers, '-fwebmaster@example.com');

?> 
