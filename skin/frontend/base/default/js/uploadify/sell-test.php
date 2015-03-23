<?php 
error_reporting(E_ALL);
ini_set('display_errors',1);

extract($_POST);
$body =  "<strong>Item info:</strong> ". $itemInfo . "<br><br>
                        <strong>Timing: </strong>" . $timing . "<br><br>
                        <strong>Location: </strong>" . $location . "<br><br>
                        <strong>Contact Info: </strong>" . $contactInfo . "<br><br>
                        <strong>Uploaded Images: </strong>";
if (isset($_FILES['files'])) {
 foreach($_FILES['files']['name'] as $file) {
                $renamed_file = str_replace(" ", "_", $file);
		$body .= "<a href='http://thethingsandstuff.com/var/uploadify/". urlencode($renamed_file) . "'>".$renamed_file."</a><br>";
        }
}
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
