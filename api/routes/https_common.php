<?php
function bad_request($message){
    http_response_code(400);
    echo $message;
    exit();
    };
    function unauthorized(){
    http_response_code(401);
    exit();
    };
    function internal_server_error(){
    http_response_code(500);
    exit();
    };
require_once("profile.php");
require_once("login.php");
require_once("likes_dislikes.php");
require_once("logout.php");
require_once("posts.php");
require_once("signup.php");
require_once("sqlite.php");
require_once("users.php");
require_once("search.php");
require_once("home.php");
require_once("save_name.php");
get('/email', "api/controllers/send_mail.php");
any('/404', "views/view_404_not.php");