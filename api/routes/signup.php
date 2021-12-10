<?php 
get('/signup', "views/view_sign_up.php");
get('/signup/error/$errorMess', 'views/view_sign_up.php');
get('/signup/verify/ask', "api/controllers/verify_email.php");
get('/signup/verify/$token/$selector', "api/controllers/auth_email.php");
get('/signup/verify/error/$errorMessage', "views/view_sign_up.php");
post('/signup', 'api/controllers/signup.php');