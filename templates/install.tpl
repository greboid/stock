{if $version == false}
    {assign var=errorText value='Installation'}
{else}
    {assign var=errorText value='Upgrade'}
{/if}

{include file='header.tpl'}
{$msg|default:""}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Install or Upgrade</h1>
                <p>
                    There's something wrong with your database structure, if you
                    just upgraded this is normal just run the upgrade, if you've
                    not upgraded however this is probably pretty bad.  You've
                    got two options;fuck it all, lets burn it all to the ground
                    and <a href="/setup/dropandcreate">go from scratch</a> or
                    try running the nice <a href="/setup/dbupgrade">database
                    upgrade</a>.
                </p>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
