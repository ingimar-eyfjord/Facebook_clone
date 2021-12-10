<?php
get('/user/$id', 'views/view_single_user.php');
get('/users', 'views/view_users.php');
get('/users/getAll', 'api/controllers/get_users.php');
post('/users/block', 'api/controllers/block_user.php');
post('/users/delete/$id', 'api/controllers/delete_user.php');
  