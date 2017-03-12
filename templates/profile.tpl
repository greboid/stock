{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col">
        </div>
        <div class="col align-self-center">
            <h1>Profile</h1>
            <h2>User Details</h2>
            <form method="post" action="" id="profileDetailsForm">
                <fieldset>
                    <div class="form-group row">
                        <label class="col-4 col-form-label" for="username">Username</label>
                        <input class="col form-control" id="username" name="username" type="text" placeholder="name" disabled value="{$username}">
                    </div>
                    <div class="form-group row">
                        <label class="col-4 col-form-label" for="name">Name</label>
                        <input class="col form-control" id="name" name="name" type="text" placeholder="name" required value="{$userdata['name']}">
                    </div>
                    <div class="form-group row">
                        <label class="col-4 col-for-label" for="email">Email Address</label>
                        <input class="col form-control" id="email" type="email" name="email" required value="{$userdata['email']}">
                    </div>
                    <div class="offset-4 form-group row">
                        <button class="btn btn-primary btn-block" type="submit">Update Details</button>
                    </div>
                </fieldset>
            </form>
            <h2>Password</h2>
            <form method="post" action="" id="changePasswordForm">
                <fieldset>
                    <div class="form-group row">
                        <label class="col-4 col-form-label" for="newpassword">New Password</label>
                        <input class="col form-control" id="newpassword" type="password" name="newpassword" placeholder="New Password" required>
                    </div>
                    <div class="form-group row">
                        <label class="col-4 col-form-label" for="confirmpassword">Confirm New Password</label>
                        <input class="col form-control" id="confirmpassword" type="password" name="confirmpassword" placeholder="Confirm New Password" required>
                    </div>
                    <div class="offset-4 form-group row">
                        <button class="btn btn-primary btn-block" type="submit">Update Password</button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="col">
        </div>
    </div>
</div>
{include file='footer.tpl'}
