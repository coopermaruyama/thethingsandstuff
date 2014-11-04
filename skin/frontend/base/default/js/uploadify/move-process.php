<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
extract($_POST);
$moveDate = isset($moveDate) ? $moveDate : "N/A";
$contactInfo = isset($contactInfo) ? $contactInfo : "N/A";
$originAddress = isset($originAddress) ? $originAddress : "N/A";
$destination = isset($destination) ? $destination : "N/A";
$numberofRooms = isset($numberofRooms) ? $numberofRooms : "N/A";
$ApproximateValue = isset($ApproximateValue) ? $ApproximateValue : "N/A";
$mostExpensive = isset($mostExpensive) ? $mostExpensive : "N/A";
$notes = isset($notes) ? $notes : "N/A";
$body =  "<strong>Move date:</strong> ". $moveDate . "<br><br>
                        <strong>Contact Info: </strong>" . $contactInfo . "<br><br>
                        <strong>Origin Address: </strong>" . $originAddress . "<br><br>
                        <strong>Destination Address: </strong>" . $destination . "<br><br>
                        <strong>Number of Rooms: </strong>" . $numberofRooms . "<br><br>
                        <strong>Approximate total value of items: </strong>" . $ApproximateValue . "<br><br>
                        <strong>Approximate value of most expensive piece: </strong>" . $mostExpensive . "<br><br>
                        <strong>Notes: </strong>" . $notes . "<br><br>";

        $header = 'MIME-Version: 1.0' . "\r\n";
$header .= 'From: The Things and Stuff <contact@thethingsandstuff.com>' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  if (mail("thethingsandstuff1@gmail.com", "New Sell Reqeust via The Things And Stuff Website", $body, $header)) {
                header("Location: http://thethingsandstuff.com/sell-thanks/");
        }
        else {
            $die_body = "Error: please email us at contact@thethingsandstuff.com<br><br>";
            $die_body .= "moveDate = $moveDate<br><br> contactInfo = $contactInfo<br><br> originAddress = $originAddress<br><br> destination = $destination<br><br> numberofRooms = $numberofRooms<br><br> ApproximateValue = $ApproximateValue<br><br> mostExpensive = $mostExpensive<br><br> notes = $notes<br><br>";
            die($die_body);
        }
 ?>
