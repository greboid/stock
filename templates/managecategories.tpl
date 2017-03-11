{function name=catMenu}
  {foreach $data as $entry}
        <tr>
            <form action="/delete/category/{$entry['id']}" method="post">
                <td>{$entry['name']|escape:'htmlall'}</td>
                <td>{$entry['parentName']|escape:'htmlall'}</td>
                <td><button class="btn btn-danger">Delete</button></td>
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
            <div class="col">
            </div>
            <div class="col align-self-center">
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
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addCategoryModal">
                    Add Item
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>


<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="col align-self-center">
                    <form method="post" action="/add/category" id="addCategoryForm">
                        <h1 class="col offset-2 col-header">Add a category</h1>
                        <input type="hidden" id="action" name="action" value="addlocation">
                        <fieldset>
                            <div class="form-group row">
                                <label class="col-2 col-form-label" for="name">Name</label>
                                <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                            </div>
                            {if $categories|@count > 0}
                            <div class="form-group row">
                                <label for="site" class="col-2 col-form-label">Parent</label>
                                <select class="col form-control" id="parent" name="parent">
                                    <option selected=""></option>
                                    {foreach from=$categories key=categoryID item=category}
                                        <option value="{$categoryID|escape:'htmlall'}">{$category['name']|escape:'htmlall'}</option>
                                    {/foreach}
                                </select>
                            </div>
                            {/if}
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addCategoryForm" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{include file='footer.tpl'}
