{footer_script}{literal}
jQuery(document).ready(function(){
  jQuery("#activate_comments").change(function(){
    if ($(this).is(':checked')) {
      jQuery("#comments_param_warp").show();
    } else {
      jQuery("#comments_param_warp").hide();
    }
  });

  var targets = {
    'input[name="rate"]' : '#rate_anonymous',
    'input[name="allow_user_registration"]' : '#email_admin_on_new_user',
    'input[name="comments_validation"]' : '#email_admin_on_comment_validation',
    'input[name="user_can_edit_comment"]' : '#email_admin_on_comment_edition',
    'input[name="user_can_delete_comment"]' : '#email_admin_on_comment_deletion',
  };

  for (selector in targets) {
    var target = targets[selector];

    jQuery(target).toggle(jQuery(selector).is(':checked'));

    (function(target){
      jQuery(selector).bind('change', function() {
        jQuery(target).toggle($(this).is(':checked'));
      });
    })(target);
  };
});
{/literal}{/footer_script}

<h2>{'Piwigo configuration'|@translate} {$TABSHEET_TITLE}</h2>

{if !isset($default)}
<form method="post" action="{$F_ACTION}" class="properties"{if isset($watermark)} enctype="multipart/form-data"{/if}>
{/if}
<div id="configContent">
{if isset($main)}
<fieldset id="mainConf">
  <legend></legend>
  <ul>
    <li>
      
        <label for="gallery_title">{'Gallery title'|@translate}</label>
      <br>
      <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{$main.CONF_GALLERY_TITLE}">
    </li>

    <li>
      
        <label for="page_banner">{'Page banner'|@translate}</label>
      <br>
      <textarea rows="5" cols="50" class="description" name="page_banner" id="page_banner">{$main.CONF_PAGE_BANNER}</textarea>
    </li>

    <li>
      <label>
        <input type="checkbox" name="rate" {if ($main.rate)}checked="checked"{/if}>
        {'Allow rating'|@translate}
      </label>
    </li>

    <li id="rate_anonymous">
      <label>
        <input type="checkbox" name="rate_anonymous" {if ($main.rate_anonymous)}checked="checked"{/if}>
        {'Rating by guests'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="allow_user_registration" {if ($main.allow_user_registration)}checked="checked"{/if}>
        {'Allow user registration'|@translate}
      </label>
    </li>

    <li id="email_admin_on_new_user">
      <label>
        <input type="checkbox" name="email_admin_on_new_user" {if ($main.email_admin_on_new_user)}checked="checked"{/if}>
        {'Email admins when a new user registers'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="allow_user_customization" {if ($main.allow_user_customization)}checked="checked"{/if}>
        {'Allow user customization'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="obligatory_user_mail_address" {if ($main.obligatory_user_mail_address)}checked="checked"{/if}>
        {'Mail address is obligatory for all users'|@translate}
      </label>
    </li>

    <li>
      <label>{'Week starts on'|@translate}
      {html_options name="week_starts_on" options=$main.week_starts_on_options selected=$main.week_starts_on_options_selected}</label>
    </li>
    
    <li>
        <label>{'Default photos order'|@translate}</label>
        
        {foreach from=$main.order_by item=order}
        <span class="filter {if $ORDER_BY_IS_CUSTOM}transparent{/if}">          
          <select name="order_by[]" {if $ORDER_BY_IS_CUSTOM}disabled{/if}>
            {html_options options=$main.order_by_options selected=$order}
          </select>
          <a class="removeFilter">{'delete'|@translate}</a>
        </span>
        {/foreach}
        
        {if !$ORDER_BY_IS_CUSTOM}
          <a class="addFilter">{'Add a criteria'|@translate}</a>
        {else}
          <span class="order_by_is_custom">{'You can\'t define a default photo order because you have a custom setting in your local configuration.'|@translate}</span>
        {/if}
    </li>
    
{if !$ORDER_BY_IS_CUSTOM}
{footer_script require='jquery'}
// counters for displaying of addFilter link
fields = {$main.order_by|@count}; max_fields = Math.ceil({$main.order_by_options|@count}/2);

{literal}
function updateAddFilterLink() {
  if (fields >= max_fields) {
    $('.addFilter').css('display', 'none');
  } else {
    $('.addFilter').css('display', '');
  }
}

function updateRemoveFilterTrigger() {
  $(".removeFilter").click(function () {
    $(this).parent('span.filter').remove();
    fields--;
    updateAddFilterLink();
  });
  
  $(".removeFilter").css('display', '');
  $(".filter:first .removeFilter").css('display', 'none');
}

jQuery(document).ready(function () {
  $('.addFilter').click(function() {
    $(this).prev('span.filter').clone().insertBefore($(this));
    $(this).prev('span.filter').children('select[name="order_by[]"]').val('');
    
    fields++;
    updateRemoveFilterTrigger();
    updateAddFilterLink();
  });
  
  updateRemoveFilterTrigger();
  updateAddFilterLink();
});
{/literal}
{/footer_script}
{/if}

    <li>
      <strong>{'Save visits in history for'|@translate}</strong>

      <label>
        <input type="checkbox" name="history_guest" {if ($main.history_guest)}checked="checked"{/if}>
        {'simple visitors'|@translate}
      </label>

      <label>
        <input type="checkbox" name="log" {if ($main.log)}checked="checked"{/if}>
        {'registered users'|@translate}
      </label>

      <label>
        <input type="checkbox" name="history_admin" {if ($main.history_admin)}checked="checked"{/if}>
        {'administrators'|@translate}
      </label>

    </li>
  </ul>
</fieldset>
{/if}

{if isset($comments)}
<fieldset id="commentsConf">
  <legend></legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" name="activate_comments" id="activate_comments"{if ($comments.activate_comments)}checked="checked"{/if}>
        {'Activate comments'|@translate}
      </label>
    </li>
  </ul>
  
  <ul id="comments_param_warp"{if not ($comments.activate_comments)} style="display:none;"{/if}>
    <li>
      <label>
        <input type="checkbox" name="comments_forall" {if ($comments.comments_forall)}checked="checked"{/if}>
        {'Comments for all'|@translate}
      </label>
    </li>

    <li>
      <label>
        {'Number of comments per page'|@translate}
        <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{$comments.NB_COMMENTS_PAGE}">
      </label>
    </li>
    
    <li>
      <label>
        {'Default comments order'|@translate}
        <select name="comments_order">
          {html_options options=$comments.comments_order_options selected=$comments.comments_order}
        </select>
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="comments_validation" {if ($comments.comments_validation)}checked="checked"{/if}>
        {'Validation'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="user_can_edit_comment" {if ($comments.user_can_edit_comment)}checked="checked"{/if}>
        {'Allow users to edit their own comments'|@translate}
      </label>
    </li>
    <li>
      <label>
        <input type="checkbox" name="user_can_delete_comment" {if ($comments.user_can_delete_comment)}checked="checked"{/if}>
        {'Allow users to delete their own comments'|@translate}
      </label>
    </li>

    <li id="notifyAdmin">
      <strong>{'Notify administrators when a comment is'|@translate}</strong>

      <label id="email_admin_on_comment_validation">
        <input type="checkbox" name="email_admin_on_comment_validation" {if ($comments.email_admin_on_comment_validation)}checked="checked"{/if}> {'pending validation'|@translate}
      </label>

      <label>
        <input type="checkbox" name="email_admin_on_comment" {if ($comments.email_admin_on_comment)}checked="checked"{/if}> {'added'|@translate}
      </label>

      <label id="email_admin_on_comment_edition">
        <input type="checkbox" name="email_admin_on_comment_edition" {if ($comments.email_admin_on_comment_edition)}checked="checked"{/if}> {'modified'|@translate}
      </label>

      <label id="email_admin_on_comment_deletion">
        <input type="checkbox" name="email_admin_on_comment_deletion" {if ($comments.email_admin_on_comment_deletion)}checked="checked"{/if}> {'deleted'|@translate}
      </label>
    </li>
  </ul>
</fieldset>
{/if}

{if isset($sizes)}

{footer_script}
var labelMaxWidth = "{'Maximum width'|@translate}";
var labelWidth = "{'Width'|@translate}";

var labelMaxHeight = "{'Maximum height'|@translate}";
var labelHeight = "{'Height'|@translate}";
{literal}
jQuery(document).ready(function(){
  function toggleResizeFields(size) {
    var checkbox = jQuery("#original_resize");
    var needToggle = jQuery("#sizeEdit-original");

    if (jQuery(checkbox).is(':checked')) {
      needToggle.show();
    }
    else {
      needToggle.hide();
    }
  }

  toggleResizeFields("original");
  jQuery("#original_resize").click(function () {toggleResizeFields("original")});

  jQuery("a[id^='sizeEditOpen-']").click(function(){
    var sizeName = jQuery(this).attr("id").split("-")[1];
    jQuery("#sizeEdit-"+sizeName).toggle();
    jQuery(this).hide();
		return false;
  });

  jQuery(".cropToggle").click(function() {
    var labelBoxWidth = jQuery(this).parents('table.sizeEditForm').find('td.sizeEditWidth');
    var labelBoxHeight = jQuery(this).parents('table.sizeEditForm').find('td.sizeEditHeight');

    if (jQuery(this).is(':checked')) {
      jQuery(labelBoxWidth).html(labelWidth);
      jQuery(labelBoxHeight).html(labelHeight);
    }
    else {
      jQuery(labelBoxWidth).html(labelMaxWidth);
      jQuery(labelBoxHeight).html(labelMaxHeight);
    }
  });

  jQuery("#showDetails").click(function() {
    jQuery(".sizeDetails").show();
    jQuery(this).css("visibility", "hidden");
		return false;
  });

});
{/literal}{/footer_script}

{html_style}{literal}
.sizeEnable {width:50px;}
.sizeEditForm {margin:0 0 10px 20px;}
.sizeEdit {display:none;}
#sizesConf table {margin:0;}
.showDetails {padding:0;}
.sizeDetails {display:none;margin-left:10px;}
.sizeEditOpen {margin-left:10px;}
{/literal}{/html_style}

<fieldset id="sizesConf">
  <legend>{'Original Size'|@translate}</legend>

  <div>
    <label for="original_resize">
      <input type="checkbox" name="original_resize" id="original_resize" {if ($sizes.original_resize)}checked="checked"{/if}>
      {'Resize after upload'|@translate}
    </label>
  </div>

  <table id="sizeEdit-original">
    <tr>
      <th>{'Maximum width'|@translate}</th>
      <td>
        <input type="text" name="original_resize_maxwidth" value="{$sizes.original_resize_maxwidth}" size="4" maxlength="4"{if isset($ferrors.original_resize_maxwidth)} class="dError"{/if}> {'pixels'|@translate}
        {if isset($ferrors.original_resize_maxwidth)}<span class="dErrorDesc" title="{$ferrors.original_resize_maxwidth}">!</span>{/if}
      </td>
    </tr>
    <tr>
      <th>{'Maximum height'|@translate}</th>
      <td>
        <input type="text" name="original_resize_maxheight" value="{$sizes.original_resize_maxheight}" size="4" maxlength="4"{if isset($ferrors.original_resize_maxheight)} class="dError"{/if}> {'pixels'|@translate}
        {if isset($ferrors.original_resize_maxheight)}<span class="dErrorDesc" title="{$ferrors.original_resize_maxheight}">!</span>{/if}
      </td>
    </tr>
    <tr>
      <th>{'Image Quality'|@translate}</th>
      <td>
        <input type="text" name="original_resize_quality" value="{$sizes.original_resize_quality}" size="3" maxlength="3"{if isset($ferrors.original_resize_quality)} class="dError"{/if}> %
        {if isset($ferrors.original_resize_quality)}<span class="dErrorDesc" title="{$ferrors.original_resize_quality}">!</span>{/if}
      </td>
    </tr>
  </table>

</fieldset>

<fieldset id="multiSizesConf">
  <legend>{'Multiple Size'|@translate}</legend>

<div class="showDetails">
  <a href="#" id="showDetails"{if $show_details or isset($ferrors)} style="display:none"{/if}>{'show details'|@translate}</a>
</div>

<table style="margin:0">
{foreach from=$derivatives item=d key=type}
  <tr>
    <td>
      <label>
        <span class="sizeEnable">
  {if $d.must_enable}
          &#x2714;
  {else}
          <input type="checkbox" name="d[{$type}][enabled]" {if $d.enabled}checked="checked"{/if}>
  {/if}
        </span>
        {$type|@translate}
      </label>
    </td>

    <td>
      <span class="sizeDetails"{if isset($ferrors)} style="display:inline"{/if}>{$d.w} x {$d.h} {'pixels'|@translate}{if $d.crop}, {'Crop'|@translate|lower}{/if}</span>
    </td>

    <td>
      <span class="sizeDetails"{if isset($ferrors) and !isset($ferrors.$type)} style="display:inline"{/if}>
        <a href="#" id="sizeEditOpen-{$type}" class="sizeEditOpen">{'edit'|@translate}</a>
      </span>
    </td>
  </tr>

  <tr id="sizeEdit-{$type}" class="sizeEdit" {if isset($ferrors.$type)} style="display:block"{/if}>
    <td colspan="3">
      <table class="sizeEditForm">
  {if !$d.must_square}
        <tr>
          <td colspan="2">
            <label>
              <input type="checkbox" class="cropToggle" name="d[{$type}][crop]" {if $d.crop}checked="checked"{/if}>
              {'Crop'|@translate}
            </label>
          </td>
        </tr>
  {/if}

        <tr>
          <td class="sizeEditWidth">{if $d.must_square or $d.crop}{'Width'|@translate}{else}{'Maximum width'|@translate}{/if}</td>
          <td>
            <input type="text" name="d[{$type}][w]" maxlength="4" size="4" value="{$d.w}"{if isset($ferrors.$type.w)} class="dError"{/if}>
            {'pixels'|@translate}
            {if isset($ferrors.$type.w)}<span class="dErrorDesc" title="{$ferrors.$type.w}">!</span>{/if}
          </td>
        </tr>

  {if !$d.must_square}
        <tr>
          <td class="sizeEditHeight">{if $d.crop}{'Height'|@translate}{else}{'Maximum height'|@translate}{/if}</td>
          <td>
            <input type="text" name="d[{$type}][h]" maxlength="4" size="4"  value="{$d.h}"{if isset($ferrors.$type.h)} class="dError"{/if}>
            {'pixels'|@translate}
            {if isset($ferrors.$type.h)}<span class="dErrorDesc" title="{$ferrors.$type.h}">!</span>{/if}
          </td>
        </tr>
  {/if}
				<tr>
				<td>{'Sharpen'|@translate}</td>
				<td>
					<input type="text" name="d[{$type}][sharpen]" maxlength="4" size="4"  value="{$d.sharpen}"{if isset($ferrors.$type.sharpen)} class="dError"{/if}>
					%
					{if isset($ferrors.$type.sharpen)}<span class="dErrorDesc" title="{$ferrors.$type.sharpen}">!</span>{/if}
				</td>
				</tr>
      </table> {* #sizeEdit *}
    </td>
  </tr>
{/foreach}
</table>

<p style="margin:10px 0 0 0;{if isset($ferrors)} display:block;{/if}" class="sizeDetails">
  {'Image Quality'|@translate}
  <input type="text" name="resize_quality" value="{$resize_quality}" size="3" maxlength="3"{if isset($ferrors.resize_quality)} class="dError"{/if}> %
  {if isset($ferrors.resize_quality)}<span class="dErrorDesc" title="{$ferrors.resize_quality}">!</span>{/if}
</p>
<p style="margin:10px 0 0 0;{if isset($ferrors)} display:block;{/if}" class="sizeDetails">
  <a href="{$F_ACTION}&action=restore_settings" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'Reset to default values'|@translate}</a>
</p>

{if !empty($custom_derivatives)}
<fieldset class="sizeDetails"><legend>{'custom'|@translate}</legend><table style="margin:0">
{foreach from=$custom_derivatives item=time key=custom}
<tr><td><label><input type="checkbox" name="delete_custom_derivative_{$custom}"> {'Delete'|@translate} {$custom} ({'Last hit'|@translate}: {$time})</label></td></tr>
{/foreach}
</table></fieldset>
{/if}

</fieldset>
{/if}

{if isset($watermark)}

{footer_script}{literal}
jQuery(document).ready(function() {

  function onWatermarkChange() {
    var val = jQuery("#wSelect").val();
    if (val.length) {
      jQuery("#wImg").attr('src', {/literal}'{$ROOT_URL}'{literal}+val).show();
    }
    else {
      jQuery("#wImg").hide();
    }
  }

  onWatermarkChange();

  jQuery("#wSelect").bind("change", onWatermarkChange);

  if (jQuery("input[name='w[position]']:checked").val() == 'custom') {
    jQuery("#positionCustomDetails").show();
  }

  jQuery("input[name='w[position]']").change(function(){
    if (jQuery(this).val() == 'custom') {
      jQuery("#positionCustomDetails").show();
    }
    else {
      jQuery("#positionCustomDetails").hide();
    }
  });

  jQuery(".addWatermarkOpen").click(function(){
    jQuery("#addWatermark, #selectWatermark").toggle();
		return false;
  });
});
{/literal}{/footer_script}

<fieldset id="watermarkConf">
  <legend></legend>
  <ul>
    <li>
      <span id="selectWatermark"{if isset($ferrors.watermarkImage)} style="display:none"{/if}><label>{'Select a file'|@translate}</label>
      <select name="w[file]" id="wSelect">
	{html_options options=$watermark_files selected=$watermark.file}
      </select>
      {'... or '|@translate}<a href="#" class="addWatermarkOpen">{'add a new watermark'|@translate}</a>
      <br><img id="wImg"></img></span>{* #selectWatermark *}
      <span id="addWatermark"{if isset($ferrors.watermarkImage)} style="display:inline"{/if}>
      {'add a new watermark'|@translate} {'... or '|@translate}<a href="#" class="addWatermarkOpen">{'Select a file'|@translate}</a>
      <br><input type="file" size="60" id="watermarkImage" name="watermarkImage"{if isset($ferrors.watermarkImage)} class="dError"{/if}> (png)
      {if isset($ferrors.watermarkImage)}<span class="dErrorDesc" title="{$ferrors.watermarkImage}">!</span>{/if}
      </span>{* #addWatermark *}
    </li>

    <li>
      <label>
        {'Apply watermark if width is bigger than'|@translate}
        <input  size="4" maxlength="4" type="text" name="w[minw]" value="{$watermark.minw}"{if isset($ferrors.watermark.minw)} class="dError"{/if}>
      </label>
      {'pixels'|@translate}
    </li>

    <li>
      <label>
        {'Apply watermark if height is bigger than'|@translate}
	<input  size="4" maxlength="4" type="text" name="w[minh]" value="{$watermark.minh}"{if isset($ferrors.watermark.minh)} class="dError"{/if}>
      </label>
      {'pixels'|@translate}
    </li>

    <li>
      <label>{'Position'|@translate}</label>
      <br>
      <div id="watermarkPositionBox">
        <label class="right">{'top right corner'|@translate} <input name="w[position]" type="radio" value="topright"{if $watermark.position eq 'topright'} checked="checked"{/if}></label>
        <label><input name="w[position]" type="radio" value="topleft"{if $watermark.position eq 'topleft'} checked="checked"{/if}> {'top left corner'|@translate}</label>
        <label class="middle"><input name="w[position]" type="radio" value="middle"{if $watermark.position eq 'middle'} checked="checked"{/if}> {'middle'|@translate}</label>
        <label class="right">{'bottom right corner'|@translate} <input name="w[position]" type="radio" value="bottomright"{if $watermark.position eq 'bottomright'} checked="checked"{/if}></label>
        <label><input name="w[position]" type="radio" value="bottomleft"{if $watermark.position eq 'bottomleft'} checked="checked"{/if}> {'bottom left corner'|@translate}</label>
      </div>
      <label style="display:block;margin-top:10px;font-weight:normal;"><input name="w[position]" type="radio" value="custom"{if $watermark.position eq 'custom'} checked="checked"{/if}> {'custom'|@translate}</label>
      <div id="positionCustomDetails">
        <label>{'X Position'|@translate}
	  <input size="3" maxlength="3" type="text" name="w[xpos]" value="{$watermark.xpos}"{if isset($ferrors.watermark.xpos)} class="dError"{/if}>%
          {if isset($ferrors.watermark.xpos)}<span class="dErrorDesc" title="{$ferrors.watermark.xpos}">!</span>{/if}
        </label>

        <br>
        <label>{'Y Position'|@translate}
          <input size="3" maxlength="3" type="text" name="w[ypos]" value="{$watermark.ypos}"{if isset($ferrors.watermark.ypos)} class="dError"{/if}>%
          {if isset($ferrors.watermark.ypos)}<span class="dErrorDesc" title="{$ferrors.watermark.ypos}">!</span>{/if}
        </label>

        <br>
        <label>{'X Repeat'|@translate}
          <input size="3" maxlength="3" type="text" name="w[xrepeat]" value="{$watermark.xrepeat}"{if isset($ferrors.watermark.xrepeat)} class="dError"{/if}>
          {if isset($ferrors.watermark.xrepeat)}<span class="dErrorDesc" title="{$ferrors.watermark.xrepeat}">!</span>{/if}
        </label>
      </div>
    </li>

    <li>
      <label>{'Opacity'|@translate}</label>
      <input size="3" maxlength="3" type="text" name="w[opacity]" value="{$watermark.opacity}"{if isset($ferrors.watermark.opacity)} class="dError"{/if}> %
      {if isset($ferrors.watermark.opacity)}<span class="dErrorDesc" title="{$ferrors.watermark.opacity}">!</span>{/if}
    </li>
  </ul>
</fieldset>

{/if} {* end of watermark section *}

{if isset($display)}
<fieldset id="indexDisplayConf">
  <legend>{'Main Page'|@translate}</legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" name="menubar_filter_icon" {if ($display.menubar_filter_icon)}checked="checked"{/if}>
        {'display only recently posted photos'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>    
    
    <li>
      <label>
        <input type="checkbox" name="index_new_icon" {if ($display.index_new_icon)}checked="checked"{/if}>
        {'Activate icon "new" next to albums and pictures'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="index_sort_order_input" {if ($display.index_sort_order_input)}checked="checked"{/if}>
        {'Sort order'|@translate|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="index_flat_icon" {if ($display.index_flat_icon)}checked="checked"{/if}>
        {'display all photos in all sub-albums'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="index_posted_date_icon" {if ($display.index_posted_date_icon)}checked="checked"{/if}>
        {'display a calendar by posted date'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="index_created_date_icon" {if ($display.index_created_date_icon)}checked="checked"{/if}>
        {'display a calendar by creation date'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="index_slideshow_icon" {if ($display.index_slideshow_icon)}checked="checked"{/if}>
        {'slideshow'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>
  </ul>
</fieldset>

<fieldset id="pictureDisplayConf">
  <legend>{'Photo Page'|@translate}</legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" name="picture_slideshow_icon" {if ($display.picture_slideshow_icon)}checked="checked"{/if}>
        {'slideshow'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_metadata_icon" {if ($display.picture_metadata_icon)}checked="checked"{/if}>
        {'Show file metadata'|@translate|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_download_icon" {if ($display.picture_download_icon)}checked="checked"{/if}>
        {'Download this file'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_favorite_icon" {if ($display.picture_favorite_icon)}checked="checked"{/if}>
        {'add this photo to your favorites'|@translate|@ucfirst|@string_format:$pwg->l10n('Activate icon "%s"')}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_navigation_icons" {if ($display.picture_navigation_icons)}checked="checked"{/if}>
        {'Activate Navigation Bar'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_navigation_thumb" {if ($display.picture_navigation_thumb)}checked="checked"{/if}>
        {'Activate Navigation Thumbnails'|@translate}
      </label>
    </li>
    
    <li>
      <label>
        <input type="checkbox" name="picture_menu" {if ($display.picture_menu)}checked="checked"{/if}>
        {'Show menubar'|@translate}
      </label>
    </li>
  </ul>
</fieldset>

<fieldset id="pictureInfoConf">
  <legend>{'Photo Properties'|@translate}</legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" name="picture_informations[author]" {if ($display.picture_informations.author)}checked="checked"{/if}>
        {'Author'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[created_on]" {if ($display.picture_informations.created_on)}checked="checked"{/if}>
        {'Created on'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[posted_on]" {if ($display.picture_informations.posted_on)}checked="checked"{/if}>
        {'Posted on'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[dimensions]" {if ($display.picture_informations.dimensions)}checked="checked"{/if}>
        {'Dimensions'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[file]" {if ($display.picture_informations.file)}checked="checked"{/if}>
        {'File'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[filesize]" {if ($display.picture_informations.filesize)}checked="checked"{/if}>
        {'Filesize'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[tags]" {if ($display.picture_informations.tags)}checked="checked"{/if}>
        {'Tags'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[categories]" {if ($display.picture_informations.categories)}checked="checked"{/if}>
        {'Albums'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[visits]" {if ($display.picture_informations.visits)}checked="checked"{/if}>
        {'Visits'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[rating_score]" {if ($display.picture_informations.rating_score)}checked="checked"{/if}>
        {'Rating score'|@translate}
      </label>
    </li>

    <li>
      <label>
        <input type="checkbox" name="picture_informations[privacy_level]" {if ($display.picture_informations.privacy_level)}checked="checked"{/if}>
        {'Who can see this photo?'|@translate} ({'available for administrators only'|@translate})
      </label>
    </li>
  </ul>
</fieldset>
{/if}

{if !isset($default)}
	<p class="formButtons">
		<input type="submit" name="submit" value="{'Save Settings'|@translate}">
	</p>
</form>
{/if}

</div> <!-- configContent -->

{if isset($default)}
{$PROFILE_CONTENT}
{/if}
