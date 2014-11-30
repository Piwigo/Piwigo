{if empty($load_mode)}{$load_mode='footer'}{/if}
{include file='include/colorbox.inc.tpl' load_mode=$load_mode}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='addAlbum' load=$load_mode path='admin/themes/default/js/addAlbum.js'}

<div style="display:none">
  <div id="addAlbumForm">
    <form>
      {'Parent album'|@translate}<br>
      <select name="category_parent"></select>
      <br><br>
      
      {'Album name'|@translate}<br>
      <input name="category_name" type="text" maxlength="255">
      <span id="categoryNameError" style="color:red;">{'The name of an album must not be empty'|translate}</span>
      <br><br><br>
      
      <input type="submit" value="{'Create'|@translate}">
      <span id="albumCreationLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
    </form>
  </div>
</div>
