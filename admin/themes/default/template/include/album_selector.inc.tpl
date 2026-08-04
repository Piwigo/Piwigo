{if !$smarty.capture.inc_album_selector}
{capture name="inc_album_selector"}1{/capture}
{if empty($load_mode)}{$load_mode='footer'}{/if}
{include file='include/colorbox.inc.tpl' load_mode=$load_mode}
{combine_css path="admin/themes/default/css/components/album_selector.css"}
{combine_css path="themes/default/vendor/fontello/css/gallery-icon.css" order=-10}
{combine_script id='albumSelector' load=$load_mode path='admin/themes/default/js/album_selector.js'}
{* {combine_script id='albumSelector' load=$load_mode path='admin/themes/default/js/test-ab.js'} *}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{footer_script}
  const str_plus_albums_found = "{'Only the first %d albums are displayed, out of %d.'|@translate|escape:javascript}"
  const str_album_selected = "{'Album already selected'|@translate|escape:javascript}"
  const str_already_in_related_cats = '{'This albums is already in related categories list'|translate|escape:javascript}';
  const str_no_search_in_progress = '{'No search in progress'|@translate|escape:javascript}';
  const str_albums_found = '{"<b>%d</b> albums found"|translate|escape:javascript}';
  const str_album_found = '{"<b>1</b> album found"|translate|escape:javascript}';
  const str_result_limit = '{"<b>%d+</b> albums found, try to refine the search"|translate|escape:javascript}';
  const str_add_subcat_of = '{"Add a sub-album to “%s”"|translate|escape:javascript}';
  const str_create_and_select = '{"Create and select"|translate|escape:javascript}';
  const str_root_album_select = '{"Root"|translate|escape:javascript}';
  const str_complete_name_field = '{"Name field must not be empty"|translate|escape:javascript}';
  const str_an_error_has_occured = '{"An error has occured"|translate|escape:javascript}';
  const str_album_modal_title = '{'Select an album'|@translate|escape:javascript}';
  const str_album_modal_placeholder = '{'Search'|@translate|escape:javascript}';
  const str_root = '{'Root'|@translate|escape:javascript}';
{/footer_script}

<div id="addLinkedAlbum" class="linkedAlbumPopIn">
  <div class="linkedAlbumPopInContainer">
    <a class="gallery-icon-cancel ClosePopIn" id="closeAlbumPopIn"></a>

    <div class="AddIconContainer">
      <span class="AddIcon icon-blue gallery-icon-plus-circled"></span>
    </div>
    <div class="AddIconTitle">
      <span id="linkedModalTitle"></span>
    </div>

    <div class="album-selector" id="linkedAlbumSelector">
      <label class="head-button-2 put-to-root-container notClickable" id="put-to-root">
        <p class="icon-home">{'Put at the root'|@translate}</p>
      </label>
      <p class="put-to-root-container">{'or'|@translate}</p>

      <div id="linkedAlbumSearch">
        <span class='icon-search search-icon'> </span>
        <span class="icon-cancel search-cancel-linked-album" style="display: none;"></span>
        <input class='search-input' type='text' id="search-input-ab" placeholder="">
      </div>
      <div class="limitReached"></div>
      <div class="noSearch"></div>
      <div class="searching gallery-icon-spin6 animate-spin"> </div>
    </div>

    <div id="searchResult">
    </div>

    <div class="album-create" id="linkedAlbumCreate" style="display: none;">
      <div class="linked-album-subtitle">
        <p id="linkedAlbumSubtitle">Add a sub-album of “Album 3”</p>
      </div>

      <div class="linked-album-input-container album-name">
        <label class="user-property-label">{'Album name'|@translate}
          <input class="user-property-input" id="linkedAlbumInput" />
        </label>
      </div>

      <div class="linked-album-input-container">
        <label class="user-property-label">{'Position'|@translate}
          <div class="linked-add-radio-input">
            <input type="radio" id="place-start" name="position" value="first" checked>
            <label for="place-start">{'Place first'|translate}</label>
          </div>
          <div class="linked-add-radio-input">
            <input type="radio" id="place-end" name="position" value="last">
            <label for="place-end">{'Place last'|translate}</label>
          </div>
        </label>
      </div>

      <div class="AddAlbumErrors">
        <p class="icon-cancel" id="linkedAddAlbumErrors">omg big big error</p>
      </div>

      <div class="linked-album-create-btn">
        <p class="linked-button-cancel" id="linkedAlbumCancel">{'Cancel'|translate}</p>
        <p class="buttonLike icon-plus" id="linkedAddNewAlbum">{'Create and select'|translate}</p>
      </div>
    </div>

    <div class="album-switch-bottom" id="linkedAlbumSwitch">
      <div class="album-switch-container">
        <div class="switch-input">
          <label class="switch">
            <input type="checkbox" name="locked" id="album-create-check" value="true">
            <span class="slider round"></span>
          </label>
          <label class="switch-label" for="album-create-check"><span>{'Creation mode'|translate}</span> <i
              class="icon-help-circled tiptip" style="cursor:help"
              title="{"Activate create mode to create and select an album"|translate}"></i></label>
        </div>
        <p class="head-button-2 icon-plus-circled linked-add-album" id="linkedAddAlbum">
          {"Create a root album"|translate}</p>
      </div>
    </div>

  </div>
</div>
{/if}