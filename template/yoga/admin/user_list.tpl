{* $Id$ *}
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'title_liste_users'|@translate}</h2>
</div>

<form class="filter" method="post" name="add_user" action="{$F_ADD_ACTION}">
  <fieldset>
    <legend>{'Add a user'|@translate}</legend>
    <label>{'Username'|@translate} <input type="text" name="login" maxlength="50" size="20" /></label>
    <label>{'Password'|@translate} <input type="text" name="password" /></label>
    <label>{'Email address'|@translate} <input type="text" name="email" /></label>
    <input class="submit" type="submit" name="submit_add" value="{'submit'|@translate}" {$TAG_INPUT_ENABLED} />
  </fieldset>
</form>

<form class="filter" method="get" name="filter" action="{$F_FILTER_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
  <input type="hidden" name="page" value="user_list" />

  <label>{'Username'|@translate} <input type="text" name="username" value="{$F_USERNAME}" /></label>

  <label>
  {'status'|@translate}
  {html_options name=status options=$status_options selected=$status_selected}
  </label>

  <label>
  {'group'|@translate}
  {html_options name=group options=$group_options selected=$group_selected}
  </label>

  <label>
  {'Sort by'|@translate}
  {html_options name=order_by options=$order_options selected=$order_selected}
  </label>

  <label>
  {'Sort order'|@translate}
  {html_options name=direction options=$direction_options selected=$direction_selected}
  </label>

  <input class="submit" type="submit" value="{'submit'|@translate}" />

</fieldset>

</form>

<form method="post" name="preferences" action="{$F_PREF_ACTION}">

<table class="table2">
  <tr class="throw">
    <th>&nbsp;</th>
    <th>{'Username'|@translate}</th>
    <th>{'user_status'|@translate}</th>
    <th>{'Email address'|@translate}</th>
    <th>{'Groups'|@translate}</th>
    <th>{'properties'|@translate}</th>
    {foreach from=$cpl_title_user item=title}
    <th>{$title}</th>
    {/foreach}
    <th>{'Actions'|@translate}</th>
  </tr>
  {foreach from=$users item=user name=users_loop}
  <tr class="{if $smarty.foreach.users_loop.index is odd}row1{else}row2{/if}">
    <td><input type="checkbox" name="selection[]" value="{$user.ID}" {$user.CHECKED} id="selection-{$user.ID}" /></td>
    <td><label for="selection-{$user.ID}">{$user.USERNAME}</label></td>
    <td>{$user.STATUS}</td>
    <td>{$user.EMAIL}</td>
    <td>{$user.GROUPS}</td>
    <td>{$user.PROPERTIES}</td>
    {foreach from=$cpl_user[$smarty.foreach.users_loop.index] item=data}
    <td>{$data}</td>
    {/foreach}
    <td style="text-align:center;">
      <a href="{$user.U_PERM}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/permissions.png" class="button" style="border:none" alt="{'permissions'|@translate}" title="{'permissions'|@translate}" /></a>
      <a href="{$user.U_PROFILE}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/edit_s.png" class="button" style="border:none" alt="{'Profile'|@translate}" title="{'Profile'|@translate}" /></a>
      {foreach from=$cpl_link_user[$smarty.foreach.users_loop.index] item=data}
      {$data}
      {/foreach}
      </td>
  </tr>
  {/foreach}
</table>

<div class="navigationBar">{$NAVBAR}</div>

{* delete the selected users ? *}
<fieldset>
  <legend>{'Deletions'|@translate}</legend>
  <label><input type="checkbox" name="confirm_deletion" value="1" /> {'confirm'|@translate}</label>
  <input class="submit" type="submit" value="{'Delete selected users'|@translate}" name="delete" {$TAG_INPUT_ENABLED}/>
</fieldset>

