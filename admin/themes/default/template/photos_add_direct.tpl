{if $upload_mode eq 'multiple'}
{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js' }
{combine_script id='swfobject' load='footer' path='admin/include/uploadify/swfobject.js'}
{combine_script id='jquery.uploadify' load='footer' require='jquery' path='admin/include/uploadify/jquery.uploadify.v2.1.0.min.js' }
{combine_css path="admin/themes/default/uploadify.jGrowl.css"}
{combine_css path="admin/include/uploadify/uploadify.css"}
{/if}

{include file='include/colorbox.inc.tpl'}

{footer_script}{literal}
jQuery(document).ready(function(){
  function checkUploadStart() {
    var nbErrors = 0;
    jQuery("#formErrors").hide();
    jQuery("#formErrors li").hide();

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

  function fillCategoryListbox(selectId, selectedValue) {
    jQuery.getJSON(
      "ws.php?format=json&method=pwg.categories.getList",
      {
        recursive: true,
        fullname: true,
        format: "json",
      },
      function(data) {
        jQuery.each(
          data.result.categories,
          function(i,category) {
            var selected = "";
            if (category.id == selectedValue) {
              selected = "selected";
            }
            
            jQuery("<option/>")
              .attr("value", category.id)
              .attr("selected", selected)
              .text(category.name)
              .appendTo("#"+selectId)
              ;
          }
        );
      }
    );
  }

/*
  jQuery("#albumSelect").find("option").remove();
  fillCategoryListbox("albumSelect");
  fillCategoryListbox("category_parent");
*/
  
  jQuery(".addAlbumOpen").colorbox({inline:true, href:"#addAlbumForm"});

  jQuery("#addAlbumForm form").submit(function(){
      jQuery("#categoryNameError").text("");

      jQuery.ajax({
        url: "ws.php?format=json&method=pwg.categories.add",
        data: {
          parent: jQuery("select[name=category_parent] option:selected").val(),
          name: jQuery("input[name=category_name]").val(),
        },
        beforeSend: function() {
          jQuery("#albumCreationLoading").show();
        },
        success:function(html) {
          jQuery("#albumCreationLoading").hide();

          var newAlbum = jQuery.parseJSON(html).result.id;
          jQuery(".addAlbumOpen").colorbox.close();

          jQuery("#albumSelect").find("option").remove();
          fillCategoryListbox("albumSelect", newAlbum);

          /* we refresh the album creation form, in case the user wants to create another album */
          jQuery("#category_parent").find("option").remove();
          fillCategoryListbox("category_parent", newAlbum);

          jQuery("#addAlbumForm form input[name=category_name]").val('');

          return true;
        },
        error:function(XMLHttpRequest, textStatus, errorThrows) {
            jQuery("#albumCreationLoading").hide();
            jQuery("#categoryNameError").text(errorThrows).css("color", "red");
        }
      });

      return false;
  });

  jQuery("#hideErrors").click(function() {
    jQuery("#formErrors").hide();
    return false;
  });

  jQuery("#uploadWarningsSummary a.showInfo").click(function() {
    jQuery("#uploadWarningsSummary").hide();
    jQuery("#uploadWarnings").show();
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
    'buttonText'     : buttonText,
    'multi'          : true,
    'fileDesc'       : 'Photo files (*.jpg,*.jpeg,*.png)',
    'fileExt'        : '*.jpg;*.JPG;*.jpeg;*.JPEG;*.png;*.PNG',
    'sizeLimit'      : sizeLimit,
    'onSelect'       : function(event,ID,fileObj) {
      jQuery("#fileQueue").show();
    },
    'onAllComplete'  : function(event, data) {
      if (data.errors) {
        return false;
      }
      else {
        jQuery("input[name=submit_upload]").click();
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

      jQuery.jGrowl(
        '<p></p>'+msg,
        {
          theme:  'error',
          header: 'ERROR',
          sticky: true
        }
      );

      jQuery("#fileUploadgrowl" + queueID).fadeOut(
        250,
        function() {
          jQuery("#fileUploadgrowl" + queueID).remove()
        }
      );
      return false;
    },
    onCancel: function (a, b, c, d) {
      var msg = "Cancelled uploading: "+c.name;
      jQuery.jGrowl(
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
      jQuery.jGrowl(
        '<p></p>'+msg,
        {
          theme:  'warning',
          header: 'Cleared Queue',
          life:   4000,
          sticky: false
        }
      );
    },
    onComplete: function (a, b ,c, response, e) {
      var size = Math.round(c.size/1024);

      var response = jQuery.parseJSON(response);

      jQuery("#uploadedPhotos").parent("fieldset").show();
      jQuery("#uploadedPhotos").prepend('<img src="'+response.thumbnail_url+'" class="thumbnail"> ');

      jQuery.jGrowl(
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

  jQuery("input[type=button]").click(function() {
    if (!checkUploadStart()) {
      return false;
    }

    jQuery("#uploadify").uploadifySettings(
      'scriptData',
      {
        'category_id' : jQuery("select[name=category] option:selected").val(),
        'level' : jQuery("select[name=level] option:selected").val(),
      }
    );

    jQuery("#uploadify").uploadifyUpload();
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
<p style="margin:10px"><a href="{$another_upload_link}">{'Add another set of photos'|@translate}</a></p>
{else}

<div id="formErrors" class="errors" style="display:none">
  <ul>
    <li id="noPhoto">{'Select at least one photo'|@translate}</li>
  </ul>
  <div class="hideButton" style="text-align:center"><a href="#" id="hideErrors">{'Hide'|@translate}</a></div>
</div>

<div style="display:none">
  <div id="addAlbumForm" style="text-align:left;padding:1em;">
    <form>
      {'Parent album'|@translate}<br>
      <select id ="category_parent" name="category_parent">
        <option value="0">------------</option>
        {html_options options=$category_parent_options selected=$category_parent_options_selected}
      </select>

      <br><br>{'Album name'|@translate}<br><input name="category_name" type="text"> <span id="categoryNameError"></span>
      <br><br><br><input type="submit" value="{'Create'|@translate}"> <span id="albumCreationLoading" style="display:none"><img src="themes/default/images/ajax-loader-small.gif"></span>
    </form>
  </div>
</div>

<form id="uploadForm" enctype="multipart/form-data" method="post" action="{$form_action}" class="properties">
{if $upload_mode eq 'multiple'}
    <input name="upload_id" value="{$upload_id}" type="hidden">
{/if}

    <fieldset>
      <legend>{'Drop into album'|@translate}</legend>

      <select id="albumSelect" name="category">
        {html_options options=$category_options selected=$category_options_selected}
      </select>
      <br>{'... or '|@translate}<a href="#" class="addAlbumOpen" title="{'create a new album'|@translate}">{'create a new album'|@translate}</a>
      
    </fieldset>

    <fieldset>
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>

    <fieldset>
      <legend>{'Select files'|@translate}</legend>

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
    
    </fieldset>

    <p>
      <input class="submit" type="submit" name="submit_upload" value="{'Start Upload'|@translate}">
    </p>
{elseif $upload_mode eq 'multiple'}

    <p>
      <input type="file" name="uploadify" id="uploadify">
    </p>

    <div id="fileQueue" style="display:none"></div>

    <p id="uploadModeInfos">{'You are using the Flash uploader. Problems? Try the <a href="%s">Browser uploader</a> instead.'|@translate|@sprintf:$switch_url}</p>

    </fieldset>
    <p>
      <input class="submit" type="button" value="{'Start Upload'|@translate}">
      <input type="submit" name="submit_upload" style="display:none">
    </p>
{/if}
</form>

<fieldset style="display:none">
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div id="uploadedPhotos"></div>
</fieldset>

{/if} {* empty($thumbnails) *}
{/if} {* $setup_errors *}

</div> <!-- photosAddContent -->
