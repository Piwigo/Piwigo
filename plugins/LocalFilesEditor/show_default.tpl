{html_head}
<style type="text/css">#headbranch, #theHeader, #copyright {ldelim} display: none; }</style>
{/html_head}
{combine_script id="jquery"}

{combine_script id="codemirror" path="plugins/LocalFilesEditor/codemirror/lib/codemirror.js"}
{combine_script id="codemirror.xml" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/xml/xml.js"}
{combine_script id="codemirror.javascript" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/javascript/javascript.js"}
{combine_script id="codemirror.css" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/css/css.js"}
{combine_script id="codemirror.clike" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/clike/clike.js"}
{combine_script id="codemirror.htmlmixed" require="codemirror.xml,codemirror.javascript,codemirror.css" path="plugins/LocalFilesEditor/codemirror/mode/htmlmixed/htmlmixed.js"}
{combine_script id="codemirror.php" require="codemirror.xml,codemirror.javascript,codemirror.css,codemirror.clike" path="plugins/LocalFilesEditor/codemirror/mode/php/php.js"}

{combine_css path="plugins/LocalFilesEditor/codemirror/lib/codemirror.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/xml/xml.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/javascript/javascript.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/css/css.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/clike/clike.css"}
{combine_css path="plugins/LocalFilesEditor/locfiledit.css"}

{footer_script}
function loadCodemirror() {ldelim}
  editor = CodeMirror.fromTextArea(document.getElementById("text"), {ldelim}
    matchBrackets: true,
    readOnly: true,
    mode: "{$CODEMIRROR_MODE}",
    tabMode: "shift"
  });
  jQuery("#showedit").hide();
  jQuery("#hideedit").show();
  jQuery.post("update_config.php", {ldelim} editarea: "on"});
}

function unloadCodemirror() {ldelim} 
  editor.toTextArea();
  jQuery("#hideedit").hide();
  jQuery("#showedit").show();
  jQuery.post("update_config.php", {ldelim} editarea: "off"});
}

{if $LOAD_CODEMIRROR == 'on'}
if (document.getElementById("text") != null) loadCodemirror();
{/if}
{/footer_script}

<div id="LocalFilesEditor">
<div style="overflow:auto;"><b>{$TITLE}</b></div>

<textarea id="text" rows="30" cols="90">{$DEFAULT_CONTENT}</textarea>

<div id="editarea_buttons">
<a href="javascript:loadCodemirror();" id="showedit">[{'locfiledit_enable_codemirror'|@translate}]</a>
<a href="javascript:unloadCodemirror();" id="hideedit">[{'locfiledit_disable_codemirror'|@translate}]</a>
</div>

</div>
