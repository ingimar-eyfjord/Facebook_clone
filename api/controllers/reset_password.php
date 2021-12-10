<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
$selector = bin2hex(random_bytes(8));
$token = random_bytes(32);
$url = "web.exam:8001/create-new-password/".$selector . "/" . bin2hex($token);
$expires = date('U') + 900;
$email = $data['Email'];

try{    
    $POST = [':email'=>$email];
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");           
    $stmt->execute($POST);
    $rows = $stmt->fetchAll(); 
    //? User does not exist.
    if(sizeof($rows) === 0){
        http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
    }else{
        $first_name = $rows[0]->first_name;
        $username = $rows[0]->first_name;

    }
}catch(PDOException $ex){
    echo "Sorry, something went wrong";
        http_response_code(500);
        exit();
}
try{    
    $stmt = $db->prepare('DELETE FROM resetpassword WHERE Email = :Email');
    $stmt->bindValue(':Email', $email);
    $stmt->execute();
}catch(PDOException $ex){
        http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
}
try{    
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    $POST = [$email,$selector,$hashedToken,$expires];
    $stmt = $db->prepare('INSERT INTO resetpassword (Email, selector, token, expires) values(?,?,?,?)');
    $stmt->execute($POST);
    $subject = "Reset password email";
    require_once('reset_password_email.php');
    
    require_once($_SERVER['DOCUMENT_ROOT'].'/services/send_mail.php'); 
    http_response_code(200);
    echo "An email has been sent to you, please follow the instructions given to change your password.";
    exit();
}catch(PDOException $ex){
        http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
}