{* $Id$ *}

<ul class="thumbnailCategories">
  {foreach from=$category_thumbnails item=cat}
  <li>
    <div class="thumbnailCategory">
      <div class="illustration">
        <a href="{$cat.URL}">
          <img src="{$cat.TN_SRC}" alt="{$cat.ALT}" title="{'hint_category'|@translate}">
        </a>
      </div>
      <div class="description">
        <h3>
          <a href="{$cat.URL}">{$cat.NAME}</a>
          {$cat.ICON}
        </h3>
        {if isset($cat.INFO_DATES) }
        <p>{$cat.INFO_DATES}</p>
        {/if}
        <p>{$cat.CAPTION_NB_IMAGES}</p>
        <p>{$cat.DESCRIPTION}</p>
      </div>
    </div>
  </li>
  {/foreach}
</ul>

