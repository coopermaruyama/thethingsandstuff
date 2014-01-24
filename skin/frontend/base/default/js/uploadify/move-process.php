<?php 
extract($_POST);
$body =  "<strong>Move date:</strong> ". $moveDate . "<br><br>
                        <strong>Origin Address: </strong>" . $originAddress . "<br><br>
                        <strong>Destination Address: </strong>" . $destination . "<br><br>
                        <strong>Number of Rooms: </strong>" . $numberofRooms . "<br><br>
                        <strong>Approximate total value of items: </strong>" . $ApproximateValue . "<br><br>
                        <strong>Approximate value of most expensive piece: </strong>" . $mostExpensive . "<br><br>
                        <strong>Notes: </strong>" . $notes . "<br><br>";

        $header = 'MIME-Version: 1.0' . "\r\n";
$header .= 'From: The Things and Stuff <contact@thethingsandstuff.com>' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  if (mail("contact@thethingsandstuff.com", "New Sell Reqeust via The Things And Stuff Website", $body, $header)) {
                header("Location: http://thethingsandstuff.com/sell-thanks/");
        }
        else {
                die("Error: please email us at contact@thethingsandstuff.com");
        }
 ?>
