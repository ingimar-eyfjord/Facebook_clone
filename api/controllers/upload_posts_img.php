<?php

$body = file_get_contents('php://input');
$data = json_decode($body , true);

function saveImages($Blob, $name){
    if (preg_match('/^data:image\/(\w+);base64,/', $Blob, $type)) {
        $Blob = substr($Blob, strpos($Blob, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif
        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            throw new \Exception('invalid image type');
        }
        $Blob = str_replace( ' ', '+', $Blob );
        $Blob = base64_decode($Blob);
        if ($Blob === false) {
            throw new \Exception('base64_decode failed');
            http_response_code(500);
            echo "Something went wrong.";
            exit();
        }
        file_put_contents("content/posts/images/{$name}.{$type}", $Blob);
    } else {
        // throw new \Exception('did not match data URI with image data');
        http_response_code(500);
        echo "Something went wrong.";
        exit();
    }
}

foreach($data as $key => $value) {
    $Blob =  $value['data'];
    $name = $value['name'];
    saveImages($Blob, $name);
}

http_response_code(200);
echo "Files uploaded";
exit();