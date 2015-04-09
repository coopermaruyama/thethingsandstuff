<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "Mail.php";

$from = "The Things and Stuff Mail <contact@thethingsandstuff.com>";
$to = "Troy pasulka <troypasulka@gmail.com>";
$subject = "Hi!";
$body = "Testing";

$host = "smtp.gmail.com";
$port = "587";
$username = "contact@thethingsandstuff.com";
$password = "pmanage1!";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }

//mail("troypasulka@gmail.com", "New Sell Reqeust via The Things And Stuff Website", "hello"); ?>
