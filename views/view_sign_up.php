<?php  require_once(__DIR__.'./header.php'); ?>
<form class="flexColumn log_sign_form container" method="POST" onsubmit="signUp(this); return false;">
    <?php
        if(isset($errorMessage)){
            echo '<p class="error"> '.urldecode($errorMessage).'</p>';
        }
        ?>
    <legend>Signup information</legend>
    <div class="form-floating m-3">
        <input class="form-control" type="text" placeholder="First Name" id="floatingInputF" name="first_name" required>
        <label for="floatingInputF">First Name</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="text" placeholder="Last name" id="floatingInputL" name="last_name" required>
        <label for="floatingInputL">Last name</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="number" min="1" max="100" placeholder="Age" id="floatingInputA" name="age"
            required>
        <label for="floatingInputA">Age</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="text" placeholder="Username" autocomplete="username" id="floatingInputU"
            name="username" required>
        <label for="floatingInputU">Username</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="email" onblur="_$.val(this)" onkeydown="_$.val(this)" onkeyup="_$.val(this)"
            placeholder="Password" autocomplete="email" id="floatingInputE" name="email" required>
        <label for="floatingInputE">Email</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="text" onblur="_$.val(this)" onkeydown="_$.val(this)" onkeyup="_$.val(this)"
            placeholder="Phone number" autocomplete="tel" pattern="[0-9]{8}" id="floatingInputPhone" name="Phone"
            required>
        <label for="floatingInputPhone">Phone</label>
    </div>
    <div class="m-3">
        <p class="pStrength">Password Strength: <span id="strength"></span></p>
        <input type="range" min="0" max="100" value="0" class="slider" id="myRange">
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="password" onblur="_$.val(this)" onkeydown="_$.val(this)"
            onkeyup="_$.val(this)" placeholder="Password" autocomplete="new-password" id="floatingInputP"
            name="Password" required>
        <label for="floatingInputP">Password</label>
    </div>
    <div class="form-floating m-3">
        <input class="form-control" type="password" onblur="_$.val(this)" onkeydown="_$.val(this)"
            onkeyup="_$.val(this)" placeholder="Confirm password" autocomplete="new-password" id="floatingInput"
            name="cPassword" required>
        <label for="floatingInput">Confirm password</label>
    </div>
    <button type="submit" class="btn btn-primary m-3">Sign up</button>
    <h3 class="m-3" id="message" style="text-align:center;"></h3>
</form>
<script>
async function signUp(element) {
    const inputs = $('input')
    const body = formToJSON(inputs)
    $.post("/signup", JSON.stringify(body)).done(function(data) {
        _$.one('#message').innerHTML = data;
        _$.one('#message').style.color = "green";
        return
    }).fail(function(data) {
        _$.one('#message').innerHTML = data;
        _$.one('#message').style.color = "red";
        return
    });
}
// console.log(body)

// let conn = await fetch(`/signup`, {
//     method: "POST",
//     headers: {
//         "Content-Type": "application/json", // sent request
//         // "Accept":       "application/json"   // expected data sent back
//     },
//     body: JSON.stringify(body),
//     cache: "no-cache"
// })

// let data = await conn.text()
// console.log(data)
// if (!conn.ok) {
//     console.log(data)
//     _$.one('#message').innerHTML = data;
//     _$.one('#message').style.color = "red";
//     return
// }
// _$.one('#message').innerHTML = data;
// _$.one('#message').style.color = "green";
</script>


<?php  require_once(__DIR__.'./footer.php'); ?>