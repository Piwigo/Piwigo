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
var str_yes_change_parent = "{'Yes change parent anyway'|@translate}";
var str_no_change_parent = "{'No, don\'t move this album here'|@translate}";
var str_root = "{'Root'|@translate}";
var openCat = {$open_cat};
var nb_albums = {$nb_albums};
var light_album_manager = {$light_album_manager};

var x_nb_subcats = "{'%d sub-albums'|@translate}";
var x_nb_images = "{'%d photos'|@translate}";
var x_nb_sub_photos = "{'%d pictures in sub-albums'|@translate}";

var delay_autoOpen = {$delay_before_autoOpen}
{/footer_script}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_css path="themes/default/js/plugins/jqtree.css"}
{combine_script id='jtree' load='footer' path='themes/default/js/plugins/tree.jquery.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}

{footer_script}
const delete_album_with_name = '{'Delete album "%s".'|@translate|escape:javascript}';
const delete_album_with_subs = '{'Delete album "%s" and its %d sub-albums.'|@translate|escape:javascript}'
const has_images_associated_outside = '{"delete album and all %d photos, even the %d associated to other albums"|@translate|escape:javascript}';
const has_images_becomming_orphans = '{'delete album and the %d orphan photos'|@translate|escape:javascript}';
const has_images_recursives = '{'delete only album, not photos'|@translate|escape:javascript}';
const rename_item = '{'Rename "%s"'|@translate|escape:javascript}';

const str_add_album = '{'Add Album'|@translate|escape:javascript}';
const str_edit_album = '{'Edit album'|@translate|escape:javascript}';
const str_add_photo = '{'Add Photos'|@translate|escape:javascript}';
const str_visit_gallery = '{'Visit Gallery'|@translate|escape:javascript}';
const str_sort_order = '{'Automatic sort order'|@translate|escape:javascript}';
const str_delete_album = '{'Delete album'|@translate|escape:javascript}';
const str_root_order = '{'Apply to root albums'|@translate|escape:javascript}';
str_sub_album_order = '{'Apply to direct sub-albums'|@translate|escape:javascript}';
str_album_name_empty = '{'Album name must not be empty'|@translate|escape:javascript}'

const add_album_root_title = '{'Create a new album at root'|@translate|escape:javascript}';
const add_sub_album_of = '{'Create a sub-album of "%s"'|@translate|escape:javascript}';
{/footer_script}

{combine_script id='jquery.tipTip' load='footer' path='themes/default/js/plugins/jquery.tipTip.minified.js'}

{combine_script id='albums' load='footer' path='admin/themes/default/js/albums.js'}

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
          <input type="radio" value="natural_order ASC" name="order">
          {'Album name, 1 &rarr; 5 &rarr; 10 &rarr; 100'|@translate}
        </label>
        
        <label class="font-checkbox">
          <span class="icon-dot-circled"> </span>
          <input type="radio" value="natural_order DESC" name="order">
          {'Album name, 100 &rarr; 10 &rarr; 5 &rarr; 1'|@translate}
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
  <div class="add-album-button">
    <label class="head-button-2 icon-add-album">
      <p>{'Add Album'|@translate}</p>
    </label>
  </div>
  <div class="order-root-button">
    <label class="order-root head-button-2 icon-sort-name-up">
      <p>{'Automatic sort order'|@translate}</p>
    </label>
  </div>
  <div class="cat-move-info icon-help-circled"> {'Drag and drop to reorder albums'|@translate}</div>
</div>

<div id="AddAlbum" class="AddAlbumPopIn">
  <div class="AddAlbumPopInContainer">
    <a class="icon-cancel CloseAddAlbum"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-add-album"></span>
    </div>
    <div class="AddIconTitle">
      <span></span>
    </div>

    <div class="AddAlbumInputContainer">
      <label class="user-property-label AddAlbumLabelUsername">{'Album name'|@translate}
        <input class="user-property-input" />
      </label>
    </div>

    <div class="AddAlbumInputContainer">
      <label class="user-property-label AddAlbumLabelUsername">{'Position'|@translate}

      <div class="AddAlbumPositionSelect">
        <div class="AddAlbumRadioInput">
          <input type="radio" id="place-start"
          name="position" value="first" {if "first" == {$POS_PREF}} checked {/if}>
          <label for="place-start">{'Place first'|translate}</label>
        </div>
        <div class="AddAlbumRadioInput">
          <input type="radio" id="place-end"
          name="position" value="last" {if "last" == {$POS_PREF}} checked {/if}>
          <label for="place-end">{'Place last'|translate}</label>
        </div>
      </div>
    </div>
    

    <div class="AddAlbumErrors icon-cancel">
    </div>

    <div class="AddAlbumFormValidation">
      <div class="AddAlbumSubmit">
        <span>{'Add'|@translate}</span>
      </div>

      <div class="AddAlbumCancel">
        <span>{'Cancel'|@translate}</span>
      </div>
    </div>
  </div>
</div>

<div id="RenameAlbum" class="RenameAlbumPopIn">
  <div class="RenameAlbumPopInContainer">
    <a class="icon-cancel CloseRenameAlbum"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-pencil"></span>
    </div>
    <div class="RenameAlbumTitle">
      <span></span>
    </div>

    <div class="RenameAlbumInputContainer">
      <label class="user-property-label RenameAlbumLabelUsername">{'Rename album'|@translate}
        <input class="user-property-input" />
      </label>
    </div>

    <div class="RenameAlbumErrors icon-cancel">
    </div>

    <div class="RenameAlbumFormValidation">
      <div class="RenameAlbumSubmit">
        <span>{'Yes, rename'|@translate}</span>
      </div>

      <div class="RenameAlbumCancel">
        <span>{'Cancel'|@translate}</span>
      </div>
    </div>
  </div>
</div>

<div id="DeleteAlbum" class="DeleteAlbumPopIn">
  <div class="DeleteAlbumPopInContainer">
    <div class="DeleteIconTitle">
      <span>{'Supprimer l\'album : tatatatattata'|translate}</span>
    </div>

    <div class="DeleteAlbumInputContainer">
      <ul class="deleteAlbumOptions">
        <li id="IMAGES_ASSOCIATED_OUTSIDE"><label class=""><input type="radio" name="photo_deletion_mode" value="force_delete"><span class="innerText"></span></label></li>
        <li id="IMAGES_BECOMING_ORPHAN"><label class=""><input type="radio" name="photo_deletion_mode" value="delete_orphans"><span class="innerText"></span></label></li>
        <li id="IMAGES_RECURSIVE"><label class=""><input type="radio" name="photo_deletion_mode" value="no_delete" checked="checked">{'delete only album, not photos'|translate}</label></li>
      </ul>
    </div>
    

    <div class="DeleteAlbumErrors icon-cancel">
    </div>

    <div class="DeleteAlbumFormValidation">
      <div class="DeleteAlbumSubmit">
        <span>{'Confirm deletion'|translate}</span>
      </div>

      <div class="DeleteAlbumCancel">
        <span>{'Cancel'|translate}</span>
      </div>
    </div>
  </div>
</div>

<div class='tree'> </div>

<style>

.animateFocus {
  position: relative;
  border-left: 4px solid #ff7700;
}

.animateFocus .icon-grip-vertical-solid {
  color: #ff7700;
}

.animateFocus:before {
  content: '';
  width: 100%;
  height: 100%;
  position:absolute;
  top:50%;
  left:50%;
  transform: translate(-50%,-50%);
  animation: ripples .8s ease-out 0.3s  ;
  opacity: 0;
  border-radius: 5px;
}

.jqtree_element {
  transition: 1s;
}

@keyframes ripples {
  0% {
      border: 0px solid #ff7700;
      opacity: 0.7;
  }
  100% {
      border: 20px solid #ff7700;
      opacity: 0;
  }
}

.add-album-button label::before {
  margin-right: 7px;
}

#AddAlbum, #DeleteAlbum, #RenameAlbum {
  display: none;
}

