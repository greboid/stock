{include file='header.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <div>
            <div class="posts">
                <h1>Add a category</h1>
                    <div class="post-description">
                        <form class="pure-form pure-form-aligned" method="post">
                            <input type="hidden" id="action" name="action" value="addlocation">
                            <fieldset>
                                <div class="pure-control-group">
                                    <label for="name">Name</label>
                                    <input id="name" name="name" type="text" placeholder="name" required>
                                </div>
                                <div class="pure-control-group">
                                    <label for="site">Parent</label>
                                    <select id="parent" name="parent">
                                        <option selected=""></option>
                                        {foreach from=$categories key=categoryID item=category}
                                            <option value="{$categoryID|escape:'htmlall'}">{$category['name']|escape:'htmlall'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="pure-controls">
                                    <button type="submit" class="pure-button pure-button-primary">Submit</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
{include file='footer.tpl'}
