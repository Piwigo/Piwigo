{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='alternativeView' load='footer' path='admin/themes/default/js/cat_list.js'}
{combine_script id='jquery.cookie' path='themes/default/js/jquery.cookie.js' load='footer'}

{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function(){
  $(".addAlbumHead").click(function () {
    $(".addAlbum input[name=virtual_name]").focus();
  })
});
{/literal}{/footer_script}

<div class="selectedAlbum cat-list-album-path">
  <span class="icon-sitemap selectedAlbum-first">{$CATEGORIES_NAV}</span>
  <div class="AlbumViewSelector">
    <input type="radio" name="layout" class="switchLayout" id="displayCompact" {if $smarty.cookies.pwg_album_manager_view == 'compact'}checked{/if}/><label for="displayCompact"><span class="icon-th-large firstIcon tiptip" title="{'Compact View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout tiptip" id="displayLine" {if $smarty.cookies.pwg_album_manager_view == 'line'}checked{/if}/><label for="displayLine"><span class="icon-th-list tiptip" title="{'Line View'|translate}"></span></label><input type="radio" name="layout" class="switchLayout" id="displayTile" {if $smarty.cookies.pwg_album_manager_view == 'tile' || !$smarty.cookies.pwg_album_manager_view}checked{/if}/><label for="displayTile"><span class="icon-pause lastIcon tiptip" title="{'Tile View'|translate}"></span></label>
  </div>
</div>
{assign var='color_tab' value=["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"]}
<div class="categoryContainer">
  <div class="addAlbum">
    <div class="addAlbumHead">
      <span class="icon-plus-circled icon-blue icon-blue-full"></span>
      <p>{"Add Album"|@translate}
    </div>
    <form action="{$F_ACTION}" method="post">
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <label for="virtual_name">{"Album name"|@translate}</label>
      <input type="text" name="virtual_name" placeholder="{"Album name"|@translate}">
      <button name="submitAdd" type="submit" class="buttonLike">
          <i class="icon-plus"></i> {"Create"|@translate}
        </button>
      <a class="cancelAddAlbum">{"Cancel"|@translate}</a>
    </form>
  </div>
  {if count($categories)}
  {foreach from=$categories item=category}
  <div class="categoryBox{if $category.IS_VIRTUAL} virtual_cat{/if}" id="cat_{$category.ID}">

    <div class="albumTop">
      <div class="albumIcon">
        <span class="
        {if $category.NB_SUB_ALBUMS == 0}icon-folder-open{else}icon-sitemap{/if}
        {$color_tab[$category.ID % 5]}
        "> </span>
      </div>

      <div class="albumTitle">
        {$category.NAME}
      </div>
    </div>
    
    <span class="albumInfos"><p>{$category.NB_PHOTOS|translate_dec:'%d photo':'%d photos'}</p> <p>{$category.NB_SUB_PHOTOS|translate_dec:'%d photo':'%d photos'} {$category.NB_SUB_ALBUMS|translate_dec:'in %d sub-album':'in %d sub-albums'}</p></span>

    <div class="albumActions">
      <a href="{$category.U_EDIT}" class="actionEdit" {*title="{'Edit'|@translate}"*}><span class="icon-pencil tiptip" title="{'Edit'|@translate}"></span><span class="iconLegend">{'Edit'|@translate}</span></a>
      <a href="{$category.U_CHILDREN}" class="actionTitle" {*title="{'sub-albums'|@translate}"*}><span class="icon-sitemap tiptip" title="{'sub-albums'|@translate}"></span><span class="iconLegend">{'sub-albums'|@translate}</span></a>
      <a href="{$category.U_MOVE}" class="actionMove"><span class="icon-move tiptip" title="{'Move'|@translate}"></span><span class="iconLegend">{'Move'|@translate}</span></a>
      {if cat_admin_access($category.ID)}
      <a href="{$category.U_JUMPTO}" class="actionGalery" {*title="{'Visit Gallery'|@translate}"*}><span class="icon-eye tiptip" title="{'Visit Gallery'|@translate}"></span><span class="iconLegend">{'Visit Gallery'|@translate}</span></a>
      {else}
      <span href="{$category.U_JUMPTO}" class="actionGalery" {*title="{'This album is private'|@translate}"*}><span class="icon-eye tiptip" title="{'This album is private'|@translate}"></span><span class="iconLegend">{'Visit Gallery'|@translate}</span></span>
      {/if}
      <a href="{$category.U_ADD_PHOTOS_ALBUM}" class="actionAdd" {*title="{'Add Photos'|@translate}"*}><span class="icon-plus tiptip" title="{'Add Photos'|@translate}"></span><span class="iconLegend">{'Add Photos'|@translate}</span></a>
    </div>
  </div>
    {/foreach}
    {/if}
</div>

<style>


/*
 *  Switch btn between views
 */

 #tabsheet , .selectedAlbum{
   margin: 0 0 10px 0 !important;
 }

.selectedAlbum {
  display: flex;
  flex-direction: row;
  justify-content: space-between;

  padding: 0 34px 0 22px;

  align-items: baseline;
}

.selectedAlbum-first {
  margin-left: 0px;
}

.AlbumViewSelector {
  padding: 7px 0px;
  margin-right: 0px;
  border-radius: 10px;
}

.AlbumViewSelector span {
  border-radius: 0;
  padding: 7px;
}

.addAlbum button {
  white-space: nowrap;
}

/* Should be done with :first-child and :last-child but doesn't work */

.AlbumViewSelector label span.firstIcon{
  border-radius: 7px 0 0 7px;
}

.AlbumViewSelector label span.lastIcon{
  border-radius: 0 7px 7px 0;
}

.icon-th-large, .icon-th-list, .icon-pause {
  padding: 10px;
  font-size: 19px;

  transition: 0.3s;
}

.switchLayout {
  display: none;
}

.albumActions a span.iconLegend {
  font-size: 14px;
}

.categoryContainer {
  padding: 0 20px 0 20px;
}

/*
 *  Tiles display
 */

.tile_add.addAlbum form input::placeholder {
  color: transparent !important;
}

.albumTop {
  display: flex;
  flex-direction: row;

  padding: 0px 20px;
  height: 75px;

  align-items: baseline;
}

.categoryBox, .addAlbum{
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.albumIcon span{
  font-size: 19px;
  width: 27px;
  padding: 10px;
  border-radius: 30px;
}

.albumInfos {
  color: #a9a9a9;

  display: flex;
  flex-direction: column;
  align-items: center;
}

.albumInfos p {
  margin: 0 20px;
  text-align: right;
}

.albumActions {
  display: flex;
  flex-direction: row;
  justify-content: space-between;

  margin-top: auto;

  width: 100%;
  margin-bottom: auto;
}

.albumActions a:first-child{
  margin-left: 35px;
}

.albumActions a:last-child {
  margin-right: 35px;
}

.addAlbum form {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.addAlbum form input {
  border: none;
  border-radius: 5px;

  margin: 0px 10px;

  max-width: 200px;
  width: 75%;
}

.addAlbum form button {
  margin-bottom: 0;
  height: 30px;
}

.addAlbumHead {
  padding: 0;
}

</style>
