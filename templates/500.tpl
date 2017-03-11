{include file='header.tpl'}
{$msg|default:""}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Page not found</h1>
                <p>Error loading page: <br>{$error|escape:'htmlall'}</p>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
