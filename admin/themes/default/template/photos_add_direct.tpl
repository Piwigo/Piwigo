{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.plupload' load='footer' require='jquery' path='themes/default/js/plugins/plupload/plupload.full.min.js'}
{combine_script id='jquery.plupload.queue' load='footer' require='jquery' path='themes/default/js/plugins/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js'}

{combine_css path="themes/default/js/plugins/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css"}

{assign var="plupload_i18n" value="themes/default/js/plugins/plupload/i18n/`$lang_info.plupload_code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$plupload_i18n|@file_exists}
  {combine_script id="plupload_i18n-`$lang_info.plupload_code`" load="footer" path=$plupload_i18n require="jquery.plupload.queue"}
{/if}

{include file='include/colorbox.inc.tpl'}
{include file='include/add_album.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='piecon' load='footer' path='themes/default/js/plugins/piecon.js'}

{html_style}
.addAlbumFormParent { display: none; } /* specific to this page, do not move in theme.css */
{/html_style}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'), {
  filter: function(categories, options) {
    if (categories.length > 0) {
      jQuery(".addAlbumEmptyCenter").css( "height", "auto" );
      jQuery(".addAlbumFormParent").attr( "style", "display: block !important;" );
    }
    
    return categories;
  }
});

jQuery('[data-add-album]').pwgAddAlbum({
  afterSelect: function() {
    jQuery("#uploadForm").show();
    jQuery(".addAlbumEmptyCenter").hide();
    jQuery(".addAlbumEmptyCenter").css( "height", "auto" );
    jQuery(".addAlbumFormParent").attr( "style", "display: block !important;" );

    var categorySelectedId = jQuery("select[name=category] option:selected").val();
    var categorySelectedPath = jQuery("select[name=category]")[0].selectize.getItem(categorySelectedId).text();
    jQuery('.selectedAlbum').show().find('span').html(categorySelectedPath);
    jQuery('.selectAlbumBlock').hide();
  }
});

Piecon.setOptions({
  color: '#ff7700',
  background: '#bbb',
  shadow: '#fff',
  fallback: 'force'
});

var pwg_token = '{$pwg_token}';
var photosUploaded_label = "{'%d photos uploaded'|translate}";
var batch_Label = "{'Manage this set of %d photos'|translate}";
var albumSummary_label = "{'Album "%s" now contains %d photos'|translate|escape}";
var uploadedPhotos = [];
var uploadCategory = null;

{literal}
jQuery(document).ready(function(){
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

	jQuery("#uploader").pluploadQueue({
		// General settings
    browse_button : 'addFiles',
    container : 'uploadForm',
    
		// runtimes : 'html5,flash,silverlight,html4',
		runtimes : 'html5',

		// url : '../upload.php',
		url : 'ws.php?method=pwg.images.upload&format=json',
		
		chunk_size: '{/literal}{$chunk_size}{literal}kb',
		
		filters : {
			// Maximum file size
			max_file_size : '1000mb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : "{/literal}{$file_exts}{literal}"}
			]
		},

		// Rename files by clicking on their titles
		// rename: true,

		// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		dragdrop: true,

    preinit: {
      Init: function (up, info) {
        jQuery('#uploader_container').removeAttr("title"); //remove the "using runtime" text
        
        jQuery('#startUpload').on('click', function(e) {
            e.preventDefault();
            up.start();
          });
        
        jQuery('#cancelUpload').on('click', function(e) {
            e.preventDefault();
            up.stop();
            up.trigger('UploadComplete', up.files);
          });
      }
    },

    init : {
      // update custom button state on queue change
      QueueChanged : function(up) {
        jQuery('#addFiles').addClass("addFilesButtonChanged");
        jQuery('#startUpload').prop('disabled', up.files.length == 0);

        if (up.files.length > 0) {
          jQuery('.plupload_filelist_footer').show();
          jQuery('.plupload_filelist').css("overflow-y", "scroll");
        }

        if (up.files.length == 0) {
          jQuery('#addFiles').removeClass("addFilesButtonChanged");
          jQuery('.plupload_filelist_footer').hide();
          jQuery('.plupload_filelist').css("overflow-y", "hidden");
        }
      },

      UploadProgress: function(up, file) {
        jQuery('#uploadingActions .progressbar').width(up.total.percent+'%');
        Piecon.setProgress(up.total.percent);
      },
      
      BeforeUpload: function(up, file) {
        //console.log('[BeforeUpload]', file);
        
        // hide buttons
        jQuery('#startUpload, .selectFilesButtonBlock, .selectAlbumBlock').hide();
        jQuery('#uploadingActions').show();
        var categorySelectedId = jQuery("select[name=category] option:selected").val();
        var categorySelectedPath = jQuery("select[name=category]")[0].selectize.getItem(categorySelectedId).text();
        jQuery('.selectedAlbum').show().find('span').html(categorySelectedPath);

        // warn user if she wants to leave page while upload is running
        jQuery(window).bind('beforeunload', function() {
          return "{/literal}{'Upload in progress'|translate|escape}{literal}";
        });

        // no more change on category/level
        jQuery("select[name=level]").attr("disabled", "disabled");

        // You can override settings before the file is uploaded
        up.setOption(
          'multipart_params',
          {
            category : jQuery("select[name=category] option:selected").val(),
            level : jQuery("select[name=level] option:selected").val(),
            pwg_token : pwg_token
            // name : file.name
          }
        );
      },

      FileUploaded: function(up, file, info) {
        // Called when file has finished uploading
        //console.log('[FileUploaded] File:', file, "Info:", info);
        
        // hide item line
        jQuery('#'+file.id).hide();
      
        var data = jQuery.parseJSON(info.response);
      
        jQuery("#uploadedPhotos").parent("fieldset").show();
      
        html = '<a href="admin.php?page=photo-'+data.result.image_id+'" target="_blank">';
        html += '<img src="'+data.result.square_src+'" class="thumbnail" title="'+data.result.name+'">';
        html += '</a> ';
      
        jQuery("#uploadedPhotos").prepend(html);

        // do not remove file, or it will reset the progress bar :-/
        // up.removeFile(file);
        uploadedPhotos.push(parseInt(data.result.image_id));
        uploadCategory = data.result.category;
      },

      Error: function(up, error) {
        // Called when file has finished uploading
        //console.log('[Error] error: ', error);
        var piwigoApiResponse = jQuery.parseJSON(error.response);

        jQuery(".errors ul").append('<li>'+piwigoApiResponse.message+'</li>');
        jQuery(".errors").show();
      },

      UploadComplete: function(up, files) {
        // Called when all files are either uploaded or failed
        //console.log('[UploadComplete]');
        
        Piecon.reset();

        jQuery(".selectAlbum, .selectFiles, #permissions, .showFieldset").hide();

        jQuery(".infos").append('<ul><li>'+sprintf(photosUploaded_label, uploadedPhotos.length)+'</li></ul>');

        html = sprintf(
          albumSummary_label,
          '<a href="admin.php?page=album-'+uploadCategory.id+'">'+uploadCategory.label+'</a>',
          parseInt(uploadCategory.nb_photos)
        );

        jQuery(".infos ul").append('<li>'+html+'</li>');

        jQuery(".infos").show();

        // TODO: use a new method pwg.caddie.empty +
        // pwg.caddie.add(uploadedPhotos) instead of relying on huge GET parameter
        // (and remove useless code from admin/photos_add_direct.php)

        jQuery(".batchLink").attr("href", "admin.php?page=photos_add&section=direct&batch="+uploadedPhotos.join(","));
        jQuery(".batchLink").html(sprintf(batch_Label, uploadedPhotos.length));

        jQuery(".afterUploadActions").show();
        jQuery('#uploadingActions').hide();

        // user can safely leave page without warning
        jQuery(window).unbind('beforeunload');
      }
    }
	});
{/literal}
});
{/footer_script}

