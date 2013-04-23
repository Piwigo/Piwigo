{if $upload_mode eq 'multiple'}
{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js' }
{combine_script id='jquery.uploadify' load='footer' require='jquery' path='admin/include/uploadify/jquery.uploadify.v3.0.0.min.js' }
{combine_script id='jquery.ui.progressbar' load='footer'}
{combine_css path="themes/default/js/plugins/jquery.jgrowl.css"}
{combine_css path="admin/include/uploadify/uploadify.css"}
{/if}

{include file='include/colorbox.inc.tpl'}
{include file='include/add_album.inc.tpl'}

{footer_script}{literal}
jQuery(document).ready(function(){
  function checkUploadStart() {
    var nbErrors = 0;
    jQuery("#formErrors").hide();
    jQuery("#formErrors li").hide();

    if (jQuery("#albumSelect option:selected").length == 0) {
      jQuery("#formErrors #noAlbum").show();
      nbErrors++;
    }

    var nbFiles = 0;
    if (jQuery("#uploadBoxes").size() == 1) {
      jQuery("input[name^=image_upload]").each(function() {
        if (jQuery(this).val() != "") {
          nbFiles++;
        }
      });
    }
    else {
      nbFiles = jQuery(".uploadifyQueueItem").size();
    }

    if (nbFiles == 0) {
      jQuery("#formErrors #noPhoto").show();
      nbErrors++;
    }

    if (nbErrors != 0) {
      jQuery("#formErrors").show();
      return false;
    }
    else {
      return true;
    }

  }

  function humanReadableFileSize(bytes) {
    var byteSize = Math.round(bytes / 1024 * 100) * .01;
    var suffix = 'KB';

    if (byteSize > 1000) {
      byteSize = Math.round(byteSize *.001 * 100) * .01;
      suffix = 'MB';
    }

    var sizeParts = byteSize.toString().split('.');
    if (sizeParts.length > 1) {
      byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0,2);
    }
    else {
      byteSize = sizeParts[0];
    }

    return byteSize+suffix;
  }

  jQuery("#hideErrors").click(function() {
    jQuery("#formErrors").hide();
    return false;
  });

  jQuery("#uploadWarningsSummary a.showInfo").click(function() {
    jQuery("#uploadWarningsSummary").hide();
    jQuery("#uploadWarnings").show();
    return false;
  });

  jQuery("#showPermissions").click(function() {
    jQuery(this).parent(".showFieldset").hide();
    jQuery("#permissions").show();
    return false;
  });

{/literal}
{if $upload_mode eq 'html'}
{literal}
  function addUploadBox() {
    var uploadBox = '<p class="file"><input type="file" size="60" name="image_upload[]"></p>';
    jQuery(uploadBox).appendTo("#uploadBoxes");
  }

  addUploadBox();

  jQuery("#addUploadBox A").click(function () {
    addUploadBox();
  });

  jQuery("#uploadForm").submit(function() {
    return checkUploadStart();
  });
{/literal}
{elseif $upload_mode eq 'multiple'}

var uploadify_path = '{$uploadify_path}';
var upload_id = '{$upload_id}';
var session_id = '{$session_id}';
var pwg_token = '{$pwg_token}';
var buttonText = "{'Select files'|@translate}";
var sizeLimit = Math.round({$upload_max_filesize} / 1024); /* in KBytes */

{literal}
  jQuery("#uploadify").uploadify({
    'uploader'       : uploadify_path + '/uploadify.php',
    'langFile'       : uploadify_path + '/uploadifyLang_en.js',
    'swf'            : uploadify_path + '/uploadify.swf',
    'checkExisting'  : false,

    buttonCursor     : 'pointer',
    'buttonText'     : buttonText,
    'width'          : 300,
    'cancelImage'    : uploadify_path + '/cancel.png',
    'queueID'        : 'fileQueue',
    'auto'           : false,
    'multi'          : true,
    'fileTypeDesc'   : 'Photo files',
    'fileTypeExts'   : '*.jpg;*.JPG;*.jpeg;*.JPEG;*.png;*.PNG;*.gif;*.GIF',
    'fileSizeLimit'  : sizeLimit,
    'progressData'   : 'percentage',
    requeueErrors   : false,
    'onSelect'       : function(event,ID,fileObj) {
      jQuery("#fileQueue").show();
    },
    'onQueueComplete'  : function(stats) {
      jQuery("input[name=submit_upload]").click();
    },
    onUploadError: function (file,errorCode,errorMsg,errorString,swfuploadifyQueue) {
      /* uploadify calls the onUploadError trigger when the user cancels a file! */
      /* There no error so we skip it to avoid panic.                            */
      if ("Cancelled" == errorString) {
        return false;
      }

      var msg = file.name+', '+errorString;

      /* Let's put the error message in the form to display once the form is     */
      /* performed, it makes support easier when user can copy/paste the error   */
      /* thrown.                                                                 */
      jQuery("#uploadForm").append('<input type="hidden" name="onUploadError[]" value="'+msg+'">');

      jQuery.jGrowl(
        '<p></p>onUploadError '+msg,
        {
          theme:  'error',
          header: 'ERROR',
          life:   4000,
          sticky: false
        }
      );

      return false;
    },
    onUploadSuccess: function (file,data,response) {
      var data = jQuery.parseJSON(data);
      jQuery("#uploadedPhotos").parent("fieldset").show();

      /* Let's display the thumbnail of the uploaded photo, no need to wait the  */
      /* end of the queue                                                        */
      jQuery("#uploadedPhotos").prepend('<img src="'+data.thumbnail_url+'" class="thumbnail"> ');
    },
    onUploadComplete: function(file,swfuploadifyQueue) {
      var max = parseInt(jQuery("#progressMax").text());
      var next = parseInt(jQuery("#progressCurrent").text())+1;
      var addToProgressBar = 2;
      if (next <= max) {
        jQuery("#progressCurrent").text(next);
      }
      else {
        addToProgressBar = 1;
      }

      jQuery("#progressbar").progressbar({
        value: jQuery("#progressbar").progressbar("option", "value") + addToProgressBar
      });
    }
  });

  jQuery("input[type=button]").click(function() {
    if (!checkUploadStart()) {
      return false;
    }

    jQuery("#uploadify").uploadifySettings(
      'postData',
      {
        'category_id' : jQuery("select[name=category] option:selected").val(),
        'level' : jQuery("select[name=level] option:selected").val(),
        'upload_id' : upload_id,
        'session_id' : session_id,
        'pwg_token' : pwg_token,
      }
    );

    nb_files = jQuery(".uploadifyQueueItem").size();
    jQuery("#progressMax").text(nb_files);
    jQuery("#progressbar").progressbar({max: nb_files*2, value:1});
    jQuery("#progressCurrent").text(1);

    jQuery("#uploadProgress").show();

    jQuery("#uploadify").uploadifyUpload();
  });

{/literal}
{/if}
});
{/footer_script}

<div class="titrePage">
  <h2>{'Upload Photos'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<div id="photosAddContent">

{if count($setup_errors) > 0}
<div class="errors">
  <ul>
  {foreach from=$setup_errors item=error}
    <li>{$error}</li>
  {/foreach}
  </ul>
</div>
{else}

  {if count($setup_warnings) > 0}
<div class="warnings">
  <ul>
    {foreach from=$setup_warnings item=warning}
    <li>{$warning}</li>
    {/foreach}
  </ul>
  <div class="hideButton" style="text-align:center"><a href="{$hide_warnings_link}">{'Hide'|@translate}</a></div>
</div>
  {/if}


{if !empty($thumbnails)}
<fieldset>
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div>
  {foreach from=$thumbnails item=thumbnail}
    <a href="{$thumbnail.link}" class="externalLink">
      <img src="{$thumbnail.src}" alt="{$thumbnail.file}" title="{$thumbnail.title}" class="thumbnail">
    </a>
  {/foreach}
  </div>
  <p id="batchLink"><a href="{$batch_link}">{$batch_label}</a></p>
</fieldset>
<p style="margin:10px"><a href="{$another_upload_link}">{'Add another set of photos'|@translate}</a></p>
{else}

<div id="formErrors" class="errors" style="display:none">
  <ul>
    <li id="noAlbum">{'Select an album'|@translate}</li>
    <li id="noPhoto">{'Select at least one photo'|@translate}</li>
  </ul>
  <div class="hideButton" style="text-align:center"><a href="#" id="hideErrors">{'Hide'|@translate}</a></div>
</div>


<form id="uploadForm" enctype="multipart/form-data" method="post" action="{$form_action}" class="properties">
{if $upload_mode eq 'multiple'}
    <input name="upload_id" value="{$upload_id}" type="hidden">
{/if}

    <fieldset>
      <legend>{'Drop into album'|@translate}</legend>

      <span id="albumSelection"{if count($category_options) == 0} style="display:none"{/if}>
      <select id="albumSelect" name="category">
        {html_options options=$category_options selected=$category_options_selected}
      </select>
      <br>{'... or '|@translate}</span><a href="#" class="addAlbumOpen" title="{'create a new album'|@translate}">{'create a new album'|@translate}</a>
      
    </fieldset>

    <fieldset>
      <legend>{'Select files'|@translate}</legend>
 
    {if isset($original_resize_maxheight)}<p class="uploadInfo">{'The picture dimensions will be reduced to %dx%d pixels.'|@translate|@sprintf:$original_resize_maxwidth:$original_resize_maxheight}</p>{/if}

    <p id="uploadWarningsSummary">{$upload_max_filesize_shorthand}B. {$upload_file_types}. {if isset($max_upload_resolution)}{$max_upload_resolution}Mpx{/if} <a class="showInfo" title="{'Learn more'|@translate}">i</a></p>

    <p id="uploadWarnings">
{'Maximum file size: %sB.'|@translate|@sprintf:$upload_max_filesize_shorthand}
{'Allowed file types: %s.'|@translate|@sprintf:$upload_file_types}
  {if isset($max_upload_resolution)}
{'Approximate maximum resolution: %dM pixels (that\'s %dx%d pixels).'|@translate|@sprintf:$max_upload_resolution:$max_upload_width:$max_upload_height}
  {/if}
    </p>



{if $upload_mode eq 'html'}
      <div id="uploadBoxes"></div>
      <div id="addUploadBox">
        <a href="javascript:">{'+ Add an upload box'|@translate}</a>
      </div>

    <p id="uploadModeInfos">{'You are using the Browser uploader. Try the <a href="%s">Flash uploader</a> instead.'|@translate|@sprintf:$switch_url}</p>

{elseif $upload_mode eq 'multiple'}
    <div id="uploadify">You've got a problem with your JavaScript</div> 

    <div id="fileQueue" style="display:none"></div>

    <p id="uploadModeInfos">{'You are using the Flash uploader. Problems? Try the <a href="%s">Browser uploader</a> instead.'|@translate|@sprintf:$switch_url}</p>

{/if}
    </fieldset>

    <p class="showFieldset"><a id="showPermissions" href="#">{'Manage Permissions'|@translate}</a></p>

    <fieldset id="permissions" style="display:none">
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>

{if $upload_mode eq 'html'}
    <p>
      <input class="submit" type="submit" name="submit_upload" value="{'Start Upload'|@translate}">
    </p>
{elseif $upload_mode eq 'multiple'}
    <p style="margin-bottom:1em">
      <input class="submit" type="button" value="{'Start Upload'|@translate}">
      <input type="submit" name="submit_upload" style="display:none">
    </p>
{/if}
</form>

<div id="uploadProgress" style="display:none">
{'Photo %s of %s'|@translate|@sprintf:'<span id="progressCurrent">1</span>':'<span id="progressMax">10</span>'}
<br>
<div id="progressbar"></div>
</div>

<fieldset style="display:none">
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div id="uploadedPhotos"></div>
</fieldset>

{/if} {* empty($thumbnails) *}
{/if} {* $setup_errors *}

</div> <!-- photosAddContent -->
