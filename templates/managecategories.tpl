{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Manage Categories</h1>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$categories key=categoryid item=category}
                        <tr>
                            <form action="/delete/category/{$categoryid}" method="post">
                                <td>{$category['name']|escape:'htmlall'}</td>
                                <td>{$category['parent']|escape:'htmlall'}</td>
                                <td><button class="btn btn-default">Delete</button></td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
{include file='footer.tpl'}
