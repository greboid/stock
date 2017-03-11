{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1 class="col offset-2 col-header">Add a site</h1>
                <form method="post">
                    <input type="hidden" id="action" name="action" value="addsite">
                    <fieldset>
                        <div class="form-group row">
                            <label class="col-2 col-for-label" for="name">Name</label>
                            <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                        </div>
                        <div class="form-group row">
                            <button type="submit" class="col offset-2 btn btn-primary btn-block">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
