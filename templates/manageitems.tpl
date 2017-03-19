{assign var="nbsp" value="&nbsp;&nbsp;&nbsp;"}

{function name=catMenu level=0}
  {foreach $data as $entry}
      <option value="{$entry['id']}">{$nbsp|str_repeat:$level}{$entry['name']|escape:'htmlall'|truncate:30}</option>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        {catMenu data=$entry['subcategories'] level=$level+1}
      {/if}
  {/foreach}
{/function}

{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1 class="text-center">Manage Items</h1>
                <table id="items" class="table table-hover table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Location Name</th>
                            <th class="text-center">Site</th>
                            <th class="text-center"># Stock</th>
                            <th class="table-actions text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$stock key=id item=item}
                            <tr>
                                <form action="/delete/item/{$id}" method="post">
                                        <td class="align-middle">{$item['name']|escape:'htmlall'}</td>
                                        <td class="align-middle">{$item['location']|escape:'htmlall'}</td>
                                        <td class="align-middle">{$item['site']|escape:'htmlall'}</td>
                                        <td class="align-middle">{$item['count']|escape:'htmlall'}</td>
                                        <td class="align-middle"><button class="btn btn-danger" >Delete</button></td>
                                </form>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addItemModal">
                    Add Item
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/add/item" id="addItemForm">
                            <input type="hidden" id="action" name="action" value="additem">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="name">Name</label>
                                    <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="location">Location</label>
                                    <select class="col form-control" id="location" name="location" required>
                                        <option selected=""></option>
                                        {foreach from=$locations key=siteID item=site}
                                            <optgroup label="{$site['name']|escape:'htmlall'}">
                                            {foreach from=$site['locations'] key=locationID item=location}
                                                <option value="{$locationID|escape:'htmlall'}">{$location}</option>
                                            {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="category">Category</label>
                                    <select class="col form-control" id="category" name="category" required>
                                        <option selected=""></option>
                                        {catMenu data=$categories}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="count">Initial Stock Count</label>
                                    <input class="col form-control" id="count" name="count" type="number" placeholder="Initial count" required min="0" max="{$max_stock}">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="addItemForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{include file='footer.tpl'}