<fieldset>
  <legend>{'Status'|@translate}</legend>

  <table>
    <tr>
      <td>{'Status'|@translate}</td>
      <td>
        <label><input type="radio" name="status_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
        <label><input type="radio" name="status_action" value="set" id="status_action_set" {$STATUS_ACTION_SET} /> {'set to'|@translate}</label>
        <select onmousedown="document.getElementById('status_action_set').checked = true;" name="status" size="1">
          {html_options options=$pref_status_options selected=$pref_status_selected}
        </select>
      </td>
    </tr>

    {if isset($adviser)}
    <tr>
      <td>{'adviser'|@translate}</td>
      <td>
        <label><input type="radio" name="adviser" value="leave" checked="checked" /> {'leave'|@translate}</label>
        / {'set to'|@translate}
        <label><input type="radio" name="adviser" value="true"  {$ADVISER_YES} />{'Yes'|@translate}</label>
        <label><input type="radio" name="adviser" value="false" {$ADVISER_NO}  />{'No'|@translate}</label>
      </td>
    </tr>
    {/if}

  </table>
</fieldset>

{* form to set properties for many users at once *}
<fieldset>
  <legend>{'Groups'|@translate}</legend>

<table>

  <tr>
    <td>{'associate to group'|@translate}</td>
    <td>
      {html_options name=associate options=$association_options selected=$associate_selected}
    </td>
  </tr>

  <tr>
    <td>{'dissociate from group'|@translate}</td>
    <td>
      {html_options name=dissociate options=$association_options selected=$dissociate_selected}
    </td>
  </tr>

</table>

</fieldset>

{* Properties *}
<fieldset>
  <legend>{'properties'|@translate}</legend>

  <table>

    <tr>
      <td>{'enabled_high'|@translate}</td>
      <td>
        <label><input type="radio" name="enabled_high" value="leave" checked="checked" /> {'leave'|@translate}</label>
        / {'set to'|@translate}
        <label><input type="radio" name="enabled_high" value="true"  {$ENABLED_HIGH_YES} />{'Yes'|@translate}</label>
        <label><input type="radio" name="enabled_high" value="false" {$ENABLED_HIGH_NO}  />{'No'|@translate}</label>
      </td>
    </tr>

	<tr>
		<td>{'Privacy level'|@translate}</td>
		<td>
			<label><input type="radio" name="level_action" value="leave" checked="checked" />{'leave'|@translate}</label>
			<label><input type="radio" name="level_action" value="set" id="level_action_set" {$LEVEL_ACTION_SET} />{'set to'|@translate}</label>
			<select onmousedown="document.getElementById('level_action_set').checked = true;" name="level" size="1">
			  {html_options options=$level_options selected=$level_selected}
			</select>
	  </td>
	</tr>

  </table>

</fieldset>

{* preference *}
<fieldset>
  <legend>{'Preferences'|@translate}</legend>

