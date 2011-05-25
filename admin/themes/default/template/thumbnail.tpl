{combine_script id='jquery.ajaxmanager' load='footer' path='themes/default/js/plugins/jquery.ajaxmanager.js'}

{footer_script}
var width_str = '{'Width'|@translate}';
var height_str = '{'Height'|@translate}';
var max_width_str = '{'Maximum Width'|@translate}';
var max_height_str = '{'Maximum Height'|@translate}';
var remaining = '{'photos without thumbnail (jpeg and png only)'|@translate}';
var todo = {$TOTAL_NB_REMAINING};
var done = 0;

{literal}
var queuedManager = $.manageAjax.create('queued', { 
  queue: true,  
  cacheResponse: false,
  maxRequests: 3,
  complete: function() {
    jQuery("#thumb_remaining").text(todo-(++done) + ' ' + remaining);
    if (todo == done) {
      jQuery('.waiting_bar, #thumb_remaining, .properties').hide();
    }
  }
}); 

function processThumbs(width,height,crop,follow_orientation) {
  jQuery('tr.nothumb').each(function() {
    var image_path = jQuery(this).find('td.filepath').text();
    var td=this;
    queuedManager.add({
      type: 'GET', 
      url: 'ws.php', 
      data: {
        method: 'pwg.images.resizeThumbnail',
        image_path: image_path,
        maxwidth: width,
        maxheight: height,
        crop: crop,
        follow_orientation: follow_orientation,
        format:'json'
      },
      dataType: 'json',
      success: (function(row) { return function(data) {
          if (data.stat =='ok') {
            if (todo < 200)
              jQuery(row).find('td.thumbpic').html('<img src="'+data.result.destination+'"/>');
            jQuery(row).find('td.thumbdim').html(""+data.result.width+" x "+data.result.height);
            jQuery(row).find('td.thumbgentime').html(""+data.result.time);
            jQuery(row).find('td.thumbsize').html(""+data.result.size);
            jQuery(row).removeClass("nothumb");
          } else {
            jQuery(row).find('td.thumbdim').html('#ERR#'+data.err+"# : "+data.message);
            jQuery(row).removeClass("nothumb");
            jQuery(row).addClass("error");
          }
        }
      })(td)
    });
  });
}

function toggleCropFields() {
  if (jQuery("#thumb_crop").is(':checked')) {
    jQuery("label[for='thumb_maxwidth']").text(width_str);
    jQuery("label[for='thumb_maxheight']").text(height_str);
    jQuery("#thumb_follow_orientation_li").show();
  }
  else {
    jQuery("label[for='thumb_maxwidth']").text(max_width_str);
    jQuery("label[for='thumb_maxheight']").text(max_height_str);
    jQuery("#thumb_follow_orientation_li").hide();
  }
}

jQuery(document).ready(function(){
  jQuery('input#proceed').click (function () {
    var width = jQuery('input[name="thumb_maxwidth"]').val();
    var height = jQuery('input[name="thumb_maxheight"]').val();
    var crop = jQuery('#thumb_crop').is(':checked');
    var follow_orientation = jQuery('#thumb_follow_orientation').is(':checked');
    jQuery(".waiting_bar").toggle();
    if (todo < 200)
      jQuery('.thumbpic').show();
    jQuery('.thumbgentime, .thumbsize, .thumbdim').show();
    processThumbs(width,height,crop,follow_orientation);
  });

  toggleCropFields();
  jQuery("#thumb_crop").click(function () {toggleCropFields()});

  jQuery('.thumbpic, .thumbgentime, .thumbsize, .thumbdim').hide();
});
{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Thumbnail creation'|@translate}</h2>
</div>

{if !empty($remainings) }
<form method="post" action="{$params.F_ACTION}" class="properties">

  <fieldset>
    <legend>{'Thumbnail creation'|@translate}</legend>

    <ul>
      <li>
        <span class="property"><label for="thumb_crop">{'Crop'|@translate}</label></span>
        <input type="checkbox" name="thumb_crop" id="thumb_crop" {$values.thumb_crop}>
      </li>
      <li id="thumb_follow_orientation_li">
        <span class="property"><label for="thumb_follow_orientation">{'Follow Orientation'|@translate}</label></span>
        <input type="checkbox" name="thumb_follow_orientation" id="thumb_follow_orientation" {$values.thumb_follow_orientation}>
      </li>
      <li>
        <span class="property"><label for="thumb_maxwidth">{'Maximum Width'|@translate}</label></span>
        <input type="text" name="thumb_maxwidth" id="thumb_maxwidth" value="{$values.thumb_maxwidth}" size="4" maxlength="4"> {'pixels'|@translate}
      </li>
      <li>
        <span class="property"><label for="thumb_maxheight">{'Maximum Height'|@translate}</label></span>
        <input type="text" name="thumb_maxheight" id="thumb_maxheight" value="{$values.thumb_maxheight}" size="4" maxlength="4"> {'pixels'|@translate}
      </li>
      <li>
        <span class="property"><label for="thumb_quality">{'Image Quality'|@translate}</label></span>
        <input type="text" name="thumb_quality" id="thumb_quality" value="{$values.thumb_quality}" size="3" maxlength="3"> %
      </li>
    </ul>
  </fieldset>

  <p class="waiting_bar"><input type="button" name="submit" id="proceed" value="{'Submit'|@translate}"></p>
  <p class="waiting_bar" style="display:none;">{'Please wait...'|@translate}<br><img src="admin/themes/default/images/ajax-loader-bar.gif"></p>
</form>

<div class="admin"><span id="thumb_remaining">{$TOTAL_NB_REMAINING} {'photos without thumbnail (jpeg and png only)'|@translate}</span></div>
<table style="width:100%;">
  <tr class="throw">
    <th>&nbsp;</th>
    <th style="width:60%;">{'Path'|@translate}</th>
    <th>{'Filesize'|@translate}</th>
    <th>{'Dimensions'|@translate}</th>
    <th class="thumbpic">{'Thumbnail'|@translate}</th>
    <th class="thumbgentime">{'generated in'|@translate}</th>
    <th class="thumbsize">{'Filesize'|@translate}</th>
    <th class="thumbdim">{'Dimensions'|@translate}</th>
  </tr>
  {foreach from=$remainings item=elt name=remain_loop}
  <tr class="{if $smarty.foreach.remain_loop.index is odd}row1{else}row2{/if} nothumb item" id="th_{$smarty.foreach.remain_loop.iteration}">
    <td>{$smarty.foreach.remain_loop.iteration}</td>
    <td class="filepath">{$elt.PATH}</td>
    <td>{$elt.FILESIZE_IMG}</td>
    <td>{$elt.WIDTH_IMG} x {$elt.HEIGHT_IMG}</td>
  <td class="thumbpic"><img src="admin/themes/default/images/ajax-loader.gif"></td>
  <td class="thumbgentime">&nbsp;</td>
  <td class="thumbsize">&nbsp;</td>
  <td class="thumbdim">&nbsp;</td>
  </tr>
  {/foreach}
</table>
{else}
<p style="text-align:left;margin:20px;">
<b>{'No missing thumbnail'|@translate}</b><br><br>
{'If you want to regenerate thumbnails, please go to the <a href="%s">Batch Manager</a>.'|@translate|@sprintf:"admin.php?page=batch_manager"}
</p>
{/if}