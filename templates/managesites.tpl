{{ include('header.tpl') }}
{{ include('menu.tpl') }}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col-8 align-self-center">
                <h1 class="text-center">Manage Sites</h1>
                <form method="post">
                    <table id="sites" class="table table-hover table-bordered">
                        <thead class="thead-default">
                            <tr>
                                <th class="text-center">Site Name</th>
                                <th class="text-center"># Locations</th>
                                <th class="table-actions text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {%for siteid, site in sites %}
                                <tr>
                                        <td class="align-middle">{{ site }}</td>
                                        <td class="align-middle">{{ locations[siteid]['locations']|count }}</td>
                                        <td class="align-middle">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary"
                                                        data-toggle="modal"
                                                        data-target="#editSiteModal"
                                                        data-siteid="{{ siteid }}"
                                                        data-siteName="{{ site }}">
                                                            Edit
                                                    </button>
                                                </span>
                                                <span class="input-group-btn">
                                                    <button
                                                        formaction="/delete/site/{$siteid}"
                                                        class="btn btn-danger"{% if locations[siteid]['locations']|count != 0 %} disabled{% endif %}>
                                                            Delete
                                                    </button>
                                                </span>
                                            </div>
                                        </td>
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </form>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addSiteModal">
                    Add Site
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSiteModal" tabindex="-1" role="dialog" aria-labelledby="addSiteModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                                    <label class="col-2 col-for-label" for="addName">Name</label>
                                    <input class="col form-control" id="addName" name="addName" type="text" placeholder="name" required>
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

    <div class="modal fade" id="editSiteModal" tabindex="-1" role="dialog" aria-labelledby="editSiteModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Site</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/edit/site" id="editSiteForm">
                            <input type="hidden" id="editID" name="editID" value="">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-2 col-for-label" for="editName">Name</label>
                                    <input class="col form-control" id="editName" name="editName" type="text" placeholder="name" required>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="editSiteForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{{ include('footer.tpl') }}
