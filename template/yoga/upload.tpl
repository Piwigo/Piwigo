{* $Id$ *}

<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'return to homepage'|@translate}"><img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"/></a></li>
    </ul>
    <h2>{'upload_title'|@translate}</h2>
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
      <td colspan="2" align="center" style="padding:10px;">
      <input name="picture" type="file" value="" />
      </td>
    </tr>
    {if isset($SHOW_FORM_FIELDS) and $SHOW_FORM_FIELDS}
    <!-- username  -->
    <tr>
      <td class="menu">{'Username'|@translate} <span style="color:red;">*</span></td>
      <td align="left" style="padding:10px;">
      <input name="username" type="text" value="{$NAME}" />
      </td>
    </tr>
    <!-- mail address  -->
    <tr>
      <td class="menu">{'mail_address'|@translate} <span style="color:red;">*</span></td>
      <td align="left" style="padding:10px;">
      <input name="mail_address" type="text" value="{$EMAIL}" />
      </td>
    </tr>
    <!-- name of the picture  -->
    <tr>
      <td class="menu">{'upload_name'|@translate}</td>
      <td align="left" style="padding:10px;">
      <input name="name" type="text" value="{$NAME_IMG}" />
      </td>
    </tr>
    <!-- author  -->
    <tr>
      <td class="menu">{'upload_author'|@translate}</td>
      <td align="left" style="padding:10px;">
      <input name="author" type="text" value="{$AUTHOR_IMG}" />
      </td>
    </tr>
    <!-- date of creation  -->
    <tr>
      <td class="menu">{'Creation date'|@translate} (DD/MM/YYYY)</td>
      <td align="left" style="padding:10px;">
      <input name="date_creation" type="text" value="{$DATE_IMG}" />
      </td>
    </tr>
    <!-- comment  -->
    <tr>
      <td class="menu">{'comment'|@translate}</td>
      <td align="left" style="padding:10px;">
       <textarea name="comment" rows="3" cols="40" style="overflow:auto">{$COMMENT_IMG}</textarea>
      </td>
    </tr>
    {/if}
    <tr>
      <td colspan="2" align="center">
      <input class="submit" name="submit" type="submit" value="{'Submit'|@translate}" />
      </td>
    </tr>
    </table>
  </form>
  {else}
  {'upload_successful'|@translate}<br />
  <div style="text-align:center;">
    <a href="{$U_RETURN}">[ {'home'|@translate} ]</a>
  </div>
  {/if}
  
  {if isset($SHOW_FORM_FIELDS) and $SHOW_FORM_FIELDS}
  <div style="text-align:left;"><span style="color:red;">*</span> : {'mandatory'|@translate}</div>
  {/if}
</div> <!-- content -->
