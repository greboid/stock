{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Stock: {$site|escape:'htmlall'}</h1>
            <table class="table table-striped table-hover">
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
                            <form class="form-horizontal" action="/edit/item/{$id|escape:'htmlall'}" method="post">
                                <td>{$item.name|escape:'htmlall'}</td>
                                <td>{$item.site|escape:'htmlall'}</td>
                                <td>{$item.location|escape:'htmlall'}</td>
                                <td>
                                    <button class="btn btn-sm" type="submit" id="countdown" name="countdown" value="2"{if $item.count < 2} disabled{/if}>--</button>
                                    <button class="btn btn-sm" type="submit" id="countdown" name="countdown" value="1"{if $item.count == 0} disabled{/if}>-</button>
                                    <input type="number" name="count" value="{$item.count|escape:'htmlall'}" required min="0" max="{$max_stock}">
                                    <button class="btn btn-sm" type="submit" id="countup" name="countup" value="1"{if $item.count == $max_stock} disabled{/if}>+</button>
                                    <button class="btn btn-sm" type="submit" id="countup" name="countup" value="2"{if $item.count > ($max_stock-2)} disabled{/if}>++</button>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-default">Edit</button>
                                </td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
{include file='footer.tpl'}
