{{ include('header.tpl') }}
<div class="container form-signin">
    <div class="row">
        <div class="col">
        </div>
        <div class="col">
            {{ msg|default('') }}
            {% if not loginMessage is empty %}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    {{ loginMessage|raw }}
                </div>
            {% endif %}
            <form class="form-signin" action="/auth/login_check" method="post">
                <h2 class="form-signin-heading">Please sign in</h2>
                <label for="_username" class="sr-only">Username</label>
                <input type="text" id="username" name="_username" class="form-control" placeholder="Username" required autofocus>
                <label for="_password" class="sr-only">Password</label>
                <input type="password" id="_password" name="_password" class="form-control" placeholder="Password" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
        </div>
        <div class="col">
        </div>
    </div>
</div>
{{ include('footer.tpl') }}
