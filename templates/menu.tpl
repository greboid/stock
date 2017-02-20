<div class="sidebar pure-u-1 pure-u-md-1-6">
    <h1 class="brand-title">Stock</h1>
    <nav class="pure-menu custom-restricted-width">
        <ul class="pure-menu-list">
            <li class="pure-menu-heading">Locations</li>
            <li>
                <a class="pure-menu-link" href="/site/all">All</a>
            </li>

            {foreach from=$sites key=siteid item=site}
                <li class="pure-menu-item">
                    <a class="pure-menu-link" href="/site/{$site|escape:'htmlall'}">{$site|escape:'htmlall'|truncate:30}</a>
                    <ul>
                        {foreach from=$locations[$siteid]['locations'] item=location}
                            {foreach from=$location item=loc}
                            <li>
                                <a class="pure-menu-link" href="/location/{$loc|escape:'htmlall'}">{$loc|escape:'htmlall'|truncate:30}</a>
                            </li>
                            {/foreach}
                        {/foreach}
                    </ul>
                </li>
            {/foreach}
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
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/add/category">Add Category</a>
            </li>
            <li class="pure-menu-heading">Manage</li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/manage/sites">Manage Sites</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/manage/locations">Manage Locations</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/manage/items">Manage Items</a>
            </li>
        </ul>
    </nav>
</div>
