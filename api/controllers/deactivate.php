<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
$_POST = $data;
$email = $_POST['email'];
if(isset($_POST['user_firstName'])){
    $username = $_POST['user_firstName'];
}
try{    
        $POST = [':user_id'=>$_POST['id']];
        $stmt = $db->prepare("UPDATE users set active = 0 WHERE user_id = :user_id AND active = 1");
        $stmt->execute($POST);
        if(!$stmt ->rowCount()){
            echo "Something went wrong";
            http_response_code(500);
            exit();
        }else{

            echo "You have successfully deactivated the account";
            $subject = "Deactivation Notification";
            require_once('deactivated_template.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/services/send_mail.php');
            http_response_code(200);
            exit();
        }
}catch(PDOException $ex){
    echo "Something went wrong";
    http_response_code(500);
    exit();
}