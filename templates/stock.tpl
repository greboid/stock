{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <section class="post">
            <header class="post-header">
                <h1>Stock: {$site} </h1>
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
                                        <input type="hidden" id="itemid" name="itemid" value="{$id}">
                                        <input type="hidden" id="siteid" name="site" value="{$siteid}">
                                        <td>{$item.name}</td>
                                        <td>{$item.site}</td>
                                        <td>{$item.location}</td>
                                        <td><input type="number" name="count" value="{$item.count}"></td>
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
