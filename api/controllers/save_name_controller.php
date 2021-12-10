<?php
$body = file_get_contents('php://input');
$data = json_decode($body , true);

file_put_contents('filename.txt', $data['name']);