{function name=catMenu}
  {foreach $data as $entry}
        <tr>
            <form action="/delete/category/{$entry['id']}" method="post">
                <td>{$entry['name']|escape:'htmlall'}</td>
                <td>{$entry['parentName']|escape:'htmlall'}</td>
                <td><button class="btn btn-default">Delete</button></td>
            </form>
        </tr>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        {catMenu data=$entry['subcategories']}
      {/if}
  {/foreach}
  </ul>
{/function}


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
                    {catMenu data=$categories}
                </tbody>
            </table>
        </div>
    </div>
</div>
{include file='footer.tpl'}
