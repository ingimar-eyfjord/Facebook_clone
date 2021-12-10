<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
if (isset($data['password'])){
    if (!isset($data['CPassword'])) {
        echo "Please confirm your password";
        http_response_code(500);
        exit();
    } 
    if ($data['password'] !== $data['CPassword']) {
        echo "Passwords do not match";
        http_response_code(500);
        exit();
    }
    $CPassword = $data['CPassword'];
    $Password = $data['password'];
    unset($data['CPassword']);
    unset($data['password']);
    $data['password'] = password_hash($Password, PASSWORD_DEFAULT);
}
$columns = implode(',', array_keys($data));
$columns = explode(',', $columns);
$placeholders = implode(',', array_fill(0, count($data), '?'));
$values = array_values($data);
$ColumnsPlaceholder = '';
for ($i=0; $i < count($columns); $i++) { 
    $ColumnsPlaceholder .= "$columns[$i] = :$columns[$i],";
}
$ColumnsPlaceholder = mb_substr($ColumnsPlaceholder, 0, -1);
$valuesPlaceholder = array();
for ($i=0; $i < count($values); $i++) { 
    $valuesPlaceholder += [":$columns[$i]"=>$values[$i]];
}
try{
    $stmt = $db->prepare("UPDATE users set $ColumnsPlaceholder WHERE user_id = ".$_SESSION['user_id']."");
    $stmt->execute($valuesPlaceholder);
    http_response_code(200);
    echo "You successfully changed the information";
}catch(PDOException $ex){
    echo $ex;
    http_response_code(500);
    echo "Sorry, something went wrong";
    exit();
}