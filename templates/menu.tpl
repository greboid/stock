<div class="sidebar pure-u-1 pure-u-md-1-6">
    <h1 class="brand-title">Stock</h1>
    <nav class="pure-menu custom-restricted-width">
        <ul class="pure-menu-list">
            <li class="pure-menu-heading">Sites</li>
            {foreach from=$sites key=id item=site}
                <li class="pure-menu-item">
                    <a class="pure-menu-link" href="/site/{$site|escape:'htmlall'}">{$site|escape:'htmlall'|truncate:30}</a>
                </li>
            {/foreach}
            <li>
                <a class="pure-menu-link" href="/site/all">All Sites</a>
            </li>
            <li class="pure-menu-heading">Add</li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/add/item">Add Item</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/add/location">Add Location</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/add/site">Add Site</a>
            </li>
            <li class="pure-menu-heading">Manage</li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/manage/sites">Manage Sites</a>
            </li>
        </ul>
    </nav>
</div>
