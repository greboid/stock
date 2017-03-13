<nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
    <button
            class="navbar-toggler navbar-toggler-right"
            type="button" data-toggle="collapse"
            data-target="#mainmenu"
            aria-controls="mainmenu"
            aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="">Stock</a>
    <div class="collapse navbar-collapse" id="mainmenu">
        <ul class="navbar-nav mr-auto mt-2 mt-md-0">
            <li class="nav-item">
                <a class="nav-link" href="/">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/locations">Locations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/categories">Categories</a>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage <span class="caret"></span></a>
            <ul class="dropdown-menu navbar-inverse bg-inverse dropdown-menu-right">
                <li class="nav-item">
                    <a class="nav-link" href="/manage/sites">Manage Sites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/manage/locations">Manage Locations</a>
                </li>
                <li>
                    <a class="nav-link" href="/manage/items">Manage Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/manage/categories">Manage Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/manage/users">Manage Users</a>
                </li>
            </ul>
        </li>
        <li class="nav-item dropdown navbar-inverse bg-inverse">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Account <span class="caret"></span></a>
            <ul class="dropdown-menu navbar-inverse bg-inverse dropdown-menu-right">
                <li class="nav-item">
                    <a class="nav-link" href="/user/profile"><i class="fa fa-inverse fa-user" aria-hidden="true"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/logout"><i class="fa fa-inverse fa-sign-out" aria-hidden="true"></i> Logout</a>
                </li>
            </ul>
        </li>
      </ul>
    </div>
</nav>
{$msg|default:""}
