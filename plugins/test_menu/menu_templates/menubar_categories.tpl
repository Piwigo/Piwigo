
<!-- categories menu bar -->

<dt>
  {if isset($section.ITEMS.U_START_FILTER)}
  <a href="{$section.ITEMS.U_START_FILTER}" title="{'start_filter_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_filter.png" class="button" alt="start filter"></a>
  {/if}
  {if isset($section.ITEMS.U_STOP_FILTER)}
  <a href="{$section.ITEMS.U_STOP_FILTER}" title="{'stop_filter_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_filter.png" class="button" alt="stop filter"></a>
  {/if}

  <a href="{$section.ITEMS.U_CATEGORIES}">{$section.NAME|@translate}</a>
</dt>
<dd>
  {$section.ITEMS.MENU_CATEGORIES_CONTENT}
  {if isset($section.ITEMS.U_UPLOAD)}
    <ul>
      <li>
        <a href="{$section.ITEMS.U_UPLOAD}">{'upload_picture'|@translate}</a>
      </li>
    </ul>
  {/if}
  <p class="totalImages">{$pwg->l10n_dec('%d element', '%d elements', $section.ITEMS.NB_PICTURE)}</p>
</dd>