<div id="photosAddContent">
  <div class="addAlbumEmptyCenter">
    <div class="addAlbumEmpty"{if $NB_ALBUMS > 0} style="display:none;"{/if}>
      <div class="addAlbumEmptyTitle">{'Welcome!'|translate}</div>
      <p class="addAlbumEmptyInfos">{'Piwigo requires an album to add photos.'|translate}</p>
      <a href="#" data-add-album="category" title="{'Create a first album'|translate}" class="buttonLike">{'Create a first album'|translate}</a>
    </div>
  </div>

<div class="infos" style="display:none"><i class="eiw-icon icon-ok"></i></div>
<div class="errors" style="display:none"><i class="eiw-icon icon-cancel"></i><ul></ul></div>

  <p class="afterUploadActions" style="margin:10px; display:none;"> <a class="batchLink icon-pencil"></a><span class="buttonSeparator">{'or'|translate}</span><a href="admin.php?page=photos_add" class="icon-plus-circled">{'Add another set of photos'|@translate}</a></p>

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


  <form id="uploadForm" enctype="multipart/form-data" method="post" action="{$form_action}"{if $NB_ALBUMS == 0} style="display:none;"{/if}>
    <fieldset class="selectAlbum">
      <legend>{'Drop into album'|@translate}</legend>
      <div class="selectedAlbum"{if !isset($ADD_TO_ALBUM)} style="display: none"{/if}><span class="icon-sitemap">{$ADD_TO_ALBUM}</span></div>
      <div class="selectAlbumBlock"{if isset($ADD_TO_ALBUM)} style="display: none"{/if}>
        <a href="#" data-add-album="category" title="{'create a new album'|@translate}" class="icon-plus"></a>
        <span id="albumSelection">
          <select data-selectize="categories" data-value="{$selected_category|@json_encode|escape:html}"
          data-default="first" name="category" style="width:600px"></select>
        </span>
      </div>
    </fieldset>
{*
    <p class="showFieldset"><a id="showPermissions" href="#">{'Manage Permissions'|@translate}</a></p>

    <fieldset id="permissions" style="display:none">
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>
*}
    <fieldset class="selectFiles">
      <legend>{'Select files'|@translate}</legend>
      <div class="selectFilesButtonBlock">
        <button id="addFiles" class="buttonLike">{'Add Photos'|translate}<i class="icon-plus-circled"></i></button>
        <div class="selectFilesinfo">
          {if isset($original_resize_maxheight)}
          <p class="uploadInfo">{'The picture dimensions will be reduced to %dx%d pixels.'|@translate:$original_resize_maxwidth:$original_resize_maxheight}</p>
          {/if}
          <p id="uploadWarningsSummary">{'Allowed file types: %s.'|@translate:$upload_file_types}</p>
          <p>
            {if isset($max_upload_resolution)}
            {'Approximate maximum resolution: %dM pixels (that\'s %dx%d pixels).'|@translate:$max_upload_resolution:$max_upload_width:$max_upload_height}
            {/if}
          </p>
        </div>
      </div>
      <div id="uploader">
        <p>Your browser doesn't have HTML5 support.</p>
      </div>

    </fieldset>
    
    <div id="uploadingActions" style="display:none">
      <button id="cancelUpload" class="buttonLike icon-cancel-circled">{'Cancel'|translate}</button>
      
      <div class="big-progressbar">
        <div class="progressbar" style="width:0%"></div>
      </div>
    </div>

    <button id="startUpload" class="buttonLike icon-upload" disabled>{'Start Upload'|translate}</button>

  </form>

  <fieldset style="display:none" class="Addedphotos">
    <div id="uploadedPhotos"></div>
  </fieldset>

{/if} {* $setup_errors *}

</div> <!-- photosAddContent -->
