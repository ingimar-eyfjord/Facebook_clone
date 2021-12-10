<?php
function redirectSuccess($where){
    http_response_code(200);
        echo $where;
        if($where == "profile"){
            header("Location: /$where"); 
        }
        exit();
    }
function redirectError($error, $where){
    http_response_code(500);
    if($where != "signup"){
        header("Location: /$where/error/$error");
    }
    exit();
}
if(!isset($_POST["Password"])) {
    $PasswordErr = "Please enter Password";
     redirectError($PasswordErr, $where);
     exit();
} 
if(isset($_POST["Password"])) {
    if(strlen($_POST["Password"]) < '5'){
        $PasswordErr = "Your Password Must Contain At Least 2 Characters!";
        redirectError($PasswordErr, $where);
        exit();
    }
    if(!preg_match("#[0-9]+#",$_POST["Password"])) {
        $PasswordErr = "Your Password Must Contain At Least 1 Number!";
        redirectError($PasswordErr, $where);
        exit();
    }
    if(!preg_match("#[A-Z]+#",$_POST["Password"])) {
        $PasswordErr = "Your Password Must Contain At Least 1 Capital Letter!";
        redirectError($PasswordErr, $where);
        exit();
    }
    if(!preg_match("#[a-z]+#",$_POST["Password"])) {
        $PasswordErr = "Your Password Must Contain At Least 1 Lowercase Letter!";
        redirectError($PasswordErr, $where);
        exit();
    }
}