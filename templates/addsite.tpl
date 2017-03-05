{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Add a site</h1>
            <form method="post">
                <input type="hidden" id="action" name="action" value="addsite">
                <fieldset>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name" type="text" placeholder="name" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
{include file='footer.tpl'}
