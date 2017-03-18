{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1>Manage Locations</h1>
                <table id="locations" class="table table-hover">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Location Name</th>
                            <th class="text-center">Site</th>
                            <th class="text-center"># Stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$locationsstockcount key=locationid item=location}
                            <tr>
                                <form action="/delete/location/{$locationsstockcount[$locationid]['id']}" method="post">
                                        <td class="align-middle">{$locationid|escape:'htmlall'}</td>
                                        <td class="align-middle">{$locationsstockcount[$locationid]['sitename']|escape:'htmlall'}</td>
                                        <td class="align-middle">{$locationsstockcount[$locationid]['stockcount']|escape:'htmlall'}</td>
                                        <td class="align-middle"><button class="btn btn-danger"{if $locationsstockcount[$locationid]['stockcount'] != 0} disabled{/if}>Delete</button></td>
                                </form>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addLocationModal">
                    Add Location
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>

    <div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-labelledby="addLocationModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/add/location" id="addLocationForm">
                            <input type="hidden" id="action" name="action" value="addlocation">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label" for="name">Name</label>
                                    <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-for-label" for="site">Site</label>
                                    <select class="col form-control" id="site" name="site" required>
                                        <option selected=""></option>
                                        {foreach from=$sites key=siteID item=site}
                                            <option value="{$siteID|escape:'htmlall'}">{$site|escape:'htmlall'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="addLocationForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{include file='footer.tpl'}
