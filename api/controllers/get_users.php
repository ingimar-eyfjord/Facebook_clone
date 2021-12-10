<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/controllers/csrf_validator.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/api/models/dbc.php'); 
try{    
        $stmt = $db->prepare('SELECT user_id, username, first_name, last_name, email, age, active FROM users');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $stmt2 = $db->prepare('SELECT * FROM blocked');
        $stmt2->execute();
        $rows2 = $stmt2->fetchAll();
        $userHasBlocked = array();
        foreach ($rows2 as $Blocked => $BlockedArr) {
        foreach ($rows as $key => $value) {
        if($_SESSION['user_id'] === $BlockedArr->user_blocked_by){
        array_push($userHasBlocked, $BlockedArr);
        }           
        }
        }
        $_tmp = array();
        foreach($userHasBlocked as $key => $value) {
                if(!array_key_exists($value->id, $_tmp)) {
                    $_tmp [$value->id] = $value;
                }
            }
        $userHasBlocked = array_values($_tmp);
        foreach($userHasBlocked as $key => $value) {
                foreach ($rows as $UsersKey => $UsersValue){
                       if($UsersValue->user_id == $value->blocked_user_id){
                        unset($rows[$UsersKey]);  
                       };
                }
                
        }
        
      header("Content-type:application/json");
      $JSON = json_encode($rows, true);
      echo $JSON;
      http_response_code(200);
      exit();
}catch(PDOException $ex){
header("Location: profile");
exit();
}