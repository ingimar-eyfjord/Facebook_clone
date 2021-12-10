<?php
get('/', 'views/view_home.php');
get('/home', 'views/view_home.php');
get('/services/find_image/$name', 'services/find_image.php');

// get('/login/error/$errorMessage', 'views/view_login.php');
    
// post('/login', "api/controllers/auth.php");

// post('/login/reactivate' , 'api/controllers/auth_reactivate.php');