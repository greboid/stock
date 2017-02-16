<div class="sidebar pure-u-1 pure-u-md-1-6">
    <h1 class="brand-title">Stock</h1>
    <nav class="pure-menu custom-restricted-width">
        <ul class="pure-menu-list">
            <li class="pure-menu-heading">Sites</li>
            {foreach from=$sites key=id item=site}
                <li class="pure-menu-item">
                    <a class="pure-menu-link" href="/?site={$id}">{$site}</a>
                </li>
            {/foreach}
            <li>
                <a class="pure-menu-link" href="/?site=0">All Sites</a>
            </li>
            <li class="pure-menu-heading">Admin</li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/?action=additem">Add Item</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/?action=addlocation">Add Location</a>
            </li>
            <li class="pure-menu-item">
                <a class="pure-menu-link" href="/?action=addsite">Add Site</a>
            </li>
        </ul>
    </nav>
</div>
