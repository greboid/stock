{{ include('header.tpl') }}
{{ include('menu.tpl') }}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Verify user</h1>
                <p>Thanks for verifying your address, to finalise your registration you just need to set your password below.</p>
                <form method="post" id="verifyEmailForm">
                    <fieldset>
                        <div class="form-group row">
                            <label class="col-4 col-form-label" for="newpassword">Password: </label>
                            <input class="col form-control" id="newpassword" name="newpassword" type="password" placeholder="Password" required>
                        </div>
                        <div class="form-group row">
                            <label class="col-4 col-form-label" for="confirmpassword">Confirm Password: </label>
                            <input class="col form-control" id="confirmPassword" name="confirmpassword" type="password" placeholder="Confirm Password">
                        </div>
                        <div class="form-group row">
                            <button class="offset-4 col form-control btn btn-block btn-primary" type="submit">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{{ include('footer.tpl') }}
