{footer_script}
var data = {json_encode($album_data)};
var pwg_token = "{$PWG_TOKEN}";
var str_show_sub = "{'Show sub-albums'|@translate}";
var str_hide_sub = "{'Hide sub-albums'|@translate}";
var str_manage_sub_album = "{'Manage sub-albums'|@translate}";
var str_apply_order_raw = "{'apply automatic sort order'|translate}";
var str_apply_order = str_apply_order_raw.charAt(0).toUpperCase() + str_apply_order_raw.slice(1);
var str_edit = "{'Edit album'|@translate}";
var str_are_you_sure = "{'The status of the album \'%s\' and its sub-albums will change to private. Are you sure?'|@translate}";
var str_yes_change_parent = "{'Yes change parent anyway'|@translate}"
var str_no_change_parent = "{'No, don\'t move this album here'|@translate}"
var str_root = "{'Root'|@translate}"
var openCat = {$open_cat};

var delay_autoOpen = {$delay_before_autoOpen}
{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_css path="themes/default/js/plugins/jqtree.css"}
{combine_script id='jtree' load='footer' path='themes/default/js/plugins/tree.jquery.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

{combine_script id='cat_move' load='footer' path='admin/themes/default/js/cat_move.js'}

<div class="titrePage">
  <h2>{'Move albums'|@translate}</h2>
</div>

<div class="cat-move-order-popin">
  <div class="order-popin-container">
    <a class="close-popin icon-cancel" onClick="$('.cat-move-order-popin').fadeOut()"> </a>
    <div class="popin-title"><span class="icon-sort-name-up icon-purple"></span><span class="popin-title-text">{'apply automatic sort order'|translate}</span></div>
    <div class="album-name icon-sitemap"></div>
    <form action="{$F_ACTION}" method="post">
      <input type="hidden" name="id" value="-1">
      <div class="choice-container">
        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="name ASC" name="order" checked>
          {'Album name, A &rarr; Z'|@translate}
        </label>

        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="name DESC" name="order">
          {'Album name, Z &rarr; A'|@translate}
        </label>

        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="date_creation DESC" name="order">
          {'Date created, new &rarr; old'|@translate}
        </label>

        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="date_creation ASC" name="order">
          {'Date created, old &rarr; new'|@translate}
        </label>

        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="date_available DESC" name="order">
          {'Date posted, new &rarr; old'|@translate}
        </label>

        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="date_available ASC" name="order">
          {'Date posted, old &rarr; new'|@translate}
        </label>
      </div>
      <input type="submit" name="simpleAutoOrder" value="{'Apply to direct sub-albums'|@translate}"/>
      <input type="submit" name="recursiveAutoOrder" value="{'Apply to the whole hierarchy'|@translate}"/>
    </form>
  </div>
</div>

<div class="cat-move-header"> 
  <div class="cat-move-info icon-help-circled"> {'Drag and drop to reorder albums'|@translate}</div>
  <a class="order-root icon-sitemap"> {'Apply an automatic order to root albums'|@translate} </a>
</div>

<div class='tree'> </div>