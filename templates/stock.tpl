{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center col-auto">
                <h1>Stock: {$site|escape:'htmlall'}</h1>
                <form class="form-horizontal" method="post">
                    <table id="stock" class="table table-striped table-hover tablesorter">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Site</th>
                                <th>Location</th>
                                <th>Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$stock key=id item=item}
                                <tr>

                                        <td>{$item.name|escape:'htmlall'}</td>
                                        <td>{$item.site|escape:'htmlall'}</td>
                                        <td>{$item.location|escape:'htmlall'}</td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button
                                                        class="btn btn-sm btn-secondary"
                                                        type="submit"
                                                        formaction="/edit/item/{$id|escape:'htmlall'}{$route}"
                                                        name="countdown"
                                                        value="2"
                                                        {if $item.count < 2} disabled{/if}>
                                                        --
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
                                                        -
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
                                                        +
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
                                                        ++
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
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
