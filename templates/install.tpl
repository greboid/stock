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
                <p>There's something wrong with your database structure.  An
                attempt to repair this has already been made and failed.  You
                can try to <a href="/setup/dbupgrade">run this again</a> or you
                can <a href="/setup/dropandcreate">delete all the data and
                start again.</a>.  Alternative you can attempt some kind of
                manual database repair.</p>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
