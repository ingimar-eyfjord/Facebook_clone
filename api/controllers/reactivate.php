<?php
// this file does not use Ajax so only session csrf check will work
if( ! isset($_SESSION['csrf']) ){ exit(); }
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
$whereSuccess = 'profile';
$where = 'login';
require_once(__DIR__.'./validator.php');  
try{    
        $Post = $_POST['emailOrUsername'];
        $POST = [':email'=>$_POST['emailOrUsername'],'username'=>$_POST['emailOrUsername']];
        $stmt = $db->prepare('SELECT * FROM users where email = :email OR username = :username');
        $stmt->execute($POST);
        $rows = $stmt->fetchAll();
        if(count($rows) == 0){
                $UserError = "User does not exist";
                redirectError($UserError, $where);
                exit();
        }
        $pwdCheck = password_verify($_POST['Password'], $rows[0]->password);
        if ($pwdCheck == false) {
        redirectError("Something went wrong", $where);
        exit();}
        else{
        $POST_2 = [':email'=>$_POST['emailOrUsername'],'username'=>$_POST['emailOrUsername']];
        $stmt_2 = $db->prepare('UPDATE users SET active = 1 where email = :email OR username = :username');
        $stmt_2->execute($POST_2);
                $_SESSION['username'] = $rows[0]->username;
                $_SESSION['email'] = $rows[0]->email;
                $_SESSION['user_id'] = $rows[0]->user_id;
                $_SESSION['first_name'] = $rows[0]->first_name;
                $_SESSION['last_name'] = $rows[0]->last_name;
                $_SESSION['age'] = $rows[0]->age;
                $_SESSION['active'] = 1;
                redirectSuccess($whereSuccess);
        }
}catch(PDOException $ex){
redirectError($ex, $where);
}