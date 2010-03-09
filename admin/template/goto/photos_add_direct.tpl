{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}

{literal}
<script>
$(document).ready(function(){

  $("input[name=category_type]").click(function () {
    $("[id^=category_type_]").hide();
    $("#category_type_"+$(this).attr("value")).show();
  });
});
</script>
{/literal}

{if $upload_mode eq 'html'}
{literal}
<script type="text/javascript">
$(document).ready(function(){
  function addUploadBox() {
    var uploadBox = '<p class="file"><input type="file" size="60" name="image_upload[]" /></p>';
    $(uploadBox).appendTo("#uploadBoxes");
  }

  addUploadBox();

  $("#addUploadBox A").click(function () {
    addUploadBox();
  });
});
</script>
{/literal}

{elseif $upload_mode eq 'multiple'}
<script type="text/javascript" src="{$uploadify_path}/swfobject.js"></script>
<script type="text/javascript" src="{$uploadify_path}/jquery.uploadify.v2.1.0.min.js"></script>

<script type="text/javascript">
var uploadify_path = '{$uploadify_path}';
var upload_id = '{$upload_id}';
var session_id = '{$session_id}';
var pwg_token = '{$pwg_token}';
var buttonText = 'Browse';

{literal}
jQuery(document).ready(function() {
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
    'onAllComplete'  : function(event, data) {
      if (data.errors) {
        return false;
      }
      else {
        $("input[name=submit_upload]").click();
      }
    }
  });
});
{/literal}
</script>
{/if}

<div class="titrePage">
  <h2>{'Upload photos'|@translate}</h2>
</div>

{if count($setup_errors) > 0}
<div class="errors">
  <ul>
  {foreach from=$setup_errors item=error}
    <li>{$error}</li>
  {/foreach}
  </ul>
</div>
{else}

{if !empty($thumbnails)}
<fieldset>
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div>
  {foreach from=$thumbnails item=thumbnail}
    <a href="{$thumbnail.link}" onclick="window.open(this.href); return false;">
      <img src="{$thumbnail.src}" alt="{$thumbnail.file}" title="{$thumbnail.title}" class="thumbnail">
    </a>
  {/foreach}
  </div>
  <p id="batchLink"><a href="{$batch_link}">{$batch_label}</a></p>
</fieldset>
{/if}


<form id="uploadForm" enctype="multipart/form-data" method="post" action="{$F_ACTION}" class="properties">
{if $upload_mode eq 'multiple'}
<input name="upload_id" value="{$upload_id}" type="hidden">
{/if}

    <div class="formField">
      <div class="formFieldTitle">{'Drop into category'|@translate}</div>
      
      <label><input type="radio" name="category_type" value="existing"> {'existing category'|@translate}</label>
      <label><input type="radio" name="category_type" value="new" checked="checked"> {'create a new category'|@translate}</label>

      <div id="category_type_existing" style="display:none" class="category_selection">
        <select class="categoryDropDown" name="category">
          {html_options options=$category_options}
        </select>
      </div>

      <div id="category_type_new" class="category_selection">
        <table>
          <tr>
            <td>{'Parent category'|@translate}</td>
            <td>
              <select class="categoryDropDown" name="category_parent">
                <option value="0">------------</option>
                {html_options options=$category_options}
              </select>
            </td>
          </tr>
          <tr>
            <td>{'Category name'|@translate}</td>
            <td>
              <input type="text" name="category_name" value="{$F_CATEGORY_NAME}" style="width:400px">
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div class="formField">
      <div class="formFieldTitle">{'Who can see these photos?'|@translate}</div>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </div>

    <div class="formField">
      <div class="formFieldTitle">{'Select files'|@translate}</div>

{if $upload_mode eq 'html'}
    <p><a href="{$switch_url}">{'... or switch to the multiple files form'|@translate}</a></p>

      <p>{'JPEG files or ZIP archives with JPEG files inside please.'|@translate}</p>

      <div id="uploadBoxes"></div>
      <div id="addUploadBox">
        <a href="javascript:">{'+ Add an upload box'|@translate}</a>
      </div>
    
    </div> <!-- formField -->

    <p>
      <input class="submit" type="submit" name="submit_upload" value="{'Upload'|@translate}" {$TAG_INPUT_ENABLED}/>
    </p>
{elseif $upload_mode eq 'multiple'}
    </table>

    <p>
      <input type="file" name="uploadify" id="uploadify" />
    </p>

    <p><a href="{$switch_url}">{'... or switch to the old style form'|@translate}</a></p>

    <div id="fileQueue"></div>

    </div> <!-- formField -->
    <p>
      <input class="submit" type="button" value="{'Upload'|@translate}" onclick="javascript:jQuery('#uploadify').uploadifyUpload()"/>
      <input type="submit" name="submit_upload" style="display:none"/>
    </p>
{/if}
</form>
{/if}
