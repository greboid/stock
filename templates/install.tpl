{if $version == false}
    {assign var=errorText value='Installation'}
{else}
    {assign var=errorText value='Upgrade'}
{/if}

{include file='header.tpl'}
            <div class="content pure-u-1 pure-u-md-5-6">
                <div>
                    <div class="posts">
                        <h1>Install or Upgrade</h1>
                            <header class="post-header">
                                <section class="post">
                            </header>
                            <div class="post-description">
                                <p>There's something wrong with your database structure, if you just upgraded this is normal just run the upgrade, if you've not upgraded however this is probably pretty bad.  You've got two options;fuck it all, lets burn it all to the ground and <a href="/setup/dropandcreate">go from scratch</a> or try running the nice <a href="/setup/dbupgrade">database upgrade</a>.</p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
{include file='footer.tpl'}
