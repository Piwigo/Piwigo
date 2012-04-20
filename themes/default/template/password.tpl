{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">
  <div class="titrePage">
    <ul class="categoryActions">
    </ul>

    <h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{$title}</h2>
  </div>

{include file='infos_errors.tpl'}

{if $action ne 'none'}
<form id="lostPassword" action="{$form_action}?action={$action}{if isset($key)}&amp;key={$key}{/if}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  {if $action eq 'lost'}
  <div class="message">{'Please enter your username or email address.'|@translate} {'You will receive a link to create a new password via email.'|@translate}</div>

  <p>
    <label>
      {'Username or email'|@translate}
      <br>
      <input type="text" id="username_or_email" name="username_or_email" size="40" maxlength="40"{if isset($username_or_email)} value="{$username_or_email}"{/if}>
    </label>
  </p>

  <p class="bottomButtons"><input type="submit" name="submit" value="{'Change my password'|@translate}"></p>
  {elseif $action eq 'reset'}

  <div class="message">{'Hello'|@translate} <em>{$username}</em>. {'Enter your new password below.'|@translate}</div>

  <p>
    <label>
      {'New password'|@translate}
      <br>
      <input type="password" name="use_new_pwd" id="use_new_pwd" value="">
    </label>
  </p>

  <p>
    <label>
      {'Confirm Password'|@translate}
      <br>
      <input type="password" name="passwordConf" id="passwordConf" value="">
    </label>
  </p>

  <p class="bottomButtons"><input type="submit" name="submit" value="{'Submit'|@translate}"></p>
  {/if}

</form>
{/if} {* $action ne 'none' *}

<script type="text/javascript">
{if $action eq 'lost'}
{literal}try{document.getElementById('username_or_email').focus();}catch(e){}{/literal}
{elseif $action eq 'reset'}
{literal}try{document.getElementById('use_new_pwd').focus();}catch(e){}{/literal}
{/if}
</script>

</div> <!-- content -->
