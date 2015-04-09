<?php
require_once "Mail.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

// build message
extract($_POST);
$body =  "Item info: ". $itemInfo . "\r\n
                        Timing: " . $timing . "\r\n
                        Location: " . $location . "\r\n
                        Contact Info: " . $contactInfo . "\r\n
                        Uploaded Images: ";
if (isset($_POST['files'])) {
 foreach($_POST['files'] as $file) {
                $renamed_file = str_replace(" ", "_", $file);
                $body .= "<a href='http://thethingsandstuff.com/var/uploadify/". urlencode($renamed_file) . "'>".$renamed_file."</a><br>";
        }
}


// Setup email
$header = 'MIME-Version: 1.0' . "\r\n";
$header .= 'From: The Things and Stuff <contact@thethingsandstuff.com>' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$from = "The Things and Stuff Mail <contact@thethingsandstuff.com>";
$to = "Troy pasulka <contact@thethingsandstuff.com>";
$subject = "New Sell Reqeust via The Things And Stuff Website";

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


// do send
$mail = $smtp->send($to, $headers, $body);

// catch error
if (PEAR::isError($mail)) {
  die("<p>Error: please email us at contact@thethingsandstuff.com</p><p>" . $mail->getMessage() . "</p>");
 } else {
  header("Location: http://thethingsandstuff.com/sell-thanks/");
 }

 ?>
