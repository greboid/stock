{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Add a category</h1>
            <form method="post">
                <input type="hidden" id="action" name="action" value="addlocation">
                <fieldset>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name" type="text" placeholder="name" required>
                    </div>
                    {if $categories|@count > 0}
                    <div class="form-group">
                        <label for="site">Parent</label>
                        <select class="form-control" id="parent" name="parent">
                            <option selected=""></option>
                            {foreach from=$categories key=categoryID item=category}
                                <option value="{$categoryID|escape:'htmlall'}">{$category['name']|escape:'htmlall'}</option>
                            {/foreach}
                        </select>
                    </div>
                    {/if}
                    <div class="">
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
{include file='footer.tpl'}
