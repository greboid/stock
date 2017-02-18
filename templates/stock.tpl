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
                                    <form class="pure-form pure-form-aligned" method="post">
                                        <input type="hidden" id="action" name="action" value="edititem">
                                        <input type="hidden" id="itemid" name="itemid" value="{$id|escape:'htmlall'}">
                                        <input type="hidden" id="siteid" name="site" value="{$siteid|escape:'htmlall'}">
                                        <td>{$item.name|escape:'htmlall'}</td>
                                        <td>{$item.site|escape:'htmlall'}</td>
                                        <td>{$item.location|escape:'htmlall'}</td>
                                        <td><input type="number" name="count" value="{$item.count|escape:'htmlall'}" required  min="0" max="{$max_stock}"></td>
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
