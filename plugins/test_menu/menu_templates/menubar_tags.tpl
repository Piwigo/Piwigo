
<!-- tags menu bar -->
<dt>{$section.NAME|@translate}</dt>
<dd>
  <ul id="menuTagCloud">
    {foreach from=$section.ITEMS item=tag}
    <li>
      {if !empty($tag.U_ADD) }
        <a href="{$tag.U_ADD}"
          title="{$pwg->l10n_dec('%d element are also linked to current tags', '%d elements are also linked to current tags', $tag.counter)}"
          rel="nofollow">
          <img src="{$ROOT_URL}{$themeconf.icon_dir}/add_tag.png" alt="+" />
        </a>
      {/if}
      <a href="{$tag.URL}" class="tagLevel{$tag.level}" title="{'See elements linked to this tag only'|@translate}">{$tag.name}</a>
      </li>
    {/foreach}
  </ul>
</dd>
