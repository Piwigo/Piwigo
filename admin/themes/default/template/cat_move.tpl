{footer_script}
var data = {json_encode($album_data)};
var pwg_token = "{$PWG_TOKEN}";
var str_show_sub = "{'Show sub-albums'|@translate}";
var str_hide_sub = "{'Hide sub-albums'|@translate}";
var str_manage_sub_album = "{'Manage sub-albums'|@translate}";
var str_apply_order = "{'Apply an automatic order to sub-albums'|@translate}";
var str_edit = "{'Edit album'|@translate}";
var str_are_you_sure = "{'Album \'%s\' status will change to private'}"
var str_yes_change_parent = "{'Yes, change parent anyway'|@translate}"
var str_no_change_parent = "{"No, I have changed my mind"|@translate}"
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

<div class="cat-move-header"> 
  <div class="cat-move-info icon-help-circled"> {'Drag and drop to reorder albums'|@translate}</div>
  <a class="order-root icon-flow-tree"> {'Apply an automatic order to root albums'|@translate} </a>
</div>

<div class='tree'> </div>