
{include file='include/autosize.inc.tpl'}
{footer_script}{literal}
jQuery(document).ready(function(){

	jQuery("#checkAllLink").click(function () {
		jQuery("#notification_by_mail input[type=checkbox]").prop('checked', true);
		return false;
	});

	jQuery("#uncheckAllLink").click(function () {
		jQuery("#notification_by_mail input[type=checkbox]").prop('checked', false);
		return false;
	});

});
{/literal}{/footer_script}

<form method="post" name="notification_by_mail" id="notification_by_mail" action="{$F_ACTION}">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  {if isset($REPOST_SUBMIT_NAME)}
  <fieldset>
    <div class="infos">
      <input type="submit" value="{'Continue processing treatment'|@translate}" name="{$REPOST_SUBMIT_NAME}">
    </div>
  </fieldset>
  {/if}

  {if isset($param)}
  <fieldset>
    <legend>{'Parameters'|@translate}</legend>
    <table>
      <tr>
        <td><label>{'Send mail on HTML format'|@translate}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_html_mail" value="true"  {if $param.SEND_HTML_MAIL}checked="checked"{/if}>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_html_mail" value="false" {if $param.SEND_HTML_MAIL === "false" || !$param.SEND_HTML_MAIL}checked="checked"{/if}>{'No'|@translate}</label>
        </td>
      </tr>
      <tr>
        <td>
          <label for="send_mail_as">{'Send mail as'|@translate}</label>
          <br><i><small>{'With blank value, gallery title will be used'|@translate}</small></i>
        </td>
        <td><input type="text" maxlength="35" size="35" name="nbm_send_mail_as" id="send_mail_as" value="{$param.SEND_MAIL_AS}"></td>
      </tr>
      <tr>
        <td><label>{'Add detailed content'|@translate}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_detailed_content" value="true"  {if $param.SEND_DETAILED_CONTENT}checked="checked"{/if}>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_detailed_content" value="false" {if !$param.SEND_DETAILED_CONTENT || $param.SEND_DETAILED_CONTENT === "false"}checked="checked"{/if}>{'No'|@translate}</label>
        </td>
      </tr>
     <tr>
        <td><label for="complementary_mail_content">{'Complementary mail content'|@translate}</label></td>
        <td><textarea cols="50" rows="5" name="nbm_complementary_mail_content" id="complementary_mail_content">{$param.COMPLEMENTARY_MAIL_CONTENT}</textarea></td>
      </tr>
      <tr>
        <td>
          <label>{'Include display of recent photos grouped by dates'|@translate}</label>
          <br><i><small>{'Available only with HTML format'|@translate}</small></i>
        </td>
        <td>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="true" {if $param.SEND_RECENT_POST_DATES}checked="checked"{/if}>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="false" {if !$param.SEND_RECENT_POST_DATES || $param.SEND_RECENT_POST_DATES === "false"}checked="checked"{/if}>{'No'|@translate}</label>
        </td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input type="submit" value="{'Submit'|@translate}" name="param_submit">
    <input type="reset" value="{'Reset'|@translate}" name="param_reset">
  </p>
  {/if}{* isset $param*}

  {if isset($subscribe)}
  <fieldset>
    <legend>{'Subscribe/unsubscribe users'|@translate}</legend>
    <p><i>{'Warning: subscribing or unsubscribing will send mails to users'|@translate}</i></p>
    {$DOUBLE_SELECT}
  </fieldset>
  {/if}{* isset $subscribe*}

  {if isset($send)}
    {if empty($send.users)}
    <p>{'There is no available subscribers to mail.'|@translate}</p>
    <p>
    {'Subscribers could be listed (available) only if there is new elements to notify.'|@translate}<br>
    {'Anyway only webmasters can see this tab and never administrators.'|@translate}
    </p>
    {else}
    <fieldset>
      <legend>{'Select recipients'|@translate}</legend>
      <table class="table2">
        <tr class="throw">
          <th>{'User'|@translate}</th>
          <th>{'Email'|@translate}</th>
          <th>{'Last send'|@translate}</th>
          <th>{'To send ?'|@translate}</th>
        </tr>
        {foreach from=$send.users item=u name=user_loop}
        <tr class="{if $smarty.foreach.user_loop.index is odd}row1{else}row2{/if}">
          <td><label for="send_selection-{$u.ID}">{$u.USERNAME}</label></td>
          <td><label for="send_selection-{$u.ID}">{$u.EMAIL}</label></td>
          <td><label for="send_selection-{$u.ID}">{$u.LAST_SEND}</label></td>
          <td><input type="checkbox" name="send_selection[]" value="{$u.ID}" {$u.CHECKED} id="send_selection-{$u.ID}"></td>
        </tr>
        {/foreach}
      </table>
      <p>
          <a href="#" id="checkAllLink">{'Check all'|@translate}</a>
        / <a href="#" id="uncheckAllLink">{'Uncheck all'|@translate}</a>
      </p>
    </fieldset>

    <fieldset>
      <legend>{'Options'|@translate}</legend>
      <table>
       <tr>
          <td><label for="send_customize_mail_content">{'Complementary mail content'|@translate}</label></td>
          <td><textarea cols="50" rows="5" name="send_customize_mail_content" id="send_customize_mail_content">{$send.CUSTOMIZE_MAIL_CONTENT}</textarea></td>
        </tr>
      </table>
    </fieldset>

{if isset($auth_key_duration)}
    <fieldset>
      <legend><span class="icon-info-circled-1 icon-blue"></span>{'Informations'|@translate}</legend>
      <p>
      {'Each email sent will contain its own automatic authentication key on links, valid for %s.'|translate:$auth_key_duration}
      <br>{'For security reason, authentication keys do not work for administrators.'|translate}
      </p>      
    </fieldset>
{/if}

    <p>
      <input type="submit" value="{'Send'|@translate}" name="send_submit">
    </p>
    {/if}
  {/if}{* isset $send*}

</form>
