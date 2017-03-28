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
                <h1 class="text-center">Install or Upgrade</h1>
                <p>There's something wrong with your database structure.  An
                attempt to repair this has already been made and failed.  You
                will need to repair this manually or contact the developers
                for assistance.</p>
                <p>
                    Details: {$error}
                </p>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
