<?php
 require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
  require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
  $body = file_get_contents('php://input');
  $data = json_decode($body , true);
try{    
        $Post = $data['emailOrUsername'];
        $POST = [':email'=>$data['emailOrUsername'],':username'=>$data['emailOrUsername']];
        $stmt = $db->prepare('SELECT * FROM users where email = :email OR username = :username');
        $stmt->execute($POST);
        $rows = $stmt->fetchAll();
        if(count($rows) == 0){
                echo "User does not exist";
                http_response_code(500);
                exit();
        }
        if($rows[0]->active == 0){
                echo "This account is deactivated";
                http_response_code(500);
                exit();
        }
        $pwdCheck = password_verify($data['Password'], $rows[0]->password);
                if ($pwdCheck == false) {
                http_response_code(500);
                exit();
        }
        else{
                $_SESSION['username'] = $rows[0]->username;
                $_SESSION['email'] = $rows[0]->email;
                $_SESSION['user_id'] = $rows[0]->user_id;
                $_SESSION['first_name'] = $rows[0]->first_name;
                $_SESSION['last_name'] = $rows[0]->last_name;
                $_SESSION['age'] = $rows[0]->age;
                $_SESSION['active'] = $rows[0]->active;
        }
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