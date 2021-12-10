<!DOCTYPE html>
<html lang="en" style="--colour:#DC143C;--stWidth:33%">
<?php 
    $csrf_token = bin2hex(random_bytes(25));
    $_SESSION['csrf'] = $csrf_token;
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'MaskBook'?></title>
    <link rel="icon" type="image/png" href="/content/images/favicon-32x32.png" />
    <script src="/library.js"></script>
    <script src="/libraries/lodash.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-lpyLfhYuitXl2zRZ5Bn2fqnhNAKOAaM/0Kr9laMspuaMiZfGmfwRNFh8HlMy49eQ" crossorigin="anonymous">
    </script>
    <script src="https://rawgit.com/moment/moment/2.2.1/min/moment.min.js"></script>
    <link rel="stylesheet" href="/libraries/OverlayScrollbars.min.css" crossorigin="anonymous">
    <script src="/libraries/jquery.overlayScrollbars.min.js"></script>
    <link rel="stylesheet" href="/styles/0px.css">
    <link rel="stylesheet" href="/styles/600px.css">
    <link rel="stylesheet" href="/styles/900px.css">
    <link rel="stylesheet" href="/styles/1080px.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body>
    <header class="header mb-3">
        <a href="" class="logo">Navigation</a>
        <input class="menu-btn" type="checkbox" id="menu-btn" />
        <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
        <ul class="menu">
            <li><a href="/home">Home</a></li>
            <li><a href="/profile">Profile</a></li>
            <?php
            if(isset($_SESSION['admin'])){
                if($_SESSION['admin'] === true){
                    echo '<li><a href="/users">Admins</a></li>';
                }
            }
            ?>
            <li><a href="/login">Login</a></li>
            <li><a href="/logout">Logout</a></li>
            <li><a href="/signup">Sign Up</a></li>
        </ul>
    </header>
    <input type="hidden" id="Token" value="<?= bin2hex(random_bytes(16))?>">
    <!-- <div class="verifyEmailMessage displayNone">
        <p>Please verify your email, within <span></span> </p>
    </div> -->
    <!--?! Setup token for PHP controllers -->
    <script>
    $.ajaxSetup({
        headers: {
            'csrf-token': '<?=$csrf_token?>'
        }
    });
    // window.addEventListener("DOMContentLoaded", verifyEmail)
    let date = undefined;
    async function verifyEmail() {
        await $.ajax({
            dataType: "json",
            url: "/signup/verify/ask",
            success: function(response) {
                startClock();
            },
            error: function(result) {
                date = JSON.parse(data)
                return;
            }
        });
    }
    let verify;

    function startClock() {
        verify = setTimeout(startClock, 1000);
        let duration = moment.duration(moment.unix(date).diff(moment()));
        let diff = duration;
        const hours = diff.asHours().toFixed(0);
        const minutes = diff.minutes().toFixed(0);
        const seconds = diff.seconds().toFixed(0);
        const message = $.one('.verifyEmailMessage')
        message.classList.remove('displayNone')
        message.querySelector('span').textContent = `${hours} hour ${minutes} minutes ${seconds} seconds left`;
    }
    </script>