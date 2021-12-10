<?php
get('/profile', 'views/view_profile.php');
post('/profile/changeInfo', 'api/controllers/change_profile_info.php');
post('/profile/deactivate', 'api/controllers/deactivate.php');
post('/profile/reactivate', 'api/controllers/auth_reactivate.php');
post('/profile/upload/image', 'api/controllers/upload_profile_img.php');