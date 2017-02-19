{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <main class="posts">
            <header class="post-header">
                <h1>Manage Items</h1>
            </header>
            <section class="post-description">
                <table class="pure-table pure-table-horizontal pure-table-striped">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Location Name</th>
                        <th>Site</th>
                        <th># Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$stock key=id item=item}
                        <tr>
                            <form action="/delete/item/{$id}" method="post">
                                    <td>{$item['name']|escape:'htmlall'}</td>
                                    <td>{$item['location']|escape:'htmlall'}</td>
                                    <td>{$item['site']|escape:'htmlall'}</td>
                                    <td>{$item['count']|escape:'htmlall'}</td>
                                    <td><button class="pure-button" >Delete</button></td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
                </table>
            </section>
        </main>
    </div>
{include file='footer.tpl'}
