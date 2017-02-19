{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <main class="posts">
            <header class="post-header">
                <h1>Manage Locations</h1>
            </header>
            <section class="post-description">
                <table class="pure-table pure-table-stripped">
                <thead>
                    <tr>
                        <th>Location Name</th>
                        <th>Site</th>
                        <th># Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$locationsstockcount key=locationid item=location}
                        <form action="/delete/location/{$locationsstockcount[$locationid]['id']}" method="post">
                            <tr>
                                <td>{$locationid|escape:'htmlall'}</td>
                                <td>{$locationsstockcount[$locationid]['sitename']|escape:'htmlall'}</td>
                                <td>{$locationsstockcount[$locationid]['stockcount']|escape:'htmlall'}</td>
                                <td><button class="pure-button" >Delete</button></td>
                            </tr>
                        </form>
                    {/foreach}
                </tbody>
                </table>
            </section>
        </main>
    </div>
{include file='footer.tpl'}
