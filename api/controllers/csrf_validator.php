<?php
$headers = getallheaders();
if( ! isset($_SESSION['csrf'])){ 
echo "There was an error";
http_response_code(500);
exit(); 
}
if( ! isset($headers['csrf-token'])){
echo "There was an error";
http_response_code(500);
exit(); }
if($headers['csrf-token'] !== $_SESSION['csrf']){
echo "There was an error";
http_response_code(500);
exit(); }