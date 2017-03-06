<!DOCTYPE HTML>
<html>
    <head>
        <title>Stock System: {$title|default:'Home'}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href='//fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="/assets/css/login.css">
    </head>
    <body>
    <div class="text-center" style="padding:50px 0">
        <div class="logo">Password Reset</div>
        <!-- Main Form -->
        <div class="login-form-1">
            <form id="forgot-password-form" class="text-left">
                <div class="etc-login-form">
                    <p>When you fill in your registered email address, you will be sent instructions on how to reset your password.</p>
                </div>
                <div class="login-form-main-message"></div>
                <div class="main-login-form">
                    <div class="login-group">
                        <div class="form-group">
                            <label for="fp_email" class="sr-only">Email address</label>
                            <input type="text" class="form-control" id="fp_email" name="fp_email" placeholder="email address">
                        </div>
                    </div>
                    <button type="submit" class="login-button"><i class="fa fa-chevron-right"></i></button>
                </div>
                <div class="etc-login-form">
                    <p>already have an account? <a href="/auth/login">login here</a></p>
                    <p>new user? <a href="/auth/register">create new account</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    <script src="/assets/js/login.js"></script>
    </body>
</html>
