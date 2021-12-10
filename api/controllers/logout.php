<?php
session_destroy();
global $root;
header("Location: /login"); 
exit();