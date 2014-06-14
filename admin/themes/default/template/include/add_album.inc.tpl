{include file='include/colorbox.inc.tpl'}

{assign var="selectizeTheme" value=($themeconf.name=='roma')|ternary:'dark':'default'}
{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.`$selectizeTheme`.css"}

{combine_script id='addAlbum.js' load='footer' require='jquery.colorbox' path='admin/themes/default/js/addAlbum.js'}

<div style="display:none">
  <div id="addAlbumForm">
    <form>
      {'Parent album'|@translate}<br>
      <select name="category_parent"></select>
      <br><br>
      
      {'Album name'|@translate}<br>
      <input name="category_name" type="text" maxlength="255"> <span id="categoryNameError"></span>
      <br><br><br>
      
      <input type="submit" value="{'Create'|@translate}">
      <span id="albumCreationLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
    </form>
  </div>
</div>
