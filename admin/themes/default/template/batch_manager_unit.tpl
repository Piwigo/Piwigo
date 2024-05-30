{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.sort' load='footer' path='themes/default/js/plugins/jquery.sort.js'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='header' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='jquery.ui.slider' require='jquery.ui' load='header' path='themes/default/js/ui/minified/jquery.ui.slider.min.js'}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}

{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{footer_script}
(function(){
{* <!-- TAGS --> *}
var tagsCache = new TagsCache({
  serverKey: '{$CACHE_KEYS.tags}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

tagsCache.selectize(jQuery('[data-selectize=tags]'), { lang: {
  'Add': '{'Create'|translate}'
}});

{* <!-- DATEPICKER --> *}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker({
    showTimepicker: true,
    cancelButton: '{'Cancel'|translate}'
  });
});

{* <!-- THUMBNAILS --> *}
jQuery("a.preview-box").colorbox( {
	photo: true
});

str_are_you_sure = '{'Are you sure?'|translate|escape:javascript}';
str_yes = '{'Yes, delete'|translate|escape:javascript}';
str_no = '{'No, I have changed my mind'|translate|@escape:'javascript'}';
str_albums_found = '{"<b>%d</b> albums found"|translate|escape:javascript}';
str_album_found = '{"<b>1</b> album found"|translate|escape:javascript}';
str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';
str_orphan = '{'This photo is an orphan'|@translate|escape:javascript}';
str_no_search_in_progress = '{'No search in progress'|@translate|escape:javascript}';
str_already_in_related_cats = '{'This albums is already in related categories list'|translate|escape:javascript}';

{literal}
$('#action-delete-picture').on('click', function() {
  $.confirm({
    title: str_are_you_sure,
    draggable: false,
    titleClass: "groupDeleteConfirm",
    theme: "modern",
    content: "",
    animation: "zoom",
    boxWidth: '30%',
    useBootstrap: false,
    type: 'red',
    animateFromElement: false,
    backgroundDismiss: true,
    typeAnimated: false,
    buttons: {
        confirm: {
          text: str_yes,
          btnClass: 'btn-red',
          action: function () {
            window.location.href = url_delete.replaceAll('amp;', '');
          }
        },
        cancel: {
          text: str_no
        }
    }
  });
})
{/literal}

}());
const strs_privacy = {
  "0" : "{$level_options[8]}",
  "1" : "{$level_options[4]}",
  "2" : "{$level_options[2]}", 
  "3" : "{$level_options[1]}",
  "4" : "{$level_options[0]}",
};

{/footer_script}

{combine_script id='batchManagerUnit' load='footer' require='jquery.ui.effect-blind,jquery.sort' path='admin/themes/default/js/batchManagerUnit.js'}


<form action="{$F_ACTION}" method="POST">
  <div style="margin: 30px 0; display: flex; justify-content: space-between;">
    <div style="margin-left: 22px;">
      {if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}
    </div>
    <div style="margin-right: 21px;" class="pagination-per-page">
      <span style="font-weight: bold;color: unset;">{'photos per page'|@translate} :</span>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
      <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
    </div>
  </div>
  <div style="clear:both"></div>

{if !empty($elements) }
<div><input type="hidden" name="element_ids" value="{$ELEMENT_IDS}"></div>
{foreach from=$elements item=element}
  {footer_script}
  window.related_category_ids_{$element.ID} = {$element.related_category_ids|@json_encode};  
  console.log(related_category_ids_{$element.ID});
  url_delete = '{$element.U_DELETE}';
  id = '{$element.ID}';

  {/footer_script}

{debug}
<fieldset class="elementEdit" id="picture-{$element.ID}" data-image_id="{$element.ID}">
  <div class="media-box">
    <img src="{$element.TN_SRC}" alt="imagename" class="media-box-embed" style="{if $element.FORMAT}width:100%; max-height:100%;{else}max-width:100%; height:100%;{/if}">
    <div class="media-hover">
    <div class='picture-preview-actions'>
    <a class="preview-box icon-zoom-square tiptip" href="{$element.FILE_SRC}" title="Zoom"></a>
    <a class="icon-download tiptip" href="{$element.U_DOWNLOAD}" title="Download"></a>
    <a class="icon-signal tiptip" href="{$element.U_HISTORY}" title="Visit history"></a>
    <a target="_blank" class="icon-pencil tiptip" href="{$element.U_EDIT}" title="{'Edit photo'|@translate}"></a>
    {if !url_is_remote($element.PATH)}
      <a class="icon-arrows-cw tiptip" href="{$element.U_SYNC}" title="{'Synchronize metadata'|@translate}"></a>
      <a class="icon-trash tiptip" title="{'delete photo'|@translate}" id='action-delete-picture'></a>
    {/if}

  </div>
  {if isset($element.U_JUMPTO)}
    <a class="see-out" href="{$element.U_JUMPTO}" >
    <p><i class="icon-left-open"></i>{'Open in gallery'|@translate}</p>
  {else}
    <a class="see-out disabled" href="#" >
    <p class="" title="{'You don\'t have access to this photo'|translate}" ><i class="icon-left-open"></i>{'Open in gallery'|translate}</p>
  {/if}
    </a>
  </div>  
  </div>
   <div class="main-info-container">
    <div class="main-info-block">
    <div class='info-framed-icon' style="margin-right:0px;">
    <i class='icon-picture'></i>
    </div>
      <span class="main-info-title">{$element.FILE}</span>
      <span class="main-info-desc">{$element.DIMENSIONS}</span>
      <span class="main-info-desc">{$element.FILESIZE}</span>
      <span class="main-info-desc">{$element.EXT}</span>

    </div>
    <div class="main-info-block">
      <div class='info-framed-icon' style="margin-right:0px;">
        <span class='icon-calendar'></span>
      </div>
      <span class="main-info-title">{$element.POST_DATE}</span>
      <span class="main-info-desc">{$element.AGE}</span>
      <span class="main-info-desc">{$element.ADDED_BY}</span>
      <span class="main-info-desc">{$element.STATS}</span>
    </div>
  </div>
  <div class="info-container">
    
    <div class="half-line-info-box">
       <strong>{'Title'|@translate}</strong>
       <input type="text" name="name-{$element.id}" value="{$element.NAME}">
    </div>
    
    <div class="calendar-box">
      <strong>{'Creation date'|@translate}</strong>
      <input type="hidden" name="date_creation-{$element.id}" value="{$element.DATE_CREATION}">
      <label class="calendar-input">
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="date_creation-{$element.id}" data-datepicker-unset="date_creation_unset-{$element.id}" readonly>
        <a href="#" class="icon-cancel-circled unset" id="date_creation_unset-{$element.id}"></a>
      </label>
      
      
    </div>
    
    <div class="half-line-info-box">
      <strong>{'Author'|@translate}</strong>
      <input type="text" name="author-{$element.id}" value="{$element.AUTHOR}">
    </div>
    
    <div class="half-line-info-box">
    <div class="privacy-label-container">
    <strong>Qui peut voir ?</strong> <i>Niveau de confidentialité</i>
    </div>
    <select name="level" size="1">
      {html_options options=$level_options selected=$element.level_options_selected}
    </select>
    {* <div class="advanced-filter-item advanced-filter-privacy" >
    <div class="privacy-label-container">
    <strong>{'Who can see this photo?'|@translate}</strong>
    <label class="advanced-filter-item-label" for="privacy-filter" >
    <span class="privacy">{$level_options[$element.LEVEL]}</span>
    </label>
    </div>
    <div class="advanced-filter-item-container">
      <div id="privacy-filter" class="select-bar"></div>
        <div class="slider-bar-wrapper">
            <div class="slider-bar-container privacy-filter-slider" value="{$element.LEVEL_CONVERT}" id="{$element.ID}"></div>
          </div>
      </div>
  </div> *}
    </div>
    
    <div class="full-line-tag-box">
    <strong>{'Tags'|@translate}</strong>
    <select data-selectize="tags" data-value="{$element.TAGS|@json_encode|escape:html}"
    placeholder="{'Type in a search term'|translate}"
    data-create="true" name="tags-{$element.id}[]" multiple></select>

    </div>
    
    <div class="full-line-info-box" id="{$element.ID}">
  <strong>{'Linked albums'|@translate} <span class="linked-albums-badge {if $element.related_categories|@count < 1 } badge-red {/if}"> {$element.related_categories|@count} </span></strong>
    {if $element.related_categories|@count < 1}
      <span class="orphan-photo">{'This photo is an orphan'|@translate}</span>
    {else}
      <span class="orphan-photo"></span>
    {/if}
    <div class="related-categories-container">
    {foreach from=$element.related_categories item=$cat_path key=$key}
    <div class="breadcrumb-item album-listed"><span class="link-path">{$cat_path['name']}</span>{if $cat_path['unlinkable']}<span id={$key} class="icon-cancel-circled remove-item"></span>{else}<span id={$key} class="icon-help-circled help-item tiptip" title="{'This picture is physically linked to this album, you can\'t dissociate them'|translate}"></span>{/if}</div>
    {/foreach}
    </div>
    <div class="breadcrumb-item linked-albums add-item {if $element.related_categories|@count < 1 } highlight {/if}"><span class="icon-plus-circled"></span>{'Add'|translate}</div>
    </div>
    
    <div class="full-line-description-box">
      <strong>{'Description'|@translate}</strong>
      <textarea cols="50" rows="4" name="description" class="description-box" name="description-{$element.id}" id="description-{$element.id}">{$element.DESCRIPTION}</textarea>
    </div>
    <div class="buttonLike save-button"><i class="icon-floppy"></i>{'Submit'|@translate}</div>
  </div>
</fieldset>
{/foreach}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<div class="bottom-save-bar">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  <button class="buttonLike" type="submit" value="{'Submit'|@translate}" name="submit" style="height:40px"><i class="icon-floppy"></i>Save all photos</button>

  {* <span class="buttonLike" type="submit" name="submit"> {'Submit'|@translate}</span> *}
  {* <input type="reset" value="{'Reset'|@translate}"> *}
</div>
{/if}

</form>
{include file='include/album_selector.inc.tpl' 
  title={'Associate to album'|@translate}
  searchPlaceholder={'Search'|@translate}
}
<style>
.selectize-input  .item,
.selectize-input .item.active {
  background-image:none !important;
  background-color: #ffa646 !important;
  border-color: transparent !important;
  color: black !important;
  

  border-radius: 20px !important;
}

.selectize-input .item .remove,
.selectize-input .item .remove {
  background-color: transparent !important;
  border-top-right-radius: 20px !important;
  border-bottom-right-radius: 20px !important;
  color: black !important;
  
  border-left: 1px solid transparent !important;

}
.selectize-input .item .remove:hover,
.selectize-input .item .remove:hover {
  background-color: #ff7700 !important;
}

.selectize-input.items.not-full.has-options,
.selectize-input.items.not-full.has-options.focus.input-active.dropdown-active,
.selectize-input.items.not-full, 
.selectize-input.items.full{
  border: 1px solid #D3D3D3 !important;
}

.breadcrumb-item.add-item.highlight{
  color: #3C3C3C !important;
}

.breadcrumb-item{
  margin: 5px 0 5px 0 !important;
}

.album-listed{
  background-color: #FFFFFF !important;
}

.elementEdit{
  display:flex;
  flex-direction:row;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
  background-color:#FAFAFA;
  padding:0px;
  margin: 1.5em !important;
  border-radius: 4px;
}


.media-box{
  display: flex;
  background-color: #3C3C3C;
  width:33%;
  justify-content: center;
  position: relative;
  border-radius: 4px 0 0 4px;
}

.media-box-embed{
  height: 100%;
  object-fit: contain;
  position: absolute; 
}

.media-hover{
  opacity:0%;
  background-color: #0000009c;
  position: relative;
  height: 100%;
  width: 100%;
}

.media-hover:hover{
  opacity: 100%;
}

.main-info-container{
  display:flex;
  flex-direction:column;
  text-align:center;
  padding:20px;
  row-gap:15px;
  width:200px;
}

.main-info-block{
  display:flex;
  flex-direction:column;
  border: 1px solid #D3D3D3;
  background: #FFF;
  border-radius: 2px;
  flex:1;
  align-items: center;
  justify-content: center;
}

.main-info-icon{
  width:40px;
  height:40px;
  margin-bottom:5px;
  fill: #3C3C3C;
}

.main-info-title{
  color: #000;
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  line-height: normal;
  width:100px;
}

.main-info-desc{
  color: #777;
  text-align: center;
  font-family: "Open Sans";
  font-size: 12px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
  width:100px;
}

.info-container{
  flex:1;
  display:flex;
  flex-direction:row;
  flex-wrap:wrap;
  align-content: flex-start;
  padding: 20px 0px;
  gap: 10px 0px;
  color:#7A7A7A;

}

.half-line-info-box{
  flex: 0 0 calc(50% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
  text-align:left;
  height: 50px;
}

.full-line-info-box{
  flex: 0 0 calc(100% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.full-line-tag-box{
  flex: 0 0 calc(100% - 20px);
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.calendar-box{
  flex: 0 0 calc(50% - 20px);
  height: 50px;
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.full-line-info-box input,
.half-line-info-box input,
.half-line-info-box select{
  display: flex;
  border-radius: 2px;
  padding: 0 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
  flex: 1;
}

.full-line-tag-box select{
  display: flex;
  border-radius: 2px;
  padding: 0 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
}

.calendar-input{
  display: flex;
  border-radius: 2px;
  padding: 0 7px;
  border: 1px solid #D3D3D3;
  background: #FFF;
  align-items: center;
  justify-content: space-between;
  flex: 1;
}

.calendar-box input{
  border:none;
  outline: none;
  height: 90%;
  width: 90%;
}

.full-line-description-box{
  flex: 0 0 calc(100% - 20px);
  min-height: 50px;
  margin: 0px 10px;
  display:flex;
  flex-direction:column;
}

.description-box{
  resize: none;
  border-radius: 2px;
  border: 1px solid #D3D3D3;
  background: #FFF;
}
.full-line-info-box input,
.half-line-info-box input,
.description-box{
  outline: none !important;
}

.save-button{
  margin: 0% 10px;
  margin-top: 20px;
}

.privacy-label-container{
  display: flex;
  flex-direction: row;
  gap: 5px;
}

.privacy-label-container span{
  color: #ffa646;
  font-weight: bold;
}
.bottom-save-bar{
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: #ffffff;
  text-align: right;
  z-index: 4;
  border-top: 1px solid #CCCCCC
}

.bottom-save-bar button{
  margin: 10px 0;
  margin-right: 2%;
}

</style>
