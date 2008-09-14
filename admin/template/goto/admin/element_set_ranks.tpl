<h2>{'Manage image ranks'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

{if !empty($thumbnails)}
  <form action="{$F_ACTION}" method="post">

  <fieldset>

    <legend>{'Edit ranks'|@translate}</legend>

  {if !empty($thumbnails)}
    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
      <li><span class="wrap1">
          <label>
            <span class="wrap2">
        {if $thumbnail.LEVEL > 0}
        <em class="levelIndicatorB">{$thumbnail.LEVEL}</em>
        <em class="levelIndicatorF" title="{$pwg->l10n($pwg->sprintf('Level %d',$thumbnail.LEVEL))}">{$thumbnail.LEVEL}</em>
        {/if}
            <span>
              <img src="{$thumbnail.TN_SRC}" class="thumbnail" />
            </span></span>
            <input style="height:12px; width:50px;" type="text" name="rank_of_image[{$thumbnail.ID}]" value="{$thumbnail.RANK}" />
          </label>
          </span>
      </li>
      {/foreach}
    </ul>
  {/if}

    <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}/></p>

  </fieldset>

  </form>

{else}
  <div class="infos"><p>{'No element in this category'|@translate}</p></div>
{/if}
