<?php  require_once(__DIR__.'./header.php'); ?>

<style>
.error {
    color: red;
}
</style>

<form class="flexColumn container log_sign_form mt-3" onsubmit="LogIN(this); return false;">
    <legend>Login</legend>
    <div class="card h-100">

        <?php
        if(isset($errorMessage)){
            echo '<p class="error"> '.urldecode($errorMessage).'</p>';
        }
        ?>
        <div class="form-floating m-3">
            <input class="form-control" type="text" placeholder="Email or Username" id="floatingInputF"
                name="emailOrUsername" required>
            <label for="floatingInputF">Email or Username</label>
        </div>
        <div class="form-floating m-3">
            <input class="form-control" type="text" placeholder="Password" id="floatingInputP" name="Password" required>
            <label for="floatingInputP">Password</label>
        </div>



        <button type="button" id="reactivateBtn" class="btn btn-primary m-3 displayNone"
            onclick="reactivate(this); return false;">Reactivate
            account</button>

        <button id="loginBtn" class="btn btn-primary m-3">Login</button>


        <a href="/login/reset-password" class="text-center m-3">Forgot your password? Click here to reset it.</a>
        <h3 class="m-3" id="message" style="text-align:center;"></h3>
    </div>
</form>

<form id="ReactivateForm" method="POST" action="/profile/reactivate">
    <input type="hidden" name="emailOrUsername">
    <input type="hidden" name="Password">
</form>


<script>
async function LogIN(form) {
    const Json = formToJSON(form)
    const LoginRequest = await $.post("/login", JSON.stringify(Json)).done(function(data) {
        window.location.replace("/home");
    }).fail(function(data) {
        if (data.responseText == "This account is deactivated") {
            $("#reactivateBtn").removeClass("displayNone")
            $("#loginBtn").addClass("displayNone")
        }
        $('#message').text("There has been an error");
        $('#message').css("color", "red");
    });
}
async function reactivate(button) {
    const form = document.querySelector("#ReactivateForm")
    const emailOrUsername = form.querySelector('[name="emailOrUsername"]')
    const Password = form.querySelector('[name="Password"]')
    const pass = $('#floatingInputP')
    const user = $('#floatingInputF')
    const Data = formToJSON([pass[0], user[0]])
    Password.value = Data.Password
    emailOrUsername.value = Data.emailOrUsername
    form.submit()
}
</script>
<?php  require_once(__DIR__.'./footer.php'); ?>

<script>
alert(
    "Copy this for default admin login for examiners username: DEFADMIN, email: ingimar.web.dev@gmail.com, Password: Zp3Lrq8cLBRfzPS"
)
</script>