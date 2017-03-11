{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col">
        </div>
        <div class="col align-self-center">
            <form method="post">
                <h1 class="col offset-2 col-header">Add a category</h1>
                <input type="hidden" id="action" name="action" value="addlocation">
                <fieldset>
                    <div class="form-group row">
                        <label class="col-2 col-form-label" for="name">Name</label>
                        <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                    </div>
                    {if $categories|@count > 0}
                    <div class="form-group row">
                        <label for="site" class="col-2 col-form-label">Parent</label>
                        <select class="col form-control" id="parent" name="parent">
                            <option selected=""></option>
                            {foreach from=$categories key=categoryID item=category}
                                <option value="{$categoryID|escape:'htmlall'}">{$category['name']|escape:'htmlall'}</option>
                            {/foreach}
                        </select>
                    </div>
                    {/if}
                    <div class="form-group row">
                        <button type="submit" class="col offset-2 btn btn-default btn-primary btn-block">Submit</button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="col">
        </div>
    </div>
</div>
{include file='footer.tpl'}
