
{* Example of resizeable *}
{*
{include file='include/autosize.inc.tpl'}
*}

<div id="content" class="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'Home'|@translate}"><img src="{$themeconf.icon_dir}/Home.png" class="button" alt="{'Home'|@translate}"></a></li>
    </ul>
    <h2>{'Upload a picture'|@translate}</h2>
  </div>

  {if !empty($errors)}
  <div class="errors">
    <ul>
      {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if not $UPLOAD_SUCCESSFUL }
  <form enctype="multipart/form-data" method="post" action="{$F_ACTION}">
    <table style="width:80%;margin-left:auto;margin-right:auto;">
    <tr>
      <td colspan="2" class="menu">
      <div style="text-align:center;">{$ADVISE_TITLE}</div>
      {if not empty($advises)}
      <ul>
        {foreach from=$advises item=advise}
        <li>{$advise}</li>
        {/foreach}
      </ul>
      {/if}
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
      <input class="file" name="picture" type="file" value="">
      </td>
    </tr>
    {if isset($SHOW_FORM_FIELDS) and $SHOW_FORM_FIELDS}
    <!-- category -->
    <tr>
      <td>{'Category'|@translate}</td>
      <td>
        {html_options name="category" options=$categories selected=$categories_selected}
      </td>
    </tr>
    <!-- username -->
    <tr>
      <td>{'Username'|@translate} <span style="color:red;">*</span></td>
      <td>
      <input name="username" type="text" value="{$NAME}">
      </td>
    </tr>
    <!-- mail address -->
    <tr>
      <td>{'E-mail address'|@translate} <span style="color:red;">*</span></td>
      <td>
      <input name="mail_address" type="text" value="{$EMAIL}">
      </td>
    </tr>
    <!-- name of the picture -->
    <tr>
      <td>{'Name of the picture'|@translate}</td>
      <td>
      <input name="name" type="text" value="{$NAME_IMG}">
      </td>
    </tr>
    <!-- author -->
    <tr>
      <td>{'Author'|@translate}</td>
      <td>
      <input name="author" type="text" value="{$AUTHOR_IMG}">
      </td>
    </tr>
    <!-- date of creation -->
    <tr>
      <td>{'Creation date'|@translate} (DD/MM/YYYY)</td>
      <td>
      <input name="date_creation" type="text" value="{$DATE_IMG}">
      </td>
    </tr>
    <!-- comment -->
    <tr>
      <td>{'Comment'|@translate}</td>
      <td>
       <textarea name="comment" id="comment" rows="3" cols="40" style="overflow:auto">{$COMMENT_IMG}</textarea>
      </td>
    </tr>
    {/if}
    <tr>
      <td colspan="2" align="center">
      <input class="submit" name="submit" type="submit" value="{'Submit'|@translate}">
      </td>
    </tr>
    </table>
  </form>
  {else}
  {'Picture uploaded with success, an administrator will validate it as soon as possible'|@translate}<br>
  <div style="text-align:center;">
    <a href="{$U_RETURN}">[ {'Home'|@translate} ]</a>
  </div>
  {/if}
  
  {if isset($SHOW_FORM_FIELDS) and $SHOW_FORM_FIELDS}
  <div style="text-align:left; margin-left:20px;"><span style="color:red;">*</span> : {'obligatory'|@translate}</div>
  {/if}
</div> <!-- content -->
