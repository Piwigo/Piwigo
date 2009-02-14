{* $Id$ *}
{$MENUBAR}
{if !empty($PLUGIN_INDEX_CONTENT_BEFORE)}{$PLUGIN_INDEX_CONTENT_BEFORE}{/if}
<div id="content" class="content">
  <div class="titrePage">
    <ul class="categoryActions">
      {if !empty($image_orders) }
      <li>
      {'Sort order'|@translate}:
      <select onchange="document.location = this.options[this.selectedIndex].value;">
        {foreach from=$image_orders item=image_order }
        <option value="{$image_order.URL}"{if $image_order.SELECTED} selected="selected"{/if}>{$image_order.DISPLAY}</option>
        {/foreach}
      </select>
      </li>
      {/if}

      {if isset($favorite) }
      <li><a href="{$favorite.U_FAVORITE}" title="{'del_all_favorites_hint'|@translate}"><img src="{$favorite.FAVORITE_IMG}" class="button" alt="favorite" title="{'del_all_favorites_hint'|@translate}"></a></li>
      {/if}

      {if isset($U_CADDIE) }
      <li><a href="{$U_CADDIE}" title="{'add to caddie'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/caddie_add.png" class="button" alt="{'caddie'|@translate}"/></a></li>
      {/if}

      {if isset($U_EDIT) }
      <li><a href="{$U_EDIT}" title="{'edit category informations'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/category_edit.png" class="button" alt="{'edit'|@translate}"/></a></li>
      {/if}

      {if isset($U_SEARCH_RULES) }
      <li><a href="{$U_SEARCH_RULES}" onclick="popuphelp(this.href); return false;" title="{'Search rules'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/search_rules.png" class="button" alt="(?)" /></a></li>
      {/if}

      {if isset($U_SLIDESHOW) }
      <li><a href="{$U_SLIDESHOW}" title="{'slideshow'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_slideshow.png" class="button" alt="{'slideshow'|@translate}"/></a></li>
      {/if}

      {if isset($U_MODE_FLAT) }
      <li><a href="{$U_MODE_FLAT}" title="{'mode_flat_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/flat.png" class="button" alt="{'mode_flat_hint'|@translate}" /></a></li>
      {/if}

      {if isset($U_MODE_NORMAL) }
      <li><a href="{$U_MODE_NORMAL}" title="{'mode_normal_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/normal_mode.png" class="button" alt="{'mode_normal_hint'|@translate}" /></a></li>
      {/if}

      {if isset($U_MODE_POSTED) }
      <li><a href="{$U_MODE_POSTED}" title="{'mode_posted_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/calendar.png" class="button" alt="{'mode_posted_hint'|@translate}" /></a></li>
      {/if}
      
      {if isset($U_MODE_CREATED) }
      <li><a href="{$U_MODE_CREATED}" title="{'mode_created_hint'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/calendar_created.png" class="button" alt="{'mode_created_hint'|@translate}" /></a></li>
      {/if}
      
      {if !empty($PLUGIN_INDEX_ACTIONS)}{$PLUGIN_INDEX_ACTIONS}{/if}
    </ul>

  <h2>{$TITLE}</h2>

  {if isset($chronology_views) }
  <div class="calendarViews">{'calendar_view'|@translate}:
    <select onchange="document.location = this.options[this.selectedIndex].value;">
      {foreach from=$chronology_views item=view}
      <option value="{$view.VALUE}"{if $view.SELECTED} selected="selected"{/if}>{$view.CONTENT}</option>
      {/foreach}
    </select>
  </div>
  {/if}

  {if isset($chronology.TITLE) }
  <h2>{$chronology.TITLE}</h2>
  {/if}

  </div> <!-- titrePage -->

{if !empty($PLUGIN_INDEX_CONTENT_BEGIN)}{$PLUGIN_INDEX_CONTENT_BEGIN}{/if}

{if !empty($category_search_results) }
<div style="font-size:16px;margin:10px 16px">{'Category results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
  <em><strong>
  {foreach from=$category_search_results item=res name=res_loop}
  {if !$smarty.foreach.res_loop.first} &mdash; {/if}
  {$res}
  {/foreach}
  </strong></em>
</div>
{/if}

{if !empty($tag_search_results) }
<div style="font-size:16px;margin:10px 16px">{'Tag results for'|@translate} <strong>{$QUERY_SEARCH}</strong> :
  <em><strong>
  {foreach from=$tag_search_results item=res name=res_loop}
  {if !$smarty.foreach.res_loop.first} &mdash; {/if}
  {$res}
  {/foreach}
  </strong></em>
</div>
{/if}

{if isset($FILE_CHRONOLOGY_VIEW) }
{include file=$FILE_CHRONOLOGY_VIEW}
{/if}

{if !empty($CATEGORIES) }{$CATEGORIES}{/if}
{if !empty($THUMBNAILS) }{$THUMBNAILS}{/if}


{if !empty($NAV_BAR) }
<div class="navigationBar">
  {$NAV_BAR}
</div>
{/if}

{if !empty($CONTENT_DESCRIPTION) }
<div class="additional_info">
  {$CONTENT_DESCRIPTION}
</div>
{/if}

{if !empty($PLUGIN_INDEX_CONTENT_END) }{$PLUGIN_INDEX_CONTENT_END}{/if}
</div> <!-- content -->

{if !empty($PLUGIN_INDEX_CONTENT_AFTER)}{$PLUGIN_INDEX_CONTENT_AFTER}{/if}
