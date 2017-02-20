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
        <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css">
        <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/grids-responsive-min.css">
        <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/buttons-min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
        <link rel="stylesheet" href="/assets/css/main.css">
    </head>
    <body>
            <div class="content pure-u-1 pure-u-md-5-6">
                <div>
                    <div class="posts">
                        <h1>{$errorText} required</h1>
                            <header class="post-header">
                                <section class="post">
                            </header>
                            <div class="post-description">
                                <p>There's something wrong with your database structure, if you just upgraded this is normal just run the upgrade, if you've not upgraded however this is probably pretty bad.  You've got two options:
                                    <ul>
                                        <li>Fuck it all, lets burn it all to the ground and <a href="/setup/dropandcreate">go from scratch</a>.</li>
                                        <li>Try running the nice <a href="/setup/dbupgrade">database upgrade</a>.</li>
                                    </ul>
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel-viewport.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script src="/assets/js/main.js"></script>
    </body>
</html>
