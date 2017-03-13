{include file='header.tpl'}
{include file='menu.tpl'}
  <div class="container-fluid">
      <div class="row">
          <div class="col">
          </div>
          <div class="col align-self-center">
              <h1>Users</h1>
                <table class="table table-striped table-hover">
                  <thead>
                      <tr>
                          <th>Username</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Enabled</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      {foreach from=$users key=id item=user}
                          <tr>
                            <td>{$user['username']|escape:'htmlall'}</td>
                            <td>{$user['name']|escape:'htmlall'}</td>
                            <td>{$user['email']|escape:'htmlall'}</td>
                            <td>{$user['enabled']|escape:'htmlall'}</td>
                            <td><button class="btn btn-danger" >Delete</button></td>
                          </tr>
                      {/foreach}
                  </tbody>
              </table>
              <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addUserModal">
                  Add User
              </button>
          </div>
          <div class="col">
          </div>
      </div>
  </div>

  <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="col align-self-center">
                    <form method="post" action="/add/user" id="addUserForm">
                        <fieldset>
                            <div class="form-group row">
                                <label class="col-3 col-form-label" for="username">Username</label>
                                <input class="col form-control" id="username" name="username" type="text" placeholder="Username" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label" for="name">Name</label>
                                <input class="col form-control" id="name" name="name" type="text" placeholder="Full name" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label" for="email">Email</label>
                                <input class="col form-control" id="email" name="email" type="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-group row">
                              <label class="offset-3 form-check-label">
                              <input type="checkbox" class="form-check-input" checked> User enabled</label>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addUserForm" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{include file='footer.tpl'}