.AddAlbumPopIn, .DeleteAlbumPopIn, .RenameAlbumPopIn{
  position: fixed;
  z-index: 100;
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%;
  overflow: auto; 
  background-color: rgba(0,0,0,0.7);
}

.AddAlbumPopInContainer, .DeleteAlbumPopInContainer, .RenameAlbumPopInContainer{
  display:flex;
  position:absolute;
  left:50%;
  top: 50%;
  transform:translate(-50%, -48%);
  text-align:left;
  padding:20px;
  flex-direction:column;
  border-radius:15px;
  align-items:center;
  width: 270px;
}
.DeleteAlbumPopInContainer {
  width: 40%;
  border-radius: 4px;
  border-top: solid 7px #e74c3c;
}
.RenameAlbumPopInContainer {
  width: auto;
  min-width: 270px;
  max-width: 700px;
}

.user-property-input {
  width: 100%;
  box-sizing:border-box;
  font-size:1.1em;
  padding:8px 16px;
  border:none;
}

.user-property-label {
  color:#A4A4A4;
  font-weight:bold;
  font-size:1.1em;
  margin-bottom:5px;
}

.AddIconContainer, .DeleteIconContainer, .AddIconContainer {
  margin-top: 10px;
}

.AddIcon {
  border-radius:50%;
  padding:10px;
  font-size: 2em;
}

.AddIconTitle, .RenameAlbumTitle {
  font-size:1.4em;
  font-weight:bold;
  margin-bottom:20px;
  margin-top:15px;
  text-align: center;
}
.DeleteIconTitle {
  font-size:1.7em;
  font-weight:bold;
  margin-bottom:10px;
  margin-top:15px;
  text-align: center;
}

.AddAlbumSubmit,
.DeleteAlbumSubmit,
.RenameAlbumSubmit {
  cursor:pointer;
  font-weight:bold;
  padding: 10px;
  margin: 20px;
  font-size:1em;
  margin-bottom:0;
}

.DeleteAlbumSubmit {
  border-radius: 4px;
  transition: .1s;
}

