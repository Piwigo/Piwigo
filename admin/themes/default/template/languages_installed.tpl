<div class="titrePage">
  <h2>{'Installed Languages'|@translate}</h2>
</div>

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
      | <a href="{$language.u_action}&amp;action=delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');" class="tiptip" title="{'Delete this language'|@translate}">{'Delete'|@translate}</a>
      {/if}
      </div>
    </div> <!-- languageActions -->
  </div> <!-- languageBox -->
    {/if}
  {/foreach}
  </div> <!-- languageBoxes -->
</fieldset>
{/foreach}
