<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button
            class="navbar-toggler navbar-toggler-right"
            type="button" data-toggle="collapse"
            data-target="#mainmenu"
            aria-controls="mainmenu"
            aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">Stock</a>
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
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Add <span class="caret"></span></a>
             <ul class="dropdown-menu">
                <li class="nav-item">
                    <a class="nav-link" href="/add/item">Add Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/add/location">Add Location</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/add/site">Add Site</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/add/category">Add Category</a>
                </li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Delete <span class="caret"></span></a>
            <ul class="dropdown-menu">
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
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Account <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li class="nav-item">
                    <a class="nav-link" href="/auth/logout">Logout</a>
                </li>
            </ul>
        </li>
      </ul>
    </div>
</nav>
