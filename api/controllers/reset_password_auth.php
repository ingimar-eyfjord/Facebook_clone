<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$body = file_get_contents('php://input');
$data = json_decode($body , true);
if (!isset($data['Password']) || !isset($data['CPassword'])) {
	echo "Please fill in both fields";
	http_response_code(500);
	exit();
} 
if ($data['Password'] !== $data['CPassword']) {
	echo "Passwords do not match";
	http_response_code(500);
	exit();
}
$selector = $data['selector'];
$token = $data['token'];
$password = $data['Password'];
$CPassword = $data['CPassword'];
$currentDate = date('U');
// require_once(__DIR__.'./validator.php');  
try{    	
    $POST = [':selector'=>$selector];
    $stmt = $db->prepare("SELECT * FROM resetpassword WHERE selector = :selector");       
    $stmt->execute($POST);
    $rows = $stmt->fetchAll(); 
    //? Token not found
    if(sizeof($rows) === 0){
		http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
    }
	$tokenBin = hex2bin($token);
	$tokenCheck = password_verify($tokenBin, $rows[0]->token);
	if ($tokenCheck === false) {
		http_response_code(500);
		echo "You need to resubmit your reset request.";
		exit();
	}
	$tokenEmail = $rows[0]->Email;
 $expires = $rows[0]->expires;
}catch(PDOException $ex){
	echo "Sorry, something went wrong";
	http_response_code(500);
	exit();
}

if($expires < $currentDate){
	echo "The process has run out of time, please resubmit your request";
	http_response_code(500);
	exit();
}
try{    	
	$POST = [':email'=>$tokenEmail];
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");       
    $stmt->execute($POST);
    $rows = $stmt->fetchAll(); 
    //? User does not exist.

    if(sizeof($rows) === 0){
		http_response_code(500);
        echo "Sorry, something went wrong";
        exit();
    }
}catch(PDOException $ex){
	echo "Sorry, something went wrong";
	http_response_code(500);
	exit();
}
try{    	
	$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    $POST = [':email'=>$tokenEmail, ':password'=>$hashedPwd];
    $stmt = $db->prepare("UPDATE USERS SET password = :password WHERE email = :email");       
    $stmt->execute($POST);
	echo "Password has been updated and you are logged in";
	$_SESSION['username'] = $rows[0]->username;
	$_SESSION['email'] = $rows[0]->email;
	$_SESSION['user_id'] = $rows[0]->user_id;
	$_SESSION['first_name'] = $rows[0]->first_name;
	$_SESSION['last_name'] = $rows[0]->last_name;
	$_SESSION['age'] = $rows[0]->age;
	$_SESSION['active'] = $rows[0]->active;

}catch(PDOException $ex){
	echo "Sorry, something went wrong";
	http_response_code(500);
	exit();
}

try{    
	$stmt = $db->prepare("SELECT * FROM admins where user_id = ".$_SESSION['user_id']."");
	$stmt->execute();
	$rows = $stmt->fetch();
	if($rows->admin == 1){
	$_SESSION['admin'] = true; 
	}else{
	$_SESSION['admin'] = false; 
	}
	header("Location: home");
	http_response_code(200);
	exit();
	}catch(PDOException $ex){
	echo "Sorry, something went wrong";
	http_response_code(500);
	exit();
	}