<table>

  <tr>
    <td>{'nb_image_per_row'|@translate}</td>
    <td>
      <label><input type="radio" name="nb_image_line_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="nb_image_line_action" value="set" id="nb_image_line_action_set" {$NB_IMAGE_LINE_ACTION_SET} /> {'set to'|@translate}</label>
      <input onmousedown="document.getElementById('nb_image_line_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_image_line" value="{$NB_IMAGE_LINE}" />
    </td>
  </tr>

  <tr>
    <td>{'nb_row_per_page'|@translate}</td>
    <td>
      <label><input type="radio" name="nb_line_page_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="nb_line_page_action" value="set" id="nb_line_page_action_set" {$NB_LINE_PAGE_ACTION_SET} /> {'set to'|@translate}</label>
      <input onmousedown="document.getElementById('nb_line_page_action_set').checked = true;"
             size="3" maxlength="2" type="text" name="nb_line_page" value="{$NB_LINE_PAGE}" />
    <td>
  </tr>

  <tr>
    <td>{'theme'|@translate}</td>
    <td>
      <label><input type="radio" name="template_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="template_action" value="set" id="template_action_set" {$TEMPLATE_ACTION_SET} /> {'set to'|@translate}</label>
      <select onmousedown="document.getElementById('template_action_set').checked = true;" name="template" size="1">
        {html_options values=$template_options output=$template_options selected=$template_selected}
      </select>
    </td>
  </tr>

  <tr>
    <td>{'language'|@translate}</td>
    <td>
      <label><input type="radio" name="language_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="language_action" value="set" id="language_action_set" {$LANGUAGE_ACTION_SET} /> {'set to'|@translate}</label>
      <select onmousedown="document.getElementById('language_action_set').checked = true;" name="language" size="1">
        {html_options options=$language_options selected=$language_selected}
      </select>
    </td>
  </tr>

  <tr>
    <td>{'recent_period'|@translate}</td>
    <td>
      <label><input type="radio" name="recent_period_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="recent_period_action" value="set" id="recent_period_action_set" {$RECENT_PERIOD_ACTION_SET} /> {'set to'|@translate}</label>
      <input onmousedown="document.getElementById('recent_period_action_set').checked = true;"
             type="text" size="3" maxlength="2" name="recent_period" value="{$RECENT_PERIOD}" />
    </td>
  </tr>

  <tr>
    <td>{'auto_expand'|@translate}</td>
    <td>
      <label><input type="radio" name="expand" value="leave" checked="checked" /> {'leave'|@translate}</label>
      / {'set to'|@translate}
      <label><input type="radio" name="expand" value="true"  {$EXPAND_YES} />{'Yes'|@translate}</label>
      <label><input type="radio" name="expand" value="false" {$EXPAND_NO}  />{'No'|@translate}</label>
    </td>
  </tr>

  <tr>
    <td>{'show_nb_comments'|@translate}</td>
    <td>
      <label><input type="radio" name="show_nb_comments" value="leave" checked="checked" /> {'leave'|@translate}</label>
      / {'set to'|@translate}
      <label><input type="radio" name="show_nb_comments" value="true" {$SHOW_NB_COMMENTS_YES} />{'Yes'|@translate}</label>
      <label><input type="radio" name="show_nb_comments" value="false" {$SHOW_NB_COMMENTS_NO} />{'No'|@translate}</label>
    </td>
  </tr>

  <tr>
    <td>{'show_nb_hits'|@translate}</td>
    <td>
      <label><input type="radio" name="show_nb_hits" value="leave" checked="checked" /> {'leave'|@translate}</label>
      / {'set to'|@translate}
      <label><input type="radio" name="show_nb_hits" value="true" {$SHOW_NB_HITS_YES} />{'Yes'|@translate}</label>
      <label><input type="radio" name="show_nb_hits" value="false" {$SHOW_NB_HITS_NO} />{'No'|@translate}</label>
    </td>
  </tr>

  <tr>
    <td>{'maxwidth'|@translate}</td>
    <td>
      <label><input type="radio" name="maxwidth_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="maxwidth_action" value="unset" /> {'unset'|@translate}</label>
      <label><input type="radio" name="maxwidth_action" value="set" id="maxwidth_action_set" {$MAXWIDTH_ACTION_SET} /> {'set to'|@translate}</label>
      <input onmousedown="document.getElementById('maxwidth_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxwidth" value="{$MAXWIDTH}" />
    </td>
  </tr>


  <tr>
    <td>{'maxheight'|@translate}</td>
    <td>
      <label><input type="radio" name="maxheight_action" value="leave" checked="checked" /> {'leave'|@translate}</label>
      <label><input type="radio" name="maxheight_action" value="unset" /> {'unset'|@translate}</label>
      <label><input type="radio" name="maxheight_action" value="set" id="maxheight_action_set" {$MAXHEIGHT_ACTION_SET} /> {'set to'|@translate}</label>
      <input onmousedown="document.getElementById('maxheight_action_set').checked = true;"
             type="text" size="4" maxlength="4" name="maxheight" value="{$MAXHEIGHT}" />
    </td>
  </tr>


</table>

</fieldset>

<p>
  {'target'|@translate}
  <label><input type="radio" name="target" value="all" /> {'all'|@translate}</label>
  <label><input type="radio" name="target" value="selection" checked="checked" /> {'selection'|@translate}</label>
</p>

<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="pref_submit" {$TAG_INPUT_ENABLED} />
  <input class="submit" type="reset" value="{'Reset'|@translate}" name="pref_reset" />
</p>

</form>
