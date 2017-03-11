{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Manage Locations</h1>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Location Name</th>
                            <th>Site</th>
                            <th># Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$locationsstockcount key=locationid item=location}
                            <tr>
                                <form action="/delete/location/{$locationsstockcount[$locationid]['id']}" method="post">
                                        <td>{$locationid|escape:'htmlall'}</td>
                                        <td>{$locationsstockcount[$locationid]['sitename']|escape:'htmlall'}</td>
                                        <td>{$locationsstockcount[$locationid]['stockcount']|escape:'htmlall'}</td>
                                        <td><button class="btn btn-danger"{if $locationsstockcount[$locationid]['stockcount'] != 0} disabled{/if}>Delete</button></td>
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
