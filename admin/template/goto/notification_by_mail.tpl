{* $Id$ *}

{known_script id="jquery.growfield" src=$ROOT_URL|@cat:"template-common/lib/plugins/jquery.growfield.packed.js"}

{include file='include/autosize.inc.tpl'}

<div class="titrePage">
  <h2>{'nbm_send_mail_to_users'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form method="post" name="notification_by_mail" id="notification_by_mail" action="{$F_ACTION}">
  {if isset($REPOST_SUBMIT_NAME)}
  <fieldset>
    <div class="infos">
      <input class="submit" type="submit" value="{'nbm_repost_submit'|@translate}" name="{$REPOST_SUBMIT_NAME}" {$TAG_INPUT_ENABLED}/>
    </div>
  </fieldset>
  {/if}

  {if isset($param)}
  <fieldset>
    <legend>{'nbm_title_param'|@translate}</legend>
    <table>
      <tr>
        <td><label>{'nbm_send_html_mail'|@translate}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_html_mail" value="true"  {if $param.SEND_HTML_MAIL}checked="checked"{/if}/>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_html_mail" value="false" {if not $param.SEND_HTML_MAIL}checked="checked"{/if}/>{'No'|@translate}</label>
        </td>
      </tr>
      <tr>
        <td>
          <label for="send_mail_as">{'nbm_send_mail_as'|@translate}</label>
          <br/><i><small>{'nbm_info_send_mail_as'|@translate}</small></i>
        </td>
        <td><input type="text" maxlength="35" size="35" name="nbm_send_mail_as" id="send_mail_as" value="{$param.SEND_MAIL_AS}"/></td>
      </tr>
      <tr>
        <td><label>{'nbm_send_detailed_content'|@translate}</label></td>
        <td>
          <label><input type="radio" name="nbm_send_detailed_content" value="true"  {if $param.SEND_DETAILED_CONTENT}checked="checked"{/if}/>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_detailed_content" value="false" {if not $param.SEND_DETAILED_CONTENT}checked="checked"{/if}/>{'No'|@translate}</label>
        </td>
      </tr>
     <tr>
        <td><label for="complementary_mail_content">{'nbm_complementary_mail_content'|@translate}</label></td>
        <td><textarea cols="50" rows="5" name="nbm_complementary_mail_content" id="complementary_mail_content">{$param.COMPLEMENTARY_MAIL_CONTENT}</textarea></td>
      </tr>
      <tr>
        <td>
          <label>{'nbm_send_recent_post_dates'|@translate}</label>
          <br/><i><small>{'nbm_info_send_recent_post_dates'|@translate}</small></i>
        </td>
        <td>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="true" {if $param.SEND_RECENT_POST_DATES}checked="checked"{/if}/>{'Yes'|@translate}</label>
          <label><input type="radio" name="nbm_send_recent_post_dates" value="false" {if not $param.SEND_RECENT_POST_DATES}checked="checked"{/if}/>{'No'|@translate}</label>
        </td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="param_submit" {$TAG_INPUT_ENABLED}/>
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="param_reset"/>
  </p>
  {/if}{* isset $param*}

  {if isset($subscribe)}
  <fieldset>
    <legend>{'nbm_title_subscribe'|@translate}</legend>
    <p><i>{'nbm_warning_subscribe_unsubscribe'|@translate}</i></p>
    {$DOUBLE_SELECT}
  </fieldset>
  {/if}{* isset $subscribe*}

  {if isset($send)}
    {if empty($send.users)}
    <p>{'nbm_no_user_available_to_send_L1'|@translate}</p>
    <p>
    {'nbm_no_user_available_to_send_L2'|@translate}<br>
    {'nbm_no_user_available_to_send_L3'|@translate}
    </p>
    {else}
    <fieldset>
      <legend>{'nbm_title_send'|@translate}</legend>
      <table class="table2">
        <tr class="throw">
          <th>{'nbm_col_user'|@translate}</th>
          <th>{'nbm_col_mail'|@translate}</th>
          <th>{'nbm_col_last_send'|@translate}</th>
          <th>{'nbm_col_check_user_send_mail'|@translate}</th>
        </tr>
        {foreach from=$send.users item=u name=user_loop}
        <tr class="{if $smarty.foreach.user_loop.index is odd}row1{else}row2{/if}">
          <td><label for="send_selection-{$u.ID}">{$u.USERNAME}</label></td>
          <td><label for="send_selection-{$u.ID}">{$u.EMAIL}</label></td>
          <td><label for="send_selection-{$u.ID}">{$u.LAST_SEND}</label></td>
          <td><input type="checkbox" name="send_selection[]" value="{$u.ID}" {$u.CHECKED} id="send_selection-{$u.ID}"/></td>
        </tr>
        {/foreach}
      </table>
      <p>
          <a href="#" onclick="SelectAll(document.getElementById('notification_by_mail')); return false;">{'Check all'|@translate}</a>
        / <a href="#" onclick="DeselectAll(document.getElementById('notification_by_mail')); return false;">{'Uncheck all'|@translate}</a>
      </p>
    </fieldset>

    <fieldset>
      <legend>{'nbm_send_options'|@translate}</legend>
      <table>
       <tr>
          <td><label for="send_customize_mail_content">{'nbm_send_complementary_mail_content'|@translate}</label></td>
          <td><textarea cols="50" rows="5" name="send_customize_mail_content" id="send_customize_mail_content">{$send.CUSTOMIZE_MAIL_CONTENT}</textarea></td>
        </tr>
      </table>
    </fieldset>

    <p>
      <input class="submit" type="submit" value="{'nbm_send_submit'|@translate}" name="send_submit" {$TAG_INPUT_ENABLED}/>
    </p>
    {/if}
  {/if}{* isset $send*}

</form>
