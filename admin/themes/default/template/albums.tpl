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

{footer_script}
const delete_album_with_name = '{'Delete album "%s".'|@translate|escape:javascript}';
const delete_album_with_subs = '{'Delete album "%s" and its %d sub-albums.'|@translate|escape:javascript}'
const has_images_associated_outside = '{"delete album and all %d photos, even the %d associated to other albums"|@translate|escape:javascript}';
const has_images_becomming_orphans = '{'delete album and the %d orphan photos'|@translate|escape:javascript}';
const has_images_recursives = '{'delete only album, not photos'|@translate|escape:javascript}';
{/footer_script}

{combine_script id='albums' load='footer' path='admin/themes/default/js/albums.js'}

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
  <div class="cat-move-info icon-help-circled"> {'Drag and drop to reorder albums'|@translate}</div>
  <div class="albumsFilter"> 
    <span class="icon-search search-icon"></span>
    <span class="icon-cancel search-cancel"></span>
    <input class='search-input' type="text" placeholder="{'Search'|@translate}">
  </div>
  <a class="order-root icon-sitemap"> {'Apply an automatic order to root albums'|@translate} </a>
</div>

<div id="AddAlbum" class="AddAlbumPopIn">
  <div class="AddAlbumPopInContainer">
    <a class="icon-cancel CloseAddAlbum CloseAddAlbum"></a>
    
    <div class="AddIconContainer">
      <span class="AddIcon icon-blue icon-plus-circled"></span>
    </div>
    <div class="AddIconTitle">
      <span>{'Créer un nouvel album à la racine'|translate}</span>
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
          <label for="place-start">{'Placer au début'|translate}</label>
        </div>
        <div class="AddAlbumRadioInput">
          <input type="radio" id="place-end"
          name="position" value="last" {if "last" == {$POS_PREF}} checked {/if}>
          <label for="place-end">{'Placer à la fin'|translate}</label>
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

#AddAlbum, #DeleteAlbum {
  display: none;
}

.AddAlbumPopIn, .DeleteAlbumPopIn{
  position: fixed;
  z-index: 100;
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%;
  overflow: auto; 
  background-color: rgba(0,0,0,0.7);
}

.AddAlbumPopInContainer, .DeleteAlbumPopInContainer{
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

.user-property-input {
  color:#353535;
  background-color:#F3F3F3;
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

.AddIconContainer, .DeleteIconContainer {
  margin-top: 10px;
}

.AddIcon {
  border-radius:50%;
  padding:10px;
  font-size: 2em;
}

.AddIconTitle {
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
.DeleteAlbumSubmit {
  cursor:pointer;
  font-weight:bold;
  color: #3F3E40;
  background-color: #FFA836;
  padding: 10px;
  margin: 20px;
  font-size:1em;
  margin-bottom:0;
}

.DeleteAlbumSubmit {
  background-color: #e74c3c;
  border-radius: 4px;
  color: #fff;
  transition: .1s;
}

.AddAlbumCancel {
  color: #3F3E40;
  font-weight: bold;
  cursor: pointer;
  font-size:1em;
}

.CloseAddAlbum{
  position:absolute;
  right:-40px;
  top:-40px;
  font-size:30px;
}

.AddAlbumPositionSelect {
  display: flex;
  flex-direction: column;
}
.AddAlbumInputContainer, .DeleteAlbumInputContainer {
  width: 100%;
  margin: 15px 0;
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
.AddAlbumRadioInput label,
.deleteAlbumOptions label {
  font-size: 12px;
  font-weight: 600;
}
.AddAlbumFormValidation,
.DeleteAlbumFormValidation {
  display: flex;
  flex-direction: row;
  align-items: baseline;
}
.AddAlbumCancel,
.DeleteAlbumCancel {
  cursor: pointer;
  font-weight: bold;
  color: #3F3E40;
  background-color: #f3f3f3;
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
.DeleteAlbumSubmit {
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
</style>