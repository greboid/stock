{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <main class="posts">
            <header class="post-header">
                <h1>Manage Categories</h1>
            </header>
            <section class="post-description">
                <table class="pure-table pure-table-horizontal pure-table-striped">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$categories key=categoryid item=category}
                        <tr>
                            <form action="/delete/category/{$categoryid}" method="post">
                                <td>{$category['name']|escape:'htmlall'}</td>
                                <td>{$category['parent']|escape:'htmlall'}</td>
                                <td><button class="pure-button">Delete</button></td>
                            </form>
                        </tr>
                    {/foreach}
                </tbody>
                </table>
            </section>
        </main>
    </div>
{include file='footer.tpl'}
