<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand">Stock</a>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/">Dashboard</a></li>
        <li><a href="/locations/">Locations</a></li>
        <li><a href="/categories/">Categories</a></li>
      </ul>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
    <ul class="nav navbar-nav navbar-right">
        <li>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Add <span class="caret"></span></a>
             <ul class="dropdown-menu">
                <li>
                    <a class="pure-menu-link" href="/add/item">Add Item</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/add/location">Add Location</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/add/site">Add Site</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/add/category">Add Category</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Delete <span class="caret"></a>
            <ul class="dropdown-menu">
                <li>
                    <a class="pure-menu-link" href="/manage/sites">Manage Sites</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/manage/locations">Manage Locations</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/manage/items">Manage Items</a>
                </li>
                <li>
                    <a class="pure-menu-link" href="/manage/categories">Manage Categories</a>
                </li>
            </ul>
      </ul>
      <form class="navbar-form navbar-right">
        <input type="text" class="form-control" placeholder="Search...">
      </form>
    </div>
  </div>
</nav>
