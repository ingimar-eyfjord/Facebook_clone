<?php
get('/login', "views/view_login.php");
get('/login/error/$errorMessage', 'views/view_login.php');
get('/login/create-new-password/$token/$selector' , 'views/view_create_new_password.php');
get('/login/reset-password' , 'views/view_reset_password.php');
post('/login', "api/controllers/auth.php");
post('/login/confirm-new-password' , 'api/controllers/reset_password_auth.php');
post('/login/reactivate' , 'api/controllers/auth_reactivate.php');
post('/login/reset-password' , 'api/controllers/reset_password.php');