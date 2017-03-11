{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Manage Sites</h1>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Site Name</th>
                            <th># Locations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$sites key=siteid item=site}
                            <tr>
                                <form action="/delete/site/{$siteid}" method="post">
                                    <td>{$site|escape:'htmlall'}</td>
                                    <td>{$locations[$siteid]['locations']|@count}</td>
                                    <td><button class="btn btn-danger"{if $locations[$siteid]['locations']|@count != 0} disabled{/if}>Delete</button></td>
                                </form>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
