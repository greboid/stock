{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="content pure-u-1 pure-u-md-5-6">
        <div>
            <div class="posts">
                <h1>Add an item</h1>
                    <header class="post-header">
                        <section class="post">
                    </header>
                    <div class="post-description">
                        <form class="pure-form pure-form-aligned" method="post">
                            <input type="hidden" id="action" name="action" value="additem">
                            <fieldset>
                                <div class="pure-control-group">
                                    <label for="name">Name</label>
                                    <input id="name" name="name" type="text" placeholder="name" required>
                                </div>
                                <div class="pure-control-group">
                                    <label for="location">Location</label>
                                    <select id="location" name="location" required>
                                        <option selected=""></option>
                                        {foreach from=$locations key=siteID item=site}
                                            <optgroup label="{$site['name']|escape:'htmlall'}">
                                            {foreach from=$site['locations'] key=locationID item=location}
                                                <option value="{$locationID|escape:'htmlall'}">{$location}</option>
                                            {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="pure-control-group">
                                    <label for="count">Initial Stock Count</label>
                                    <input id="count" name="count" type="number" placeholder="Initial count" required min="0" max="{$max_stock}">
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
