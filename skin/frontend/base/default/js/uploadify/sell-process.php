<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
extract($_POST);
$itemInfo = isset($itemInfo) ? $itemInfo : "N/A";
$timing = isset($timing) ? $timing : "N/A";
$location = isset($location) ? $location : "N/A";
$contactInfo = isset($contactInfo) ? $contactInfo : "N/A";
$renamed_file = isset($renamed_file) ? $renamed_file : "N/A";

$body =  "<strong>Item info:</strong> ". $itemInfo . "<br><br>
                        <strong>Timing: </strong>" . $timing . "<br><br>
                        <strong>Location: </strong>" . $location . "<br><br>
                        <strong>Contact Info: </strong>" . $contactInfo . "<br><br>
                        <strong>Uploaded Images: </strong>";
if (isset($_POST['files'])) {
 foreach($_POST['files'] as $file) {
                $renamed_file = str_replace(" ", "_", $file);
                $body .= "<a href='http://thethingsandstuff.com/var/uploadify/". urlencode($renamed_file) . "'>".$renamed_file."</a><br>";
        }
}
        $header = 'MIME-Version: 1.0' . "\r\n";
$header .= 'From: The Things and Stuff <contact@thethingsandstuff.com>' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  if (mail("thethingsandstuff1@gmail.com", "New Sell Reqeust via The Things And Stuff Website", $body, $header)) {
                header("Location: http://thethingsandstuff.com/sell-thanks/");
        }
        else {
                die("Error: please email us at contact@thethingsandstuff.com");
        }
 ?>
