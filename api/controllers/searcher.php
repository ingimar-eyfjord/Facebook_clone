<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once("{$_SERVER['DOCUMENT_ROOT']}/api/models/dbc.php");
$body = file_get_contents('php://input');
$data = json_decode($body , true);
$_POST = $data;
if(!isset($_POST['search_for'])){
http_response_code(400);
exit();
}
if(strlen($_POST['search_for']) < 2){
    http_response_code(400);
    exit();
}
if(strlen($_POST['search_for']) > 20){
    http_response_code(400);
    exit();
}
$offset = intval($_POST['page']) * 20;
try{
      $q = $db->prepare('SELECT username, first_name, last_name, user_id, email, age, active, phone FROM users WHERE first_name LIKE :searchString OR last_name LIKE :searchString OR username LIKE :searchString ORDER BY age');
      $q->bindValue(':searchString', '%'.trim($_POST['search_for']).'%');
      $q->execute();
      $users = $q->fetchAll();
      if(count($users) === 0){
        http_response_code(200);
        echo "no result";
        exit();
      }
      $users[0]->num_rows = count($users);
      $Q2 = $db->prepare('SELECT COUNT(:everything) FROM users WHERE first_name LIKE :searchString OR last_name LIKE :searchString'); 
      $Q2->bindValue(':everything', "*");
      $Q2->bindValue(':searchString', '%'.trim($_POST['search_for']).'%');
      $Q2->execute();
      $num = $Q2->fetchColumn(); 
      $users[0]->num_rows = $num;
      header("Content-type:application/json");
      echo json_encode($users);
      http_response_code(200);
      exit();
    }catch(PDOException $ex){
      http_response_code(500);
      echo "Sorry, something went wrong";
      exit();
    }