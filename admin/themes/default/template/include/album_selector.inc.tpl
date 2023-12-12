{if empty($load_mode)}{$load_mode='footer'}{/if}
{if !isset($show_root_btn)}{$show_root_btn=false}{/if}

{include file='include/colorbox.inc.tpl' load_mode=$load_mode}
{combine_script id='albumSelector' load_mode=$load_mode path='admin/themes/default/js/album_selector.js'}
{footer_script}
  str_no_search_in_progress = '{'No search in progress'|@translate|escape:javascript}';
  str_albums_found = '{"<b>%d</b> albums found"|translate}';
  str_album_found = '{"<b>1</b> album found"|translate}';

  {if isset($api_method)}
    api_method = '{$api_method}';
  {else}
    api_method = 'pwg.categories.getAdminList';
  {/if}
  
{/footer_script}

<div id="addLinkedAlbum" class="linkedAlbumPopIn">
  <div class="linkedAlbumPopInContainer">
    <a class="icon-cancel ClosePopIn"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-plus-circled"></span>
    </div>
    <div class="AddIconTitle">
      <span>{$title}</span>
    </div>

    {if $show_root_btn}
    <label class="head-button-2 put-to-root">
      <p class="icon-home">{'Put at the root'|@translate}</p>
    </label>
    <p>{'or'|@translate}</p>
    {/if}

    <div id="linkedAlbumSearch">
      <span class='icon-search search-icon'> </span>
      <span class="icon-cancel search-cancel-linked-album"></span>
      <input class='search-input' type='text' placeholder={$searchPlaceholder}>
    </div>
    <div class="limitReached"></div>
    <div class="noSearch"></div>
    <div class="searching icon-spin6 animate-spin"> </div>

    <div id="searchResult">
    </div>
  </div>
</div>