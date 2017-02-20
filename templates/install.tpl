{if $version == FALSE}
    {assign var=errorText value='Installation'}
{else}
    {assign var=errorText value='Upgrade'}
{/if}

<!DOCTYPE HTML>
<html>
    <head>
        <title>Stock List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/403.css">
    </head>
    <body>
        <div id="wrapper">
            <p>There's something wrong with your database structure, if you just upgraded this is normal just run the upgrade, if you've not upgraded however this is probably pretty bad.  You've got two options;fuck it all, lets burn it all to the ground and <a href="/setup/dropandcreate">go from scratch</a> or try running the nice <a href="/setup/dbupgrade">database upgrade</a>.</p>
        </div>
    </body>
</html>

