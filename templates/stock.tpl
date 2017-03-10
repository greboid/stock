{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
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
                                    <button
                                        class="btn btn-sm"
                                        type="submit"
                                        formaction="/edit/item/{$id|escape:'htmlall'}"
                                        name="countdown"
                                        value="2">
                                        --
                                    </button>
                                    <button
                                        class="btn btn-sm"
                                        type="submit"
                                        formaction="/edit/item/{$id|escape:'htmlall'}"
                                        name="countdown"
                                        value="1">
                                        -
                                    </button>
                                    <input type="number" name="{$id|escape:'htmlall'}-count" value="{$item.count|escape:'htmlall'}" required min="0" max="{$max_stock}">
                                    <button
                                        class="btn btn-sm"
                                        type="submit"
                                        formaction="/edit/item/{$id|escape:'htmlall'}"
                                        name="countup"
                                        value="1">
                                        +
                                    </button>
                                    <button
                                        class="btn btn-sm"
                                        type="submit"
                                        formaction="/edit/item/{$id|escape:'htmlall'}"
                                        name="countup"
                                        value="2">
                                        ++
                                    </button>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-default">Edit</button>
                                </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
                            </form>
        </div>
    </div>
</div>
{include file='footer.tpl'}
