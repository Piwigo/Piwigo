{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
(function(){
  var labelMaxWidth = "{'Maximum width'|translate}",
      labelWidth = "{'Width'|translate}",
      labelMaxHeight = "{'Maximum height'|translate}",
      labelHeight = "{'Height'|translate}";

  function toggleResizeFields(size) {
    var checkbox = jQuery("[name=original_resize]");
    var needToggle = jQuery("#sizeEdit-original");

    if (jQuery(checkbox).is(':checked')) {
      needToggle.show();
    }
    else {
      needToggle.hide();
    }
  }

  toggleResizeFields("original");
  jQuery("[name=original_resize]").click(function () {
    toggleResizeFields("original");
  });

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
}());
{/footer_script}

{html_style}
.sizeEnable { width:50px; }
.sizeEnable .icon-ok { position:relative; left:2px; }
.sizeEditForm { margin:0 0 10px 20px; }
.sizeEdit { display:none; }
#sizesConf table { margin:0; }
.showDetails { padding:0; }
.sizeDetails { display:none;margin-left:10px; }
.sizeEditOpen { margin-left:10px; }
{/html_style}

<h2>{'Piwigo configuration'|translate} {$TABSHEET_TITLE}</h2>

<form method="post" action="{$F_ACTION}" class="properties">

<div id="configContent">

  <fieldset id="sizesConf">
    <legend>{'Original Size'|translate}</legend>
  {if $is_gd}
    <div>
      {'Resize after upload disabled due to the use of GD as graphic library'|translate}
      <input type="checkbox" name="original_resize"disabled="disabled" style="visibility: hidden">
      <input type="hidden" name="original_resize_maxwidth" value="{$sizes.original_resize_maxwidth}">
      <input type="hidden" name="original_resize_maxheight" value="{$sizes.original_resize_maxheight}">
      <input type="hidden" name="original_resize_quality" value="{$sizes.original_resize_quality}">
    </div>
  {else}
    <div>
      <label class="font-checkbox">
        <span class="icon-check"></span>
        <input type="checkbox" name="original_resize" {if ($sizes.original_resize)}checked="checked"{/if}>
        {'Resize after upload'|translate}
      </label>
    </div>

    <table id="sizeEdit-original">
      <tr>
        <th>{'Maximum width'|translate}</th>
        <td>
          <input type="text" name="original_resize_maxwidth" value="{$sizes.original_resize_maxwidth}" size="4" maxlength="4"{if isset($ferrors.original_resize_maxwidth)} class="dError"{/if}> {'pixels'|translate}
          {if isset($ferrors.original_resize_maxwidth)}<span class="dErrorDesc" title="{$ferrors.original_resize_maxwidth}">!</span>{/if}
        </td>
      </tr>
      <tr>
        <th>{'Maximum height'|translate}</th>
        <td>
          <input type="text" name="original_resize_maxheight" value="{$sizes.original_resize_maxheight}" size="4" maxlength="4"{if isset($ferrors.original_resize_maxheight)} class="dError"{/if}> {'pixels'|translate}
          {if isset($ferrors.original_resize_maxheight)}<span class="dErrorDesc" title="{$ferrors.original_resize_maxheight}">!</span>{/if}
        </td>
      </tr>
      <tr>
        <th>{'Image Quality'|translate}</th>
        <td>
          <input type="text" name="original_resize_quality" value="{$sizes.original_resize_quality}" size="3" maxlength="3"{if isset($ferrors.original_resize_quality)} class="dError"{/if}> %
          {if isset($ferrors.original_resize_quality)}<span class="dErrorDesc" title="{$ferrors.original_resize_quality}">!</span>{/if}
        </td>
      </tr>
    </table>
  {/if}
  </fieldset>

  <fieldset id="multiSizesConf">
    <legend>{'Multiple Size'|translate}</legend>

    <div class="showDetails">
      <a href="#" id="showDetails"{if isset($ferrors)} style="display:none"{/if}>{'show details'|translate}</a>
    </div>

    <table style="margin:0">
    {foreach from=$derivatives item=d key=type}
      <tr>
        <td>
          <label>
            {if $d.must_enable}
            <span class="sizeEnable">
              <span class="icon-ok"></span>
            </span>
            {else}
            <span class="sizeEnable font-checkbox">
              <span class="icon-check"></span>
              <input type="checkbox" name="d[{$type}][enabled]" {if $d.enabled}checked="checked"{/if}>
            </span>
            {/if}
            {$type|translate}
          </label>
        </td>

        <td>
          <span class="sizeDetails"{if isset($ferrors)} style="display:inline"{/if}>{$d.w} x {$d.h} {'pixels'|translate}{if $d.crop}, {'Crop'|translate|lower}{/if}</span>
        </td>

        <td>
          <span class="sizeDetails"{if isset($ferrors) and !isset($ferrors.$type)} style="display:inline"{/if}>
            <a href="#" id="sizeEditOpen-{$type}" class="sizeEditOpen">{'edit'|translate}</a>
          </span>
        </td>
      </tr>

      <tr id="sizeEdit-{$type}" class="sizeEdit" {if isset($ferrors.$type)} style="display:block"{/if}>
        <td colspan="3">
          <table class="sizeEditForm">
          {if !$d.must_square}
            <tr>
              <td colspan="2">
                <label class="font-checkbox">
                <span class="icon-check"></span>
                <input type="checkbox" class="cropToggle" name="d[{$type}][crop]" {if $d.crop}checked="checked"{/if}>
                  {'Crop'|translate}
                </label>
              </td>
            </tr>
          {/if}
            <tr>
              <td class="sizeEditWidth">{if $d.must_square or $d.crop}{'Width'|translate}{else}{'Maximum width'|translate}{/if}</td>
              <td>
                <input type="text" name="d[{$type}][w]" maxlength="4" size="4" value="{$d.w}"{if isset($ferrors.$type.w)} class="dError"{/if}> {'pixels'|translate}
                {if isset($ferrors.$type.w)}<span class="dErrorDesc" title="{$ferrors.$type.w}">!</span>{/if}
              </td>
            </tr>
          {if !$d.must_square}
            <tr>
              <td class="sizeEditHeight">{if $d.crop}{'Height'|translate}{else}{'Maximum height'|translate}{/if}</td>
              <td>
                <input type="text" name="d[{$type}][h]" maxlength="4" size="4"  value="{$d.h}"{if isset($ferrors.$type.h)} class="dError"{/if}> {'pixels'|translate}
                {if isset($ferrors.$type.h)}<span class="dErrorDesc" title="{$ferrors.$type.h}">!</span>{/if}
              </td>
            </tr>
          {/if}
            <tr>
              <td>{'Sharpen'|translate}</td>
              <td>
                <input type="text" name="d[{$type}][sharpen]" maxlength="4" size="4"  value="{$d.sharpen}"{if isset($ferrors.$type.sharpen)} class="dError"{/if}> %
                {if isset($ferrors.$type.sharpen)}<span class="dErrorDesc" title="{$ferrors.$type.sharpen}">!</span>{/if}
              </td>
            </tr>
          </table> {* #sizeEdit *}
        </td>
      </tr>
    {/foreach}
    </table>

    <p style="margin:10px 0 0 0;{if isset($ferrors)} display:block;{/if}" class="sizeDetails">
      {'Image Quality'|translate}
      <input type="text" name="resize_quality" value="{$resize_quality}" size="3" maxlength="3"{if isset($ferrors.resize_quality)} class="dError"{/if}> %
      {if isset($ferrors.resize_quality)}<span class="dErrorDesc" title="{$ferrors.resize_quality}">!</span>{/if}
    </p>
    <p style="margin:10px 0 0 0;{if isset($ferrors)} display:block;{/if}" class="sizeDetails">
      <a href="{$F_ACTION}&action=restore_settings" onclick="return confirm('{'Are you sure?'|translate|@escape:javascript}');">{'Reset to default values'|translate}</a>
    </p>

  {if !empty($custom_derivatives)}
    <fieldset class="sizeDetails">
      <legend>{'custom'|translate}</legend>

      <table style="margin:0">
      {foreach from=$custom_derivatives item=time key=custom}
        <tr><td>
          <label class="font-checkbox">
            <span class="icon-check"></span>
            <input type="checkbox" name="delete_custom_derivative_{$custom}"> {'Delete'|translate} {$custom} ({'Last hit'|translate}: {$time})
          </label>
        </td></tr>
      {/foreach}
      </table>
    </fieldset>
  {/if}

  </fieldset>

</div> <!-- configContent -->

<p class="formButtons">
  <button name="submit" type="submit" class="buttonLike">
    <i class="icon-floppy"></i> {'Save Settings'|@translate}
  </button>
</p>

<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>