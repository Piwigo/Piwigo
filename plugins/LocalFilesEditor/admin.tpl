{if isset($editarea)}
<script type="text/javascript" src="{$editarea.URL}"></script>
<script type="text/javascript">
editAreaLoader.init({ldelim}
	id: "text"
	{foreach from=$editarea.OPTIONS key=option item=value}
  , {$option}: {$value|editarea_quote}
  {/foreach}
{rdelim});
</script>
{/if}

<div class="titrePage">
  <h2>LocalFiles Editor</h2>
</div>

<form method="post" class="properties" action="" ENCTYPE="multipart/form-data">
<div style="text-align:center;">

{if isset ($css_lang_tpl)}
{html_options name=file_to_edit options=$css_lang_tpl.OPTIONS selected=$css_lang_tpl.SELECTED}
<input class="submit" type="submit" value="{'locfiledit_edit'|@translate}" name="edit" />
<br>
<br>
{/if}

{foreach from=$show_default item=file}
<a href="{$file.SHOW_DEFAULT}" onclick="window.open( this.href, 'local_file', 'location=no,toolbar=no,menubar=no,status=no,resizable=yes,scrollbars=yes,width=800,height=600' ); return false;">{'locfiledit_show_default'|@translate} "{$file.FILE}"</a>
<br>
{/foreach}

{if isset ($zone_edit)}
<br>
<input type="text" style="display:none;" value="{$zone_edit.EDITED_FILE}" name="edited_file"/>
<b>{$zone_edit.FILE_NAME}</b>
<br>
<textarea rows="30" name="text" id="text" style="width:90%;">{$zone_edit.CONTENT_FILE}</textarea>
<br>{'locfiledit_save_bak'|@translate}
<br><br>
<input class="submit" type="submit" value="{'locfiledit_save_file'|@translate}" name="submit" {$TAG_INPUT_ENABLED}/>
{if isset ($restore)}
<input class="submit" type="submit" value="{'locfiledit_restore'|@translate}" name="restore" onclick="return confirm('{'locfiledit_restore_confirm'|@translate|escape:'javascript'}');" {$TAG_INPUT_ENABLED}/>
{/if}
{/if}

<br>
</div>
</form>
