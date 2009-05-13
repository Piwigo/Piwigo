{html_head}
<link rel="stylesheet" type="text/css" href="{$LOCALEDIT_PATH}locfiledit.css">
<style type="text/css">#headbranch, #theHeader, #copyright {ldelim} display: none; }</style>
{/html_head}
{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="editarea" src=$LOCALEDIT_PATH|@cat:"editarea/edit_area_full.js"}
<script type="text/javascript">
var editarea = "{$LOAD_EDITAREA}";

function loadEditarea() {ldelim} 
  editAreaLoader.init({ldelim}
    id: "text"
    {foreach from=$EDITAREA_OPTIONS key=option item=value}
    , {$option}: {$value|editarea_quote}
    {/foreach}
  });
  jQuery("#showedit").hide();
  jQuery("#hideedit").show();
}

function unloadEditarea() {ldelim} 
  editAreaLoader.delete_instance("text");
  jQuery("#hideedit").hide();
  jQuery("#showedit").show();
}
</script>

<div id="LocalFilesEditor">
<h1>{$TITLE}</h1>

<textarea id="text" rows="30" cols="90">{$DEFAULT_CONTENT}</textarea>

<div id="editarea_buttons">
<a href="javascript:loadEditarea();" id="showedit">[{'locfiledit_enable_editarea'|@translate}]</a>
<a href="javascript:unloadEditarea();" id="hideedit">[{'locfiledit_disable_editarea'|@translate}]</a>
</div>

</div>

<script type="text/javascript">
jQuery("#editarea_buttons").show();
if (editarea == "on") loadEditarea();
</script>
