{* $Id$ *}

<div id="content" class="content">

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
      <td colspan="2" align="center">
      <input name="picture" type="file" value="" />
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
      <input name="username" type="text" value="{$NAME}" />
      </td>
    </tr>
    <!-- mail address -->
    <tr>
      <td>{'mail_address'|@translate} <span style="color:red;">*</span></td>
      <td>
      <input name="mail_address" type="text" value="{$EMAIL}" />
      </td>
    </tr>
    <!-- name of the picture -->
    <tr>
      <td>{'upload_name'|@translate}</td>
      <td>
      <input name="name" type="text" value="{$NAME_IMG}" />
      </td>
    </tr>
    <!-- author -->
    <tr>
      <td>{'upload_author'|@translate}</td>
      <td>
      <input name="author" type="text" value="{$AUTHOR_IMG}" />
      </td>
    </tr>
    <!-- date of creation -->
    <tr>
      <td>{'Creation date'|@translate} (DD/MM/YYYY)</td>
      <td>
      <input name="date_creation" type="text" value="{$DATE_IMG}" />
      </td>
    </tr>
    <!-- comment -->
    <tr>
      <td>{'comment'|@translate}</td>
      <td>
       <textarea name="comment" id="comment" rows="3" cols="40" style="overflow:auto">{$COMMENT_IMG}</textarea>
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
