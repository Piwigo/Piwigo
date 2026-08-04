{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{combine_script id='jquery.confirm' load='footer' require='jquery' path='themes/default/js/plugins/jquery-confirm.min.js'}
{combine_css path="themes/default/js/plugins/jquery-confirm.min.css"}
{footer_script}
$(".delete-lang-button").each(function() {
  let title_msg = '{'Are you sure you want to delete the language "%s"?'|@translate|@escape:'javascript'}';
  const confirm_msg = '{"Yes, I am sure"|@translate}';
  const cancel_msg = '{"No, I have changed my mind"|@translate|@escape:'javascript'}';
  let lang_name = $(this).closest(".languageBox").find('.languageName').html();
  $(this).pwg_jconfirm_follow_href({
    alert_title: title_msg.replace("%s", lang_name),
    alert_confirm: confirm_msg,
    alert_cancel: cancel_msg
  });
});
{/footer_script}

{foreach from=$language_states item=language_state}
<fieldset>
  <legend>
  {if $language_state == 'active'}
  {'Active Languages'|@translate}

  {elseif $language_state == 'inactive'}
  {'Inactive Languages'|@translate}

  {/if}
  </legend>
  <div class="languageBoxes">
  {foreach from=$languages item=language}
    {if $language.state == $language_state}
  <div class="languageBox{if $language.is_default} languageDefault{/if}">
    <div class="languageName">{$language.name}{if $language.is_default} <em>({'default'|@translate})</em>{/if}</div>
    {if $isWebmaster == 1}
    <div class="languageActions">
      <div>
      {if $language_state == 'active'}
        {if $language.deactivable}
      <a href="{$language.u_action}&amp;action=deactivate" class="tiptip" title="{'Forbid this language to users'|@translate}">{'Deactivate'|@translate}</a>
        {else}
      <span title="{$language.deactivate_tooltip}">{'Deactivate'|@translate}</span>
        {/if}

        {if not $language.is_default}
      | <a href="{$language.u_action}&amp;action=set_default" class="tiptip" title="{'Set as default language for unregistered and new users'|@translate}">{'Default'|@translate}</a>
        {/if}
      {/if}

      {if $language_state == 'inactive'}
      <a href="{$language.u_action}&amp;action=activate" class="tiptip" title="{'Make this language available to users'|@translate}">{'Activate'|@translate}</a>
        {if $CONF_ENABLE_EXTENSIONS_INSTALL}
      | <a href="{$language.u_action}&amp;action=delete" class="tiptip delete-lang-button" title="{'Delete this language'|@translate}">{'Delete'|@translate}</a>
        {/if}
      {/if}
      </div>
    </div> <!-- languageActions -->
    {/if}
  </div> <!-- languageBox -->
    {/if}
  {/foreach}
  </div> <!-- languageBoxes -->
</fieldset>
{/foreach}
