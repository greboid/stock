{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <section class="post">
            <header class="post-header">
                <h1>Stock: {$site|escape:'htmlall'} </h1>
            </header>
            <table class="pure-table pure-table-horizontal pure-table-striped">
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
                            <form class="pure-form pure-form-aligned" action="/edit/item/{$id|escape:'htmlall'}" method="post">
                                <td>{$item.name|escape:'htmlall'}</td>
                                <td>{$item.site|escape:'htmlall'}</td>
                                <td>{$item.location|escape:'htmlall'}</td>
                                <td>
                                    <button type="submit" id="countdown" name="countdown" value="2"{if $item.count == 1} disabled{/if}>--</button>
                                    <button type="submit" id="countdown" name="countdown" value="1"{if $item.count == 0} disabled{/if}>-</button>
                                    <input type="number" name="count" value="{$item.count|escape:'htmlall'}" required" min="0" max="{$max_stock}">
                                    <button type="submit" id="countup" name="countup" value="1"{if $item.count == $max_stock} disabled{/if}>+</button>
                                    <button type="submit" id="countup" name="countup" value="2"{if $item.count > ($max_stock-2)} disabled{/if}>++</button>
                                </td>
                                <td>
                                    <button type="submit" class="pure-button pure-button-success pure-button-xsmall">Edit</button>
                                </td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </section>
    </div>
{include file='footer.tpl'}
