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
{combine_css path="plugins/LocalFilesEditor/template/locfiledit.css"}

{footer_script}
var editor = CodeMirror.fromTextArea(document.getElementById("text"), {ldelim}
  readOnly: true,
  mode: "application/x-httpd-php"
});
{/footer_script}

{html_head}
<style type="text/css">
#headbranch, #theHeader, #copyright {ldelim} display: none; }
</style>
{/html_head}

<div id="LocalFilesEditor">

<div id="title_bar">
  <span class="file_name">{$TITLE}</span>
</div>

<textarea id="text" rows="30" cols="90" class="show_default_area">{$DEFAULT_CONTENT}</textarea>

</div>
