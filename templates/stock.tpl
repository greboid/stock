{extends file="base.tpl"}
{block name="content"}
    <div class="container-fluid">
        <div class="fs row">
            <div class="col bg-faded sidebar">
            <h1>Filters</h1>
              <form id="itemsearchform" class="input-group">
                        <input type="text" id="itemsearch" class="form-control" placeholder="Filter items">
                        <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="sitesearchform" class="input-group">
                    <input type="text" id="sitesearch" class="form-control" placeholder="Filter sites">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="locationsearchform" class="input-group">
                    <input type="text" id="locationsearch" class="form-control" placeholder="Filter locations">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="mincountform" class="input-group">
                    <input type="text" id="mincount" class="form-control" placeholder="Minimum count">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="maxcountform" class="input-group">
                    <input type="text" id="maxcount" class="form-control" placeholder="Maximum count">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
            </div>
            <main class="col-8">
                <h1>Stock: {$site|escape:'htmlall'}</h1>
                <form class="form-horizontal" method="post">
                    <table id="stock" class="table table-hover table-bordered dataTable">
                        <thead class="thead-default">
                            <tr>
                                <th class="text-center">Item</th>
                                <th class="text-center">Site</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Count</th>
                                <th class="table-actions text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$stock key=id item=item}
                                <tr>
                                    <td class="name align-middle">{$item.name|escape:'htmlall'}</td>
                                    <td class="align-middle">{$item.site|escape:'htmlall'}</td>
                                    <td class="align-middle">{$item.location|escape:'htmlall'}</td>
                                    <td data-order="{$item.count}" data-search="{$item.count}" class="align-middle">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button
                                                    class="btn btn-sm btn-secondary"
                                                    type="submit"
                                                    formaction="/edit/item/{$id|escape:'htmlall'}{$route}"
                                                    name="countdown"
                                                    value="2"
                                                    {if $item.count < 2} disabled{/if}>
                                                    &laquo;
                                                </button>
                                            </span>
                                            <span class="input-group-btn">
                                                <button
                                                    class="btn btn-sm btn-secondary"
                                                    type="submit"
                                                    formaction="/edit/item/{$id|escape:'htmlall'}{$route}"
                                                    name="countdown"
                                                    value="1"
                                                    {if $item.count == 0} disabled{/if}>
                                                    &lsaquo;
                                                </button>
                                            </span>
                                            <input class="form-control" type="number" name="{$id|escape:'htmlall'}-count" value="{$item.count|escape:'htmlall'}" required min="0" max="{$max_stock}">
                                            <span class="input-group-btn">
                                                <button
                                                    class="btn btn-sm btn-secondary"
                                                    type="submit"
                                                    formaction="/edit/item/{$id|escape:'htmlall'}{$route}"
                                                    name="countup"
                                                    value="1"
                                                    {if $item.count == $max_stock} disabled{/if}>
                                                    &rsaquo;
                                                </button>
                                            </span>
                                            <span class="input-group-btn">
                                                <button
                                                    class="btn btn-sm btn-secondary"
                                                    type="submit"
                                                    formaction="/edit/item/{$id|escape:'htmlall'}{$route}"
                                                    name="countup"
                                                    value="2"
                                                    {if $item.count > ($max_stock-2)} disabled{/if}>
                                                    &raquo;
                                                </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="submit" formaction="/edit/item/{$id|escape:'htmlall'}{$route}" class="btn btn-default btn-primary">Edit</button>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </form>
            </main>
            <div class="col-2">
            </div>
        </div>
    </div>
{/block}
