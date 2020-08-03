{footer_script}
var data = {json_encode($album_data)};
var pwg_token = "{$PWG_TOKEN}";
var str_show_sub = "{'Show sub-albums'|@translate}";
var str_hide_sub = "{'Hide sub-albums'|@translate}";
var str_manage_sub_album = "{'Manage sub-albums'|@translate}";
var str_apply_order = "{'Apply an automatic order to sub-albums'|@translate}";
var str_edit = "{'Edit album'|@translate}";
var str_are_you_sure = "{'Album \'%s\' status and his sub-albums will change to private. Are you sure ?'|@translate}";
var str_yes_change_parent = "{'Yes change parent anyway'|@translate}"
var str_no_change_parent = "{'No, don\'t move this album here'|@translate}"
var str_root = "{'Root'|@translate}"
var openCat = {$open_cat};
{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_css path="themes/default/js/plugins/jqtree.css"}
{combine_script id='jtree' load='footer' path='themes/default/js/plugins/tree.jquery.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css"}

{combine_script id='cat_move' load='footer' path='admin/themes/default/js/cat_move.js'}

<div class="titrePage">
  <h2>{'Move albums'|@translate}</h2>
</div>

<div class="waiting-message"> <i class='icon-spin6 animate-spin'> </i> Waiting for Piwigo response...</div>


<div class="cat-move-order-popin">
  <div class="order-popin-container">
    <a class="close-popin icon-cancel" onClick="$('.cat-move-order-popin').fadeOut()"> </a>
    <div class="album-name icon-flow-tree"></div>
    <form action="{$F_ACTION}" method="post">
      <input type="hidden" name="id" value="-1">
      <div class="choice-container">
        <label>
          {'Album name, A &rarr; Z'|@translate}
          <input type="radio" value="name ASC" name="order" checked>
          <span class="order-checkmark"> 
        </label>

        <label>
          {'Album name, Z &rarr; A'|@translate}
          <input type="radio" value="name DESC" name="order">
          <span class="order-checkmark"> 
        </label>

        <label>
          {'Date created, new &rarr; old'|@translate}
          <input type="radio" value="date_creation DESC" name="order">
          <span class="order-checkmark"> 
        </label>

        <label>
          {'Date created, old &rarr; new'|@translate}
          <input type="radio" value="date_creation ASC" name="order">
          <span class="order-checkmark"> 
        </label>

        <label>
          {'Date posted, new &rarr; old'|@translate}
          <input type="radio" value="date_available DESC" name="order">
          <span class="order-checkmark"> 
        </label>

        <label>
          {'Date posted, old &rarr; new'|@translate}
          <input type="radio" value="date_available ASC" name="order">
          <span class="order-checkmark"> 
        </label>
      </div>
      <input type="submit" name="simpleAutoOrder" value="{'Apply to direct sub-albums'|@translate}"/>
      <input type="submit" name="recursiveAutoOrder" value="{'Apply to the whole hierarchy'|@translate}"/>
    </form>
  </div>
</div>

<div class="cat-move-header"> 
  <div class="cat-move-info icon-help-circled"> {'Drag and drop to reorder albums'|@translate}</div>
  <a class="order-root icon-flow-tree"> {'Apply an automatic order to root albums'|@translate} </a>
</div>

<div class='tree'> </div>