{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Manage Items</h1>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Location Name</th>
                            <th>Site</th>
                            <th># Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$stock key=id item=item}
                            <tr>
                                <form action="/delete/item/{$id}" method="post">
                                        <td>{$item['name']|escape:'htmlall'}</td>
                                        <td>{$item['location']|escape:'htmlall'}</td>
                                        <td>{$item['site']|escape:'htmlall'}</td>
                                        <td>{$item['count']|escape:'htmlall'}</td>
                                        <td><button class="btn btn-danger" >Delete</button></td>
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
