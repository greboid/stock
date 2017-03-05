{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Locations</h1>
            <ul>
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
            </ul>
        </div>
    </div>
</div>
{include file='footer.tpl'}
