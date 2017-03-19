{include file='header.tpl'}
{include file='menu.tpl'}
  <div class="container-fluid">
      <div class="row">
          <div class="col">
          </div>
          <div class="col align-self-center">
              <h1 class="text-center">Users</h1>
              <form method="post">
                  <table id="users" class="table table-hover table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Username</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Active</th>
                            <th class="table-actions text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$users key=id item=user}
                            <tr>
                              <td class="align-middle">{$user['username']|escape:'htmlall'}</td>
                              <td class="align-middle">{$user['name']|escape:'htmlall'}</td>
                              <td class="align-middle">{$user['email']|escape:'htmlall'}</td>
                              <td class="align-middle">{$user['active']|escape:'htmlall'}</td>
                              <td class="align-middle">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                  <button type="submit" name="userid" id="userid" value={$user['id']}
                                    formaction="/user/sendverification"
                                    class="btn btn-secondary"
                                    {if $username == $user['username']}disabled{/if}>Send Verification</button>
                                  <button type="submit" name="userid" id="userid" value={$user['id']}
                                    formaction="/delete/user"
                                    class="btn btn-danger"
                                    {if $username == $user['username']}disabled{/if}>Delete</button>
                                </div>
                              </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
              </form>
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
                    <label class="col-4 col-form-label" for="username">Username</label>
                    <input class="col form-control" id="username" name="username" type="text" placeholder="Username" required>
                  </div>
                  <div class="form-group row">
                    <label class="col-4 col-form-label" for="name">Name</label>
                    <input class="col form-control" id="name" name="name" type="text" placeholder="Full name" required>
                  </div>
                  <div class="form-group row">
                    <label class="col-4 col-form-label" for="email">Email</label>
                    <input class="col form-control" id="email" name="email" type="email" placeholder="Email Address" required>
                  </div>
                  <div class="row">
                    <label class="col-4 form-check-label">Is user active?</label>
                    <label class="form-check-label mr-2">
                      <input class="form-check-input" type="radio" id="activey" name="active" value="1" checked> Yes
                    </label>
                    <label class="form-check-label">
                      <input class="form-check-input" type="radio" id="activen" name="active" value="0"> No
                    </label>
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
