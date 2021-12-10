<?php
$files = glob("content/images/profiles/" . $name . ".*")[0];

if (!empty($files)) {
    header("Location: /content/images/profiles/" . basename($files));
} else {
    // Redirect to a default image if the file can't be found
    header("Location: /content/images/profiles/avatar.png");

}