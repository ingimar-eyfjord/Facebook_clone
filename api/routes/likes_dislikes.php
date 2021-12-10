<?php 
post('/post/:id/:like_dislike', function($id, $like_dislike){
    if(!ctype_digit($id)){
        bad_request("Invalid ID");
    };
    if(!ctype_digit($like_dislike)){
        bad_request("Client sent wrong info");
    };
    switch ($like_dislike):
        case 0:
            echo 'disliked';
            break;
        case 1:
            echo 'liked';
            break;
        default:
        bad_request("Invalid like or dislike");
    endswitch;
});