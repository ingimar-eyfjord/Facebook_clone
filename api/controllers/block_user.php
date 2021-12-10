<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
print_r($data);
try{ 
    $POST = [$data['whom'],$data['user_id']];
    $stmt = $db->prepare('INSERT INTO blocked (blocked_user_id, user_blocked_by) values(?,?)');
    $stmt->execute($POST);
    http_response_code(200);
    echo "The user was successfully blocked";
    exit(); 
}catch(PDOException $ex){
    echo "Something went wrong";
    http_response_code(500);
    exit();
}