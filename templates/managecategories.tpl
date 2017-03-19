{function name=catMenu}
  {foreach $data as $entry}
        <tr>
            <form method="post">
                <td class="align-middle">{$entry['name']|escape:'htmlall'}</td>
                <td class="align-middle">{$entry['parentName']|escape:'htmlall'}</td>
                <td class="align-middle">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button"
                                    class="btn btn-primary"
                                    data-toggle="modal"
                                    data-target="#editCategoryModal"
                                    data-categoryID="{$entry['id']}"
                                    data-categoryName="{$entry['name']|escape:'htmlall'}"
                                    data-categoryParent="{$entry['parentName']|escape:'htmlall'}">
                                Edit
                            </button>
                        </span>
                        <span class="input-group-btn">
                            <button
                                    formaction="/delete/category/{$entry['id']}"
                                    class="btn btn-danger"
                                    {if (isset($entry['subcategories'])) || ($allCategoryStock[$entry['id']] > 0)}disabled{/if}>
                                Delete
                            </button>
                        </span>
                    </div>
                </td>
            </form>
        </tr>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        {catMenu data=$entry['subcategories']}
      {/if}
  {/foreach}
  </ul>
{/function}

{assign var="nbsp" value="&nbsp;&nbsp;&nbsp;"}

{function name=pickCatMenu level=0}
  {foreach $data as $entry}
      <option value="{$entry['id']}">{$nbsp|str_repeat:$level}{$entry['name']|escape:'htmlall'|truncate:30}</option>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        {pickCatMenu data=$entry['subcategories'] level=$level+1}
      {/if}
  {/foreach}
{/function}


{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col-8 align-self-center">
                <h1 class="text-center">Manage Categories</h1>
                <table id="categories" class="table table-hover table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Category Name</th>
                            <th class="text-center">Parent</th>
                            <th class="table-actions text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {catMenu data=$categories}
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addCategoryModal">
                    Add Category
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>


<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="col align-self-center">
                    <form method="post" action="/add/category" id="addCategoryForm">
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
                                    {pickCatMenu data=$categories}
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

<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="col align-self-center">
                    <form method="post" action="/edit/category" id="editCategoryForm">
                        <input type="hidden" id="editID" name="editID" value="">
                        <fieldset>
                            <div class="form-group row">
                                <label class="col-2 col-form-label" for="editName">Name</label>
                                <input class="col form-control" id="editName" name="editName" type="text" placeholder="name" required>
                            </div>
                            {if $categories|@count > 0}
                            <div class="form-group row">
                                <label for="editParent" class="col-2 col-form-label">Parent</label>
                                <select class="col form-control" id="editParent" name="editParent">
                                    <option selected=""></option>
                                    {pickCatMenu data=$categories}
                                </select>
                            </div>
                            {/if}
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" form="editCategoryForm" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{include file='footer.tpl'}
