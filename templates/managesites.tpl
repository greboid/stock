{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Manage Sites</h1>
                <table id="sites" class="table table-hover">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Site Name</th>
                            <th class="text-center"># Locations</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$sites key=siteid item=site}
                            <tr>
                                <form action="/delete/site/{$siteid}" method="post">
                                    <td class="align-middle">{$site|escape:'htmlall'}</td>
                                    <td class="align-middle">{$locations[$siteid]['locations']|@count}</td>
                                    <td class="align-middle"><button class="btn btn-danger"{if $locations[$siteid]['locations']|@count != 0} disabled{/if}>Delete</button></td>
                                </form>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addSiteModal">
                    Add Site
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSiteModal" tabindex="-1" role="dialog" aria-labelledby="addSiteModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Site</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/add/site" id="addSiteForm">
                            <input type="hidden" id="action" name="action" value="addsite">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-2 col-for-label" for="name">Name</label>
                                    <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="addSiteForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{include file='footer.tpl'}
