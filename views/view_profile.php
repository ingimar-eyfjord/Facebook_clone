<?php  require_once(__DIR__.'./header.php'); 
if(!isset($_SESSION['username'])){
    header("Location: /login");
        }
?>
<div class="container mt-3">
    <legend>Your Profile information</legend>
    <div class="row gutters">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="account-settings">
                        <div class="user-profile">
                            <div class="user-avatar">
                                <img class="profilePhoto-image"
                                    src="<?=glob("content/images/profiles/" . $_SESSION['username'] . ".*")[0]?>"
                                    aria-alt="<?=$_SESSION['username']?>'s profile photo">
                            </div>
                            <p class="font-weight-bold mt-3 mb-0">Full Name</p>
                            <p class="mt-0 pt-0"><?=$_SESSION['first_name']?> <?=$_SESSION['last_name']?></p>
                            <p class="font-weight-bold mb-0">Username</p>
                            <p><?=$_SESSION['username']?></p>
                            <p class="font-weight-bold mb-0">Age</p>
                            <p><?=$_SESSION['age']?></p>
                            <p class="font-weight-bold mb-0">Status</p>
                            <p><?=$_SESSION['active'] == "1" ? "active" : "inactive"?></p>
                            <p class="font-weight-bold mb-0">Email</p>
                            <p><?=$_SESSION['email']?></p>
                        </div>
                        <form action="profile/upload/image" id="uploadImage" method="post"
                            enctype="multipart/form-data">
                            <label class="custom-file-upload">
                                <input type="file" onchange="loadFile(event)" name="File" id="fileToUpload">
                                Click here to upload or change your profile picture
                            </label>
                            <input class="mt-3" type="submit" value="Confirm upload" name="submit">
                            <img class="mt-3" id="output" />
                        </form>
                        <button class="btn btn-danger mt-3" style="width:100%" onclick="deactivate()">Deactivate my
                            profile</button>
                    </div>
                </div>
            </div>
        </div>
        <form onsubmit="ChangeProfileInfo(this); return false;" class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <p class="font-weight-bold"> Edit your info here.</p> To change your info, only fill out the
                        fields corresponding to the desired change.
                    </div>
                    <div class="row gutters">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h6 class="mb-2 text-primary">Personal Details</h6>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="first_name">Full Name</label>
                                <input name="first_name" type="text" class="form-control" id="first_name"
                                    placeholder="Enter full name">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input name="last_name" type="text" class="form-control" id="last_name"
                                    placeholder="Enter full name">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input name="phone" type="text" class="form-control" id="phone"
                                    placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input name="age" type="number" class="form-control" id="age" placeholder="Age">
                            </div>
                        </div>
                    </div>
                    <div class="row gutters">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h6 class="mt-3 mb-2 text-primary">Account info</h6>
                        </div>
                        <!-- ? Currently websocket fetch messages by usernames, therefore I'd need to change that first to allow usernames to be changed -->

                        <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="Username">Username</label>
                                <input name="username" type="text" class="form-control" id="Username"
                                    placeholder="Enter username">
                            </div>
                        </div> -->
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="eMail">Email</label>
                                <input name="email" type="email" class="form-control" id="eMail"
                                    placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input name="password" type="password" class="form-control" id="password"
                                    placeholder="Password">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                <label for="CPassword">Confirm password</label>
                                <input name="CPassword" type="password" class="form-control" id="CPassword"
                                    placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                    <div class="row gutters mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="text-right">

                                <button type="submit" id="submit" name="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                    <h3 class="m-3" id="message" style="text-align:center;"></h3>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
async function deactivate() {
    let txt;
    let r = confirm("This will deactivate your account, are you sure?");
    if (r !== true) {
        return;
    }
    await $.ajax({
        type: "POST",
        url: "/profile/deactivate",
        data: JSON.stringify({
            id: "<?= $_SESSION['user_id'] ?>",
            email: "<?=$_SESSION['email']?>"

        }),
        success: function(response) {
            window.location.replace("/logout");
        },
        error: function(result) {
            console.log(result)
        }
    });
}
const loadFile = function(event) {
    const output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
};
async function ChangeProfileInfo(form) {
    const Json = formToJSON(form)
    const changeRequest = await $.post("/profile/changeInfo", JSON.stringify(Json)).done(function(data) {
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