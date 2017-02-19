{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <main class="posts">
            <header class="post-header">
                <h1>Manage Sites</h1>
            </header>
            <section class="post-description">
                <table class="pure-table pure-table-horizontal pure-table-striped">
                <thead>
                    <tr>
                        <th>Site Name</th>
                        <th># Locations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$sites key=siteid item=site}
                        <tr>
                            <form action="/delete/site/{$siteid}" method="post">
                                <td>{$site|escape:'htmlall'}</td>
                                <td>{$locations[$siteid]['locations']|@count}</td>
                                <td><button class="pure-button"{if $locations[$siteid]['locations']|@count != 0} disabled{/if}>Delete</button></td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
                </table>
            </section>
        </main>
    </div>
{include file='footer.tpl'}
