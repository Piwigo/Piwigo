{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{html_style}
/*
.adminOnlyIcon::before {
  content:"(";
}
.adminOnlyIcon::after {
  content:")";
}
*/
.adminOnlyIcon {
  background-color:#ddd;
  padding:2px 5px;
  border-radius:5px;
  font-size:90%;
}
{/html_style}

<h2>{'Piwigo configuration'|translate} {$TABSHEET_TITLE}</h2>

<form method="post" action="{$F_ACTION}" class="properties">

<div id="configContent">

  <fieldset id="indexDisplayConf">
    <legend>{'Main Page'|translate}</legend>
    <ul>
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="menubar_filter_icon" {if ($display.menubar_filter_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('display only recently posted photos'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_new_icon" {if ($display.index_new_icon)}checked="checked"{/if}>
          {'Activate icon "new" next to albums and pictures'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_sort_order_input" {if ($display.index_sort_order_input)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Sort order'|translate)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_flat_icon" {if ($display.index_flat_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('display all photos in all sub-albums'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_posted_date_icon" {if ($display.index_posted_date_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('display a calendar by posted date'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_created_date_icon" {if ($display.index_created_date_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('display a calendar by creation date'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_slideshow_icon" {if ($display.index_slideshow_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('slideshow'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_sizes_icon" {if ($display.index_sizes_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Photo sizes'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_edit_icon" {if ($display.index_edit_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Edit album'|translate|@ucfirst)} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="index_caddie_icon" {if ($display.index_caddie_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Add to caddie'|translate|@ucfirst)} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="display_fromto" {if ($display.display_fromto)}checked="checked"{/if}>
          {'Display album date period'|translate}
        </label>
      </li>

      <li>
        <label>
          {'Number of albums per page'|translate}
          <input type="text" size="3" maxlength="4" name="nb_categories_page" id="nb_categories_page" value="{$display.NB_CATEGORIES_PAGE}">
        </label>
      </li>
    </ul>
  </fieldset>

  <fieldset id="pictureDisplayConf">
    <legend>{'Photo Page'|translate}</legend>
    <ul>
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_slideshow_icon" {if ($display.picture_slideshow_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('slideshow'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_metadata_icon" {if ($display.picture_metadata_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Show file metadata'|translate)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_download_icon" {if ($display.picture_download_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Download this file'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_favorite_icon" {if ($display.picture_favorite_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('add this photo to your favorites'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_sizes_icon" {if ($display.picture_sizes_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Photo sizes'|translate|@ucfirst)}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_edit_icon" {if ($display.picture_edit_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Edit photo'|translate|@ucfirst)} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_caddie_icon" {if ($display.picture_caddie_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('Add to caddie'|translate|@ucfirst)} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_representative_icon" {if ($display.picture_representative_icon)}checked="checked"{/if}>
          {'Activate icon "%s"'|translate:('set as album representative'|translate|@ucfirst)} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_navigation_icons" {if ($display.picture_navigation_icons)}checked="checked"{/if}>
          {'Activate Navigation Bar'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_navigation_thumb" {if ($display.picture_navigation_thumb)}checked="checked"{/if}>
          {'Activate Navigation Thumbnails'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_menu" {if ($display.picture_menu)}checked="checked"{/if}>
          {'Show menubar'|translate}
        </label>
      </li>
    </ul>
  </fieldset>

  <fieldset id="pictureInfoConf">
    <legend>{'Photo Properties'|translate}</legend>
    <ul>
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[author]" {if ($display.picture_informations.author)}checked="checked"{/if}>
          {'Author'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[created_on]" {if ($display.picture_informations.created_on)}checked="checked"{/if}>
          {'Created on'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[posted_on]" {if ($display.picture_informations.posted_on)}checked="checked"{/if}>
          {'Posted on'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[dimensions]" {if ($display.picture_informations.dimensions)}checked="checked"{/if}>
          {'Dimensions'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[file]" {if ($display.picture_informations.file)}checked="checked"{/if}>
          {'File'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[filesize]" {if ($display.picture_informations.filesize)}checked="checked"{/if}>
          {'Filesize'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[tags]" {if ($display.picture_informations.tags)}checked="checked"{/if}>
          {'Tags'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[categories]" {if ($display.picture_informations.categories)}checked="checked"{/if}>
          {'Albums'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[visits]" {if ($display.picture_informations.visits)}checked="checked"{/if}>
          {'Visits'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[rating_score]" {if ($display.picture_informations.rating_score)}checked="checked"{/if}>
          {'Rating score'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="picture_informations[privacy_level]" {if ($display.picture_informations.privacy_level)}checked="checked"{/if}>
          {'Who can see this photo?'|translate} <span class="adminOnlyIcon tiptip" title="{'available for administrators only'|translate}"><i class="icon-users"></i> {'administrators'}</span>
        </label>
      </li>
    </ul>
  </fieldset>

</div> <!-- configContent -->

<p class="formButtons">
  <button name="submit" type="submit" class="buttonLike">
    <i class="icon-floppy"></i> {'Save Settings'|@translate}
  </button>
</p>

<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>