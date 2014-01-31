<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/var/uploadify'; // Relative to the root

//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

//if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
if (!empty($_FILES)) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
        $filename = $_FILES['Filedata']['name'];
        $filename = str_replace(' ', '_', $filename);
        $targetFile = rtrim($targetPath,'/') . '/' . $filename;

        // Validate the file type
        // $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
        $fileParts = pathinfo($filename);

        // if (in_array($fileParts['extension'],$fileTypes)) {
        if (move_uploaded_file($tempFile,$targetFile)) {
                echo base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/var/uploadify/".$filename));
   //             echo "<br>".$_FILES['Filedata']['name']."<br>";
 //echo $filename;
               // var_dump($_FILES);
        } else {
                echo 'Invalid file type.';
        }
}
?>
