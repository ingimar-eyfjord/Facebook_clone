<?php  require_once(__DIR__.'./header.php'); ?>
<form onsubmit="resetPassword(this); return false;" class="container mt-3">
    <legend>Reset password step 3</legend>
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        This page is step three part of a three step process to change your password.
                        Please choose the new password you would like.
                    </div>
                    <div class="row gutters mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="Street">New password</label>
                                <input type="password" onblur="_$.val(this)" onkeydown="_$.val(this)"
                                    onkeyup="_$.val(this)" name="Password" autocomplete="new-password"
                                    class="form-control" id="Street" placeholder="Enter new password">
                            </div>
                        </div>
                    </div>
                    <div class="row gutters mt-3">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="Street">Reenter password</label>
                                <input type="password" onblur="_$.val(this)" onkeydown="_$.val(this)"
                                    onkeyup="_$.val(this)" name="CPassword" autocomplete="new-password"
                                    class="form-control" id="Street" placeholder="Reenter new password">
                            </div>
                        </div>
                        <input type="hidden" name="selector" value="<?= $selector?>">
                        <input type="hidden" name="token" value="<?= $token?>">
                    </div>
                    <div class="row gutters mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="text-right">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary">Confirm new
                                    password</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="pStrength">Password Strength: <span id="strength"></span></p>
                        <input type="range" min="0" max="100" value="0" class="slider" id="myRange">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<h3 class="m-3" id="message" style="text-align:center;"></h3>
<script>
async function resetPassword(form) {
    const Json = formToJSON(form)
    const resetRequest = await $.post("/login/confirm-new-password", JSON.stringify(Json)).done(function(data) {
        console.log(data)
        $('#message').text(data);
        $('#message').css("color", "green");
    }).fail(function(data) {
        $('#message').text(data.responseText);
        $('#message').css("color", "red");
    });
}
</script>
<?php  require_once(__DIR__.'./footer.php'); ?>