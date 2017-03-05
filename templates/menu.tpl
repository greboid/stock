{function name=catMenu}
  <ul class="dropdown-menu">
  {foreach $data as $entry}
    <li>
      <a href="/category/{$entry['name']|escape:'htmlall'}">{$entry['name']|escape:'htmlall'|truncate:30}</a>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        <ul>
          {catMenu data=$entry['subcategories']}
        </ul>
      {/if}
    </li>
  {/foreach}
  </ul>
{/function}
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
            <li>
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Locations <span class="caret"></span>
                <ul class="dropdown-menu">
                <li>
                    <a class="pure-menu-link" href="/site/all">All</a>
                </li>
                {foreach from=$sites key=siteid item=site}
                    <li>
                        <a href="/site/{$site|escape:'htmlall'}">{$site|escape:'htmlall'|truncate:30}</a>
                        <ul>
                            {foreach from=$locations[$siteid]['locations'] item=location}
                                {foreach from=$location item=loc}
                                <li>
                                    <a href="/location/{$loc|escape:'htmlall'}">{$loc|escape:'htmlall'|truncate:30}</a>
                                </li>
                                {/foreach}
                            {/foreach}
                        </ul>
                    </li>
                {/foreach}
                </ul>
            </li>
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Categories <span class="caret"></a>
                {catMenu data=$categories}
            </li>

          </ul>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Add <span class="caret"></span>
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