.AddAlbumCancel, .RenameAlbumCancel {
  font-weight: bold;
  cursor: pointer;
  font-size:1em;
}

.CloseAddAlbum, .CloseRenameAlbum {
  position:absolute;
  right:-40px;
  top:-40px;
  font-size:30px;
}

.AddAlbumPositionSelect {
  display: flex;
  flex-direction: column;
}
.AddAlbumInputContainer {
  width: 100%;
  margin: 15px 0;
}
.DeleteAlbumInputContainer {
  width: 70%;
  margin: 15px auto;
}
input[name="position"] {
  margin-right: 5px;
}

.AddAlbumRadioInput {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-top: 10px;
}
.AddAlbumRadioInput label {
  font-size: 12px;
  font-weight: 600;
}
.deleteAlbumOptions label {
  font-size: 13px;
  font-weight: 700;
  display: flex;
  flex-direction: row;
  align-items: center;
}
.AddAlbumFormValidation,
.DeleteAlbumFormValidation,
.RenameAlbumFormValidation {
  display: flex;
  flex-direction: row;
  align-items: baseline;
}
.AddAlbumCancel,
.DeleteAlbumCancel,
.RenameAlbumCancel {
  cursor: pointer;
  font-weight: bold;
  padding: 10px 20px;
  margin: 20px;
  margin-bottom: 20px;
  margin-left: 10px;
  font-size: 1em;
  margin-bottom: 0;
}
.DeleteAlbumCancel {
  background-color: #ecf0f1;
  color: #000;
  border-radius: 4px;
  transition: .1s;
}
.DeleteAlbumCancel:hover {
  background: #bdc3c7;
}
.AddAlbumSubmit,
.DeleteAlbumSubmit,
.RenameAlbumSubmit {
  margin-right: 10px;
  padding: 10px 20px;
}
.DeleteAlbumSubmit:hover {
  background: #c0392b;
}

.deleteAlbumOptions {
  list-style-type: none;
}
.deleteAlbumOptions input{
  margin-right: 5px;
}

.album-add-button-label, .order-root-button-label {
  padding: 10px;
  border-radius: 5px;
  font-weight: bold;
  display: flex;
  align-items: baseline;
  cursor: pointer;
  font-size: 13px;
  height: 18px;
}
.album-add-button-label p,
.order-root-button-label p {
  white-space: nowrap;
  margin: 0 !important;
}

.badge-container {
  position: absolute;
  right: 275px;
}

.badge-container i {
  padding: 2px 6px 2px 4px!important;
  border-radius: 10px !important;
  font-size: 0.85em !important;
  margin: 0 2px !important;
  font-weight: 700;
  font-style: normal;
}

.notClickable {
  pointer-events: none;
  opacity: 0.5;
}

.notClickable:hover {
  cursor: not-allowed;
}

.move-cat-container, .move-cat-container .badge-container i {
  transition: 0.2s;
}

.jqtree-moving .move-cat-container {
  background-color: #ffd7ad;
}

.jqtree-moving .move-cat-container .badge-container i,
.jqtree-moving .move-cat-container .node-icon {
  color: #ffd7ad;
  background-color: #f98100;
}

.dragging .move-cat-container {
  pointer-events: none;
}

.dragging .move-cat-container .move-cat-toogler,
.dragging .move-cat-container .move-cat-action-cont a,
.dragging .move-cat-container .move-cat-title-container{
  pointer-events: all;
}


.last-update {
    display: none;
}

.badge-container:hover .badge-dropdown {
  display: flex;
}

.badge-dropdown {
  position: absolute;
  display: none;
  flex-direction: column;
  right: 50%;
  top: 30px;
  width: max-content;
  border-radius: 10px;
  z-index: 10;
  transform: translateX(48%);
  box-shadow: 0px 3px 3px 1px rgba(0,0,0,0.2);
  padding: 10px 20px;
}

.badge-dropdown:after {
  content: " ";
  position: absolute;
  top: -10px;
  left: 50%;
  transform: rotate(0);
  border-width: 5px;
  border-style: solid;
}

.badge-dropdown span {
  background: transparent;
  font-size: 14px;
  font-weight: 600;
  margin: 5px 0;
}
.badge-dropdown span::before {
  margin: 0 8px 0 0;
  width: 20px;
}

@media (max-width: 1415px) { 
  .badge-container  .last-update {
    display: none;
  }

  .badge-container .nb-sub-photos {
    display: none;
  }
}

@media (max-width: 1300px) { 
  .badge-container {
    display: none;
  }
}

@media (max-width: 1230px) { 
  .badge-container {
    display: none;
  }

  ul.jqtree-tree ul.jqtree_common {
    margin-left: 20px !important;
  }

  .move-cat-title-container {
    max-width: 60%;
  }
}

@media (max-width: 1100px) { 
  .move-cat-title-container {
    max-width: 50%;
  }
}

@media (max-width: 850px) { 
  .move-cat-title-container {
    max-width: 40%;
  }
}

</style>