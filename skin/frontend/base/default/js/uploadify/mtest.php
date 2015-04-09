<?
ini_set('display_errors', 1);
error_reporting(E_ALL); 
$headers = 'From: webmaster@example.com'; mail('cooper@landersoptimized.com', 'Test email using PHP', 'This is a test email message', $headers, '-fwebmaster@example.com'); ?>
