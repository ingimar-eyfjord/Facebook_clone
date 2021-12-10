<?php  require_once(__DIR__.'./header.php'); ?>

<form onsubmit="resetPassword(this); return false;" class="container mt-3">
    <legend>Reset password step 2</legend>
    <div class="row gutters">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        This page is step two of a three step process to change your password.
                        Please enter your email address and you will receive further instructions via mail on the next
                        step.
                    </div>

                    <div class="row gutters">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="Street">Email</label>
                                <input type="email" name="Email" class="form-control" id="Street" placeholder="Email">
                            </div>
                        </div>


                    </div>
                    <div class="row gutters mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="text-right">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary">Send reset
                                    email</button>
                            </div>
                        </div>
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
    const resetRequest = await $.post("/login/reset-password", JSON.stringify(Json)).done(function(data) {
        $('#message').text(data);
        $('#message').css("color", "green");
    }).fail(function(data) {
        $('#message').text(data.responseText);
        $('#message').css("color", "red");
    });
}
</script>
<?php  require_once(__DIR__.'./footer.php'); 