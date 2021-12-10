<?php 
// This file is not being 
if( ! isset($_SESSION['csrf']) ){ exit(); }
    if(isset($_SESSION['email'])){
        require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
        $stmt = $db->prepare('SELECT tokenExpires, selector, emailVerified FROM authenticate where email = :email');
        $stmt->bindValue(':email', $_SESSION['email']);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        if(count($rows) >= 1){
        if($rows[0]->emailVerified == 0){
        echo $rows[0]->tokenExpires;
        }else{
        echo "no";
        http_response_code(500);
        exit();
        }
        }       
        };
?>