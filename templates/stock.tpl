{assign var="nbsp" value="&nbsp;&nbsp;&nbsp;"}

{function name=catMenu level=0}
  {foreach $data as $entry}
  <option value="{$nbsp|str_repeat:$level}{$entry['name']|escape:'htmlall'}">{$nbsp|str_repeat:$level}{$entry['name']|escape:'htmlall'}</option>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
          {catMenu data=$entry['subcategories'] level=$level+1}
      {/if}
  {/foreach}
{/function}

{include file='header.tpl'}
{include file='menu.tpl'}
    <div class="container-fluid">
        <div class="fs row">
            <div class="col bg-faded sidebar">
            <h1>Filters</h1>
              <form id="itemsearchform" class="input-group">
                        <input type="text" id="itemsearch" class="form-control" placeholder="Filter items">
                        <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="sitesearchform" class="input-group">
                    <select id="sitesearch" class="form-control" multiple style="width: 100%">
                        {foreach $sites as $site}
                            <option>{$site}</option>
                        {/foreach}
                    </select>
                </form>
                <form id="locationsearchform" class="input-group">
                    <select id="locationsearch" class="form-control" multiple style="width: 100%">
                        {foreach from=$locations key=siteID item=site}
                            <optgroup label="{$site['name']|escape:'htmlall'}">
                            {foreach from=$site['locations'] key=locationID item=location}
                                <option value="{$location|escape:'htmlall'}">{$location|escape:'htmlall'}</option>
                            {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
                </form>
                <form id="categorysearchform" class="input-group">
                    <select id="categorysearch" class="form-control" multiple style="width: 100%">
                        {catMenu data=$categories}
                    </select>
                </form>
                <form id="mincountform" class="input-group">
                    <input type="number" id="mincount" class="form-control" placeholder="Minimum count" min="0">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="maxcountform" class="input-group">
                    <input type="number" id="maxcount" class="form-control" placeholder="Maximum count" min="0">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
            </div>
            <main class="col-8">
                <h1>Stock: {$site|escape:'htmlall'}</h1>
                <table id="stock" class="table table-hover table-bordered dataTable">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Item</th>
                            <th class="text-center">Site</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$stock key=id item=item}
                            <tr data-itemid="{$id|escape:'htmlall'}" data-itemmax="{$item.max}" data-itemmin="{$item.min}">
                                <td class="name align-middle">{$item.name|escape:'htmlall'}</td>
                                <td class="align-middle">{$item.site|escape:'htmlall'}</td>
                                <td class="align-middle">{$item.location|escape:'htmlall'}</td>
                                <td class="align-middle">{$item.category|escape:'htmlall'}</td>
                                <td data-order="{$item.count}" data-search="{$item.count}" class="align-middle">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="-2"
                                                {if $item.count < 2} disabled{/if}>
                                                &laquo;
                                            </button>
                                        </span>
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="-1"
                                                {if $item.count == 0} disabled{/if}>
                                                &lsaquo;
                                            </button>
                                        </span>
                                        <input class="{if $item.count <= $item.min}belowmin {/if}itemcount form-control" type="number" name="{$id|escape:'htmlall'}-count" value="{$item.count|escape:'htmlall'}" required min="0" max="{$max_stock}">
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="1"
                                                {if $item.count == $max_stock} disabled{/if}>
                                                &rsaquo;
                                            </button>
                                        </span>
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="2"
                                                {if $item.count > ($max_stock-2)} disabled{/if}>
                                                &raquo;
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </main>
            <div class="col-2">
            </div>
        </div>
    </div>
{include file='footer.tpl'}
