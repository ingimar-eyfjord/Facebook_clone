<?php
get('/posts/images/$id', 'services/get_posts_img.php');
post('/posts/images', 'api/controllers/upload_posts_img.php');
?>