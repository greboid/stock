{function name=catMenu}
  <ul>
  {foreach $data as $entry}
    <li>
      <a href="/category/{$entry['name']|escape:'htmlall'}">{$entry['name']|escape:'htmlall'|truncate:30}</a>
      {if isset($entry['subcategories']) && $entry['subcategories']|@count > 0}
        <ul>
          {catMenu data=$entry['subcategories']}
        </ul>
      {/if}
    </li>
  {/foreach}
  </ul>
{/function}


{include file='header.tpl'}
{include file='menu.tpl'}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>Categories</h1>
            {catMenu data=$categories}
        </div>
    </div>
</div>
{include file='footer.tpl'}
