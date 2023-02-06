{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.plupload' load='footer' require='jquery' path='themes/default/js/plugins/plupload/plupload.full.min.js'}
{combine_script id='jquery.plupload.queue' load='footer' require='jquery' path='themes/default/js/plugins/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}

{combine_css path="themes/default/js/plugins/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css"}

{assign var="plupload_i18n" value="themes/default/js/plugins/plupload/i18n/`$lang_info.plupload_code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$plupload_i18n|@file_exists}
  {combine_script id="plupload_i18n-`$lang_info.plupload_code`" load="footer" path=$plupload_i18n require="jquery.plupload.queue"}
{/if}

{include file='include/colorbox.inc.tpl'}
{if !$DISPLAY_FORMATS}
  {include file='include/add_album.inc.tpl'}
{/if}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='piecon' load='footer' path='themes/default/js/plugins/piecon.js'}

{html_style}
.addAlbumFormParent { display: none; } /* specific to this page, do not move in theme.css */
{/html_style}

{footer_script}

const formatMode = {if $DISPLAY_FORMATS}true{else}false{/if};
const haveFormatsOriginal = {if $HAVE_FORMATS_ORIGINAL}true{else}false{/if};
const originalImageId = haveFormatsOriginal? '{if isset($FORMATS_ORIGINAL_INFO['id'])} {$FORMATS_ORIGINAL_INFO['id']} {else} -1 {/if}' : -1;

{* <!-- CATEGORIES --> *}
if (!formatMode) {
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
}

var pwg_token = '{$pwg_token}';
var photosUploaded_label = "{'%d photos uploaded'|translate}";
var formatsUploaded_label = "{'%d formats uploaded for %d photos'|translate}";
var batch_Label = "{'Manage this set of %d photos'|translate}";
var albumSummary_label = "{'Album "%s" now contains %d photos'|translate|escape}";
var str_format_warning = "{'Error when trying to detect formats'|translate}";
var str_ok = "{'Ok'|translate}";
var str_format_warning_multiple = "{'There is multiple image in the database with the following names : %s.'|translate}";
var str_format_warning_notFound = "{'No picture found with the following name : %s.'|translate}";
var str_and_X_others = "{'and %d more'|translate}";
var file_ext = "{$file_exts}";
var format_ext = "{$format_ext}"; 
var uploadedPhotos = [];
var uploadCategory = null;

{literal}
jQuery(document).ready(function(){
  jQuery(".dont-show-again").on("click", function() {
    jQuery.ajax({
      url: "ws.php?format=json&method=pwg.users.preferences.set",
      type: "POST",
      dataType: "JSON",
      data: {
        param: 'promote-mobile-apps',
        value: false,
      },
      success: function(res) {
        jQuery(".promote-apps").hide();
      }
    })
  })

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
			max_file_size : '{/literal}{$max_file_size}{literal}mb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : formatMode ? format_ext : file_ext}
			]
		},

		// Rename files by clicking on their titles
		rename: formatMode,

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
        jQuery("#addFiles").removeClass('buttonGradient').addClass('buttonLike');

        if (up.files.length > 0) {
          jQuery('.plupload_filelist_footer').show();
          jQuery('.plupload_filelist').css("overflow-y", "scroll");
        }

        if (up.files.length == 0) {
          jQuery('#addFiles').removeClass("addFilesButtonChanged");
          jQuery("#addFiles").removeClass('buttonLike').addClass('buttonGradient');
          jQuery('.plupload_filelist_footer').hide();
          jQuery('.plupload_filelist').css("overflow-y", "hidden");
        }
      },

      FilesAdded: async function(up, files) {
        // Création de la liste avec plupload_id : image_name
        fileNames = {};
        files.forEach((file) => {
          fileNames[file.id] = file.name;
        });

        if (formatMode) {
          // If no original image is specified
          if (!haveFormatsOriginal) {
            const images_search = await new Promise((res, rej) => {
              //ajax qui renvois les id des images dans la gallerie.
              jQuery.ajax({
                url: "ws.php?format=json&method=pwg.images.formats.searchImage",
                type: "POST",
                data: {
                  category_id: jQuery("select[name=category] option:selected").val(),
                  filename_list: JSON.stringify(fileNames),
                },
                success: function(result) {
                  let data = JSON.parse(result);
                  res(data.result)
                }
              })
            })

            const notFound = [];
            const multiple = [];

            files.forEach((f) => {
              const search = images_search[f.id];
              if (search.status == "found") 
                f.format_of = search.image_id;
              else {
                if (search.status == "multiple")
                  multiple.push(f.name);
                else 
                  notFound.push(f.name);
                up.removeFile(f.id);
              } 
            })

            files.filter(f => images_search[f.id].status === "found");

            // If a file is not found or found more than one time
            if (notFound.length || multiple.length) {
              const [multStr, notFoundStr] = [multiple, notFound].map((tab) => {
                //Get names
                tab = tab.map(f => f.slice(0,f.indexOf('.')))
                // Remove duplicates
                tab = tab.filter((f,i) => i === tab.indexOf(f))

                // Add "and X more" if necessary
                if (tab.length > 5) {
                  tab[5] = str_and_X_others.replace('%d', tab.length - 5);
                  tab = tab.splice(0,6);
                }
                return tab;
              })

              $.alert({
                title: str_format_warning,
                content : (notFound.length ? `<p>${str_format_warning_notFound.replace('%s', notFoundStr.join(', '))}</p>` : "")
                  +(multiple.length ? `<p>${str_format_warning_multiple.replace('%s', multStr.join(', '))}</p>` : ""),
                ...jConfirm_warning_options
              })
            }
          } else { //If an original image is specified
            files.forEach((f) => {
              f.format_of = originalImageId;
            })
          }
        }
      },

      UploadProgress: function(up, file) {
        jQuery('#uploadingActions .progressbar').width(up.total.percent+'%');
        Piecon.setProgress(up.total.percent);
      },
      
      BeforeUpload: function(up, file) {
        // hide buttons
        jQuery('#startUpload, .selectFilesButtonBlock, .selectAlbumBlock').hide();
        jQuery('#uploadingActions').show();
        jQuery('.format-mode-group-manager').hide();
        if (!formatMode) {
          var categorySelectedId = jQuery("select[name=category] option:selected").val();
          var categorySelectedPath = jQuery("select[name=category]")[0].selectize.getItem(categorySelectedId).text();
          jQuery('.selectedAlbum').show().find('span').html(categorySelectedPath);
        }

        // warn user if she wants to leave page while upload is running
        jQuery(window).bind('beforeunload', function() {
          return "{/literal}{'Upload in progress'|translate|escape}{literal}";
        });

        // no more change on category/level
        jQuery("select[name=level]").attr("disabled", "disabled");

        // You can override settings before the file is uploaded
        var options = {
          pwg_token : pwg_token
        };

        if (formatMode) {
          options.format_of = file.format_of;
        } else {
          options.category = jQuery("select[name=category] option:selected").val();
          options.level = jQuery("select[name=level] option:selected").val();
          options.name = file.name;
        }

        up.setOption('multipart_params', options);
      },

      FileUploaded: function(up, file, info) {
        // Called when file has finished uploading
        //console.log('[FileUploaded] File:', file, "Info:", info);
        
        // hide item line
        jQuery('#'+file.id).hide();

        let data = jQuery.parseJSON(info.response);
      
        jQuery("#uploadedPhotos").parent("fieldset").show();
      
        html = '<a href="admin.php?page=photo-'+data.result.image_id+'" style="position : relative" target="_blank">';
        html += '<img src="'+data.result.square_src+'" class="thumbnail" title="'+data.result.name+'">';
        if (formatMode) html += '<div class="format-ext-name" title="'+file.name+'"><span>'+file.name.slice(file.name.indexOf('.'))+'</span></div>';
        html += '</a> ';
      
        jQuery("#uploadedPhotos").prepend(html);

        // do not remove file, or it will reset the progress bar :-/
        // up.removeFile(file);
        uploadedPhotos.push(parseInt(data.result.image_id));
        if (!formatMode)
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

        if (!formatMode) {
          jQuery.ajax({
            url: "ws.php?format=json&method=pwg.images.uploadCompleted",
            type:"POST",
            data: {
              pwg_token: pwg_token,
              image_id: uploadedPhotos.join(","),
              category_id: uploadCategory.id,
            }
          });
        }

        jQuery("#uploadForm, #permissions, .showFieldset").hide();

        const infoText = formatMode?
          sprintf(formatsUploaded_label, uploadedPhotos.length, [...new Set(files.map(f => f.format_of))].length)
          : sprintf(photosUploaded_label, uploadedPhotos.length)

        jQuery(".infos").append('<ul><li>'+infoText+'</li></ul>');


        if (!formatMode) {
          html = sprintf(
            albumSummary_label,
            '<a href="admin.php?page=album-'+uploadCategory.id+'">'+uploadCategory.label+'</a>',
            parseInt(uploadCategory.nb_photos)
          );

          jQuery(".infos ul").append('<li>'+html+'</li>');
        }

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
  {/if} {* $setup_errors *}
  
  {if $PROMOTE_MOBILE_APPS}
    <div class="promote-apps">
      <div class="promote-content">
        <div class="left-side">
          <img src="https://sandbox.piwigo.com/uploads/4/y/1/4y1zzhnrnw//2023/01/24/20230124175152-015bc1e3.png">
        </div>
        <div class="promote-text">
          <span>{"Piwigo is also on mobile."|@translate|escape:javascript}</span>
          <span>{"Try now !"|@translate|escape:javascript}</span>
        </div>
        <div class="right-side">
          <div>
            <a href="{$PHPWG_URL}/mobile-applications" target="_blank"><span class="go-to-porg icon-link-1">{"Discover"|@translate|escape:javascript}</span></a>
          </div>
        </div>
      </div>
      <span class="dont-show-again icon-cancel tiptip" title="{'Understood, do not show again'|translate|escape:javascript}"></span>
    </div>
  {/if}

  {if $ENABLE_FORMATS}
    <div class="format-mode-group-manager">
    <label class="switch" onClick="window.location.replace('{$SWITCH_MODE_URL}'); $('.switch .slider').addClass('loading');">
      <input type="checkbox" id="toggleFormatMode" {if $DISPLAY_FORMATS}checked{/if}>
      <span class="slider round"></span>
    </label>
      <p>{'Upload Formats'|@translate}</p>
    </div>
  {/if}

  {if !$DISPLAY_FORMATS}
  <div class="addAlbumEmptyCenter"{if $NB_ALBUMS > 0} style="display:none;"{/if}>
    <div class="addAlbumEmpty">
      <div class="addAlbumEmptyTitle">{'Welcome!'|translate}</div>
      <p class="addAlbumEmptyInfos">{'Piwigo requires an album to add photos.'|translate}</p>
      <a href="#" data-add-album="category" class="buttonLike">{'Create a first album'|translate}</a>
    </div>
  </div>
  {/if}

<div class="infos" style="display:none"><i class="eiw-icon icon-ok"></i></div>
<div class="errors" style="display:none"><i class="eiw-icon icon-cancel"></i><ul></ul></div>

<p class="afterUploadActions" style="margin:10px; display:none;"> 
  {if !$DISPLAY_FORMATS}
    <a class="batchLink icon-pencil"></a><span class="buttonSeparator">{'or'|translate}</span><a href="admin.php?page=photos_add" class="icon-plus-circled">{'Add another set of photos'|@translate}</a>
  {else}
    <a href="admin.php?page=photos_add&formats" class="icon-plus-circled">{'Add another set of formats'|@translate}</a>
  {/if}
</p>

  <form id="uploadForm" class="{if $DISPLAY_FORMATS}format-mode{/if}" enctype="multipart/form-data" method="post" action="{$form_action}"{if $NB_ALBUMS == 0} style="display:none;"{/if}>
    {if not $DISPLAY_FORMATS}
    <fieldset class="selectAlbum">
      <legend><span class="icon-folder-open icon-red"></span>{'Drop into album'|@translate}</legend>
      <div class="selectedAlbum"{if !isset($ADD_TO_ALBUM)} style="display: none"{/if}><span class="icon-sitemap">{$ADD_TO_ALBUM}</span></div>
      <div class="selectAlbumBlock"{if isset($ADD_TO_ALBUM)} style="display: none"{/if}>
        <span id="albumSelection">
          <select data-selectize="categories" data-value="{$selected_category|@json_encode|escape:html}"
          data-default="first" name="category" style="width:600px"></select>
        </span>
        <span class="orChoice">{'... or '|@translate} </span>
        <a href="#" data-add-album="category" class="orCreateAlbum icon-plus-circled"> {'create a new album'|@translate}</a>
      </div>
    </fieldset>
    {elseif $HAVE_FORMATS_ORIGINAL}
    <fieldset class="originalPicture">
      <legend><span class="icon-link-1 icon-red"></span>{'Picture to associate formats with'|@translate}</legend>
      <a class='info-framed' href='{$FORMATS_ORIGINAL_INFO['u_edit']}' title='{'Edit photo'|@translate}'>
        <div class='info-framed-icon'>
          <img src='{$FORMATS_ORIGINAL_INFO['src']}'></i>
        </div>
        <div class='info-framed-container'>
          <div class='info-framed-title'>{$FORMATS_ORIGINAL_INFO['name']}</div>
          {if isset($FORMATS_ORIGINAL_INFO['formats'])}<div>{$FORMATS_ORIGINAL_INFO['formats']}</div>{/if}
          <div>{$FORMATS_ORIGINAL_INFO.ext}</div>
        </div>
      </a>
    </fieldset>
    {/if}
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
      <legend><span class="icon-file-image icon-yellow"></span>{'Select files'|@translate}</legend>
      <div class="selectFilesButtonBlock">
        <button id="addFiles" class="buttonGradient">{if not $DISPLAY_FORMATS}{'Add Photos'|translate}{else}{'Add formats'|@translate}{/if}<i class="icon-plus-circled"></i></button>
        <div class="selectFilesinfo">
          {if isset($original_resize_maxheight)}
          <p class="uploadInfo">{'The picture dimensions will be reduced to %dx%d pixels.'|@translate:$original_resize_maxwidth:$original_resize_maxheight}</p>
          {/if}
            <p id="uploadWarningsSummary">
            {if not $DISPLAY_FORMATS}
              {'Allowed file types: %s.'|@translate:$upload_file_types}
            {else}
              {'Allowed file types: %s.'|@translate:$str_format_ext} 
              {if !$HAVE_FORMATS_ORIGINAL}<p>{'The original picture will be detected with the filename (without extension).'|@translate}</p>{/if}
            {/if}
            </p>
          </p>
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
      <div class="big-progressbar" style="max-width:98%;margin-bottom: 10px;">
        <div class="progressbar" style="width:0%"></div>
      </div>
      <button id="cancelUpload" class="buttonLike icon-cancel-circled">{'Cancel'|translate}</button>
    </div>

    <button id="startUpload" class="buttonGradient icon-upload" disabled>{'Start Upload'|translate}</button>

  </form>

  <fieldset style="display:none" class="Addedphotos">
    <div id="uploadedPhotos"></div>
  </fieldset>

</div> <!-- photosAddContent -->
