{assign var="nbsp" value="&nbsp;&nbsp;&nbsp;"}

{function name=catMenu level=0}
  {foreach $data as $entry}
      <option value="{$entry['id']}">{$nbsp|str_repeat:$level}{$entry['name']|escape:'htmlall'|truncate:30}</option>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        {catMenu data=$entry['subcategories'] level=$level+1}
      {/if}
  {/foreach}
{/function}

{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col align-self-center">
                <h1 class="col offset-3 col-header">Add an item</h1>
                <form method="post">
                    <input type="hidden" id="action" name="action" value="additem">
                    <fieldset>
                        <div class="form-group row">
                            <label class="col-3 col-form-label" for="name">Name</label>
                            <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label" for="location">Location</label>
                            <select class="col form-control" id="location" name="location" required>
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
                        <div class="form-group row">
                            <label class="col-3 col-form-label" for="category">Category</label>
                            <select class="col form-control" id="category" name="category" required>
                                <option selected=""></option>
                                {catMenu data=$categories}
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label" for="count">Initial Stock Count</label>
                            <input class="col form-control" id="count" name="count" type="number" placeholder="Initial count" required min="0" max="{$max_stock}">
                        </div>
                        <div class="form-group row">
                            <button type="submit" class="col offset-3 btn btn-primary btn-block">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
