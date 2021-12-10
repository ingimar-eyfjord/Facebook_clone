<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
$_POST = $data;

if(!isset($data['username'])){
    $errorMess = "Something went wrong";
    header("Location: /signup/error/$errorMess"); 
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$whereSuccess = 'profile';
$where = 'signup';
$username = $data['username'];
$email = $data['email'];
$password = $data['Password'];
$passwordRepeat = $data['cPassword'];
$age = $data['age'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$phone = $data['Phone'];
$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
require_once(__DIR__.'./validator.php');  

try{    
    $stmt = $db->prepare('SELECT user_id FROM users where email = :email OR username = :username');
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if(count($rows) >= 1){
        $UserError = "User with that username or email already exists";
        http_response_code(500);
        echo $UserError;
        exit();
    }else{
        createUser($username,$first_name,$last_name,$email,$hashedPwd, $db, $whereSuccess, $age, $where,$phone);
    }
}catch(PDOException $ex){
        http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
}
   function createUser($username,$first_name,$last_name,$email,$hashedPwd, $db, $age, $phone){
        try{
            $POST = [$username,$first_name,$last_name,$email,$hashedPwd,$age,1, $phone];
            $stmt = $db->prepare('INSERT INTO users (username, first_name, last_name, email, password, age, active, phone) values(?,?,?,?,?,?,?,?)');
            $stmt->execute($POST);
            $selector = bin2hex(random_bytes(8));
	        $token = random_bytes(32);
            $expires = date('U') + 900;
            $Verification_Post = [$email,password_hash($token, PASSWORD_DEFAULT),$selector,$expires];
            $Verification_stmt = $db->prepare('INSERT INTO authenticate (email, authToken, selector, tokenExpires) values(?,?,?,?)');
            $Verification_stmt->execute($Verification_Post);
            $sign_up_message = "You have been signed up. We sent you an email to verify that it belongs to you. Please open that email to very it";
            $send_email_email = $email;
            $subject = "Email verification needed";
            require_once('signup_email_template.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/services/send_mail.php');
            http_response_code(200);
            echo "Thank you for signing up, please go to your email address to verify your email.";
            exit(); 
        }catch(PDOException $ex){
        http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
            }
    }

   