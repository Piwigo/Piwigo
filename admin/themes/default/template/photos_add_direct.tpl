{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js' }

{if $upload_mode eq 'multiple'}
<script type="text/javascript" src="{$uploadify_path}/swfobject.js"></script>
{combine_script id='jquery.uploadify' load='footer' require='jquery' path='admin/include/uploadify/jquery.uploadify.v2.1.0.min.js' }
{/if}
{html_head}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/default/uploadify.jGrowl.css">
{/html_head}

{footer_script}{literal}
jQuery(document).ready(function(){
  function checkUploadStart() {
    var nbErrors = 0;
    $("#formErrors").hide();
    $("#formErrors li").hide();

    if ($("input[name=category_type]:checked").val() == "new" && $("input[name=category_name]").val() == "") {
      $("#formErrors #emptyCategoryName").show();
      nbErrors++;
    }

    var nbFiles = 0;
    if ($("#uploadBoxes").size() == 1) {
      $("input[name^=image_upload]").each(function() {
        if ($(this).val() != "") {
          nbFiles++;
        }
      });
    }
    else {
      nbFiles = $(".uploadifyQueueItem").size();
    }

    if (nbFiles == 0) {
      $("#formErrors #noPhoto").show();
      nbErrors++;
    }

    if (nbErrors != 0) {
      $("#formErrors").show();
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

  if ($("select[name=category] option").length == 0) {
    $('input[name=category_type][value=existing]').attr('disabled', true);
    $('input[name=category_type]').attr('checked', false);
    $('input[name=category_type][value=new]').attr('checked', true);
  }

  $("input[name=category_type]").click(function () {
    $("[id^=category_type_]").hide();
    $("#category_type_"+$(this).attr("value")).show();
  });

  $("#hideErrors").click(function() {
    $("#formErrors").hide();
    return false;
  });

{/literal}
{if $upload_mode eq 'html'}
{literal}
  function addUploadBox() {
    var uploadBox = '<p class="file"><input type="file" size="60" name="image_upload[]"></p>';
    $(uploadBox).appendTo("#uploadBoxes");
  }

  addUploadBox();

  $("#addUploadBox A").click(function () {
    addUploadBox();
  });

  $("#uploadForm").submit(function() {
    return checkUploadStart();
  });
{/literal}
{elseif $upload_mode eq 'multiple'}

var uploadify_path = '{$uploadify_path}';
var upload_id = '{$upload_id}';
var session_id = '{$session_id}';
var pwg_token = '{$pwg_token}';
var buttonText = 'Browse';
var sizeLimit = {$upload_max_filesize};

{literal}
  jQuery("#uploadify").uploadify({
    'uploader'       : uploadify_path + '/uploadify.swf',
    'script'         : uploadify_path + '/uploadify.php',
    'scriptData'     : {
      'upload_id' : upload_id,
      'session_id' : session_id,
      'pwg_token' : pwg_token,
    },
    'cancelImg'      : uploadify_path + '/cancel.png',
    'queueID'        : 'fileQueue',
    'auto'           : false,
    'displayData'    : 'speed',
    'buttonText'     : buttonText,
    'multi'          : true,
    'fileDesc'       : 'Photo files (*.jpg,*.jpeg,*.png)',
    'fileExt'        : '*.jpg;*.JPG;*.jpeg;*.JPEG;*.png;*.PNG',
    'sizeLimit'      : sizeLimit,
    'onAllComplete'  : function(event, data) {
      if (data.errors) {
        return false;
      }
      else {
        $("input[name=submit_upload]").click();
      }
    },
    onError: function (event, queueID ,fileObj, errorObj) {
      var msg;

      if (errorObj.type === "HTTP") {
        if (errorObj.info === 404) {
          alert('Could not find upload script.');
          msg = 'Could not find upload script.';
        }
        else {
          msg = errorObj.type+": "+errorObj.info;
        }
      }
      else if (errorObj.type ==="File Size") {
        msg = "File too big";
        msg = msg + '<br>'+fileObj.name+': '+humanReadableFileSize(fileObj.size);
        msg = msg + '<br>Limit: '+humanReadableFileSize(sizeLimit);
      }
      else {
        msg = errorObj.type+": "+errorObj.info;
      }

      $.jGrowl(
        '<p></p>'+msg,
        {
          theme:  'error',
          header: 'ERROR',
          sticky: true
        }
      );

      $("#fileUploadgrowl" + queueID).fadeOut(
        250,
        function() {
          $("#fileUploadgrowl" + queueID).remove()
        }
      );
      return false;
    },
    onCancel: function (a, b, c, d) {
      var msg = "Cancelled uploading: "+c.name;
      $.jGrowl(
        '<p></p>'+msg,
        {
          theme:  'warning',
          header: 'Cancelled Upload',
          life:   4000,
          sticky: false
        }
      );
    },
    onClearQueue: function (a, b) {
      var msg = "Cleared "+b.fileCount+" files from queue";
      $.jGrowl(
        '<p></p>'+msg,
        {
          theme:  'warning',
          header: 'Cleared Queue',
          life:   4000,
          sticky: false
        }
      );
    },
    onComplete: function (a, b ,c, d, e) {
      var size = Math.round(c.size/1024);
      $.jGrowl(
        '<p></p>'+c.name+' - '+size+'KB',
        {
          theme:  'success',
          header: 'Upload Complete',
          life:   4000,
          sticky: false
        }
      );
    }
  });

  $("input[type=button]").click(function() {
    if (!checkUploadStart()) {
      return false;
    }

    $("#uploadify").uploadifyUpload();
  });

{/literal}
{/if}
});
{/footer_script}

<div class="titrePage">
  <h2>{'Upload Photos'|@translate}</h2>
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
<p><a href="{$another_upload_link}">{'Add another set of photos'|@translate}</a></p>
{else}

<div id="formErrors" class="errors" style="display:none">
  <ul>
    <li id="emptyCategoryName">{'The name of an album must not be empty'|@translate}</li>
    <li id="noPhoto">{'Select at least one picture'|@translate}</li>
  </ul>
  <div class="hideButton" style="text-align:center"><a href="#" id="hideErrors">{'Hide'|@translate}</a></div>
</div>

<form id="uploadForm" enctype="multipart/form-data" method="post" action="{$form_action}" class="properties">
    <fieldset>
      <legend>{'Drop into album'|@translate}</legend>
      {if $upload_mode eq 'multiple'}
      <input name="upload_id" value="{$upload_id}" type="hidden">
      {/if}

      <label><input type="radio" name="category_type" value="existing"> {'existing album'|@translate}</label>
      <label><input type="radio" name="category_type" value="new" checked="checked"> {'create a new album'|@translate}</label>

      <div id="category_type_existing" style="display:none" class="category_selection">
        <select class="categoryDropDown" name="category">
          {html_options options=$category_options selected=$category_options_selected}
        </select>
      </div>

      <div id="category_type_new" class="category_selection">
        <table>
          <tr>
            <td>{'Parent album'|@translate}</td>
            <td>
              <select class="categoryDropDown" name="category_parent">
                <option value="0">------------</option>
                {html_options options=$category_parent_options selected=$category_parent_options_selected}
              </select>
            </td>
          </tr>
          <tr>
            <td>{'Album name'|@translate}</td>
            <td>
              <input type="text" name="category_name" value="{$F_CATEGORY_NAME}" style="width:400px">
            </td>
          </tr>
        </table>
      </div>
    </fieldset>

    <fieldset>
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>

    <fieldset>
      <legend>{'Select files'|@translate}</legend>

{if $upload_mode eq 'html'}
    <p><a href="{$switch_url}">{'... or switch to the multiple files form'|@translate}</a></p>

      <p>{'JPEG files or ZIP archives with JPEG files inside please.'|@translate}</p>

      <div id="uploadBoxes"></div>
      <div id="addUploadBox">
        <a href="javascript:">{'+ Add an upload box'|@translate}</a>
      </div>
    
    </fieldset>

    <p>
      <input class="submit" type="submit" name="submit_upload" value="{'Upload'|@translate}" {$TAG_INPUT_ENABLED}>
    </p>
{elseif $upload_mode eq 'multiple'}
    <p>
      <input type="file" name="uploadify" id="uploadify">
    </p>

    <p><a href="{$switch_url}">{'... or switch to the old style form'|@translate}</a></p>

    <div id="fileQueue"></div>

    </fieldset>
    <p>
      <input class="submit" type="button" value="{'Upload'|@translate}">
      <input type="submit" name="submit_upload" style="display:none">
    </p>
{/if}
</form>
{/if} {* empty($thumbnails) *}
{/if} {* $setup_errors *}

</div> <!-- photosAddContent -->
