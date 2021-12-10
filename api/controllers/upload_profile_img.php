<?php
// this file does not use Ajax so only session csrf check will work
 if( ! isset($_SESSION['csrf']) ){ exit(); }
if(!isset($_POST['submit'])){
    header('Location: /profile');
    exit();
}
if($_FILES['File']['error']){
    header('Location: /profile');
    exit();
}
$_FILES['File']['fileExt'] = mime_content_type($_FILES['File']['tmp_name']);
$_FILES['File']['fileExt'] = explode('/', $_FILES['File']['fileExt']);
$_FILES['File']['fileExt'] = strtolower(end($_FILES['File']['fileExt']));
$_FILES['File']['allowed'] = array('jpg', 'jpeg', 'png', 'gif');
if(!in_array($_FILES['File']['fileExt'], $_FILES['File']['allowed'])){
    header('Location: /profile');
    exit();
}
foreach ($_FILES['File']['allowed'] as $key => $value) {
    $_FILES['File']['name'] = $_SESSION['username'].".".$value;
    $CleaningDestination = "content/images/profiles/".$_FILES['File']['name'];
    if(file_exists($CleaningDestination)) {
        chmod($CleaningDestination,0755); //Change the file permissions if allowed
        unlink($CleaningDestination); //remove the file
    }
};

$_FILES['File']['name'] = $_SESSION['username'].".".$_FILES['File']['fileExt'];
$destination = "content/images/profiles/".$_FILES['File']['name'];
move_uploaded_file($_FILES['File']['tmp_name'], $destination);
header('Location: /profile');
exit();