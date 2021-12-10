<?php
$files = glob("content/posts/images/" . $id . "_*");
if (!empty($files)) {
    http_response_code(200);
    echo json_encode($files);
    exit();
} else {
    http_response_code(500);
    echo "Post has no images";
    exit();
}