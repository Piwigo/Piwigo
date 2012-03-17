<div class="titrePage">
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form action="{$F_ACTION}" method="POST" id="catModify">

<fieldset>
  <legend>{'Informations'|@translate}</legend>

  <table style="width:100%">
    <tr>
      <td id="albumThumbnail">
{if isset($representant) }
  {if isset($representant.picture) }
        <a href="{$representant.picture.URL}"><img src="{$representant.picture.SRC}" alt=""></a>
  {else}
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_representant_random.png" alt="{'Random photo'|@translate}">
  {/if}

  {if $representant.ALLOW_SET_RANDOM }
        <p style="text-align:center;"><input class="submit" type="submit" name="set_random_representant" value="Refresh" title="{'Find a new representant by random'|@translate}"></p>
  {/if}

  {if isset($representant.ALLOW_DELETE) }
        <p><input class="submit" type="submit" name="delete_representant" value="{'Delete Representant'|@translate}"></p>
  {/if}
{/if}
      </td>

      <td id="albumLinks">
<p>{$INTRO}</p>
<ul style="padding-left:15px;">
{if cat_admin_access($CAT_ID)}
  <li><a href="{$U_JUMPTO}">{'jump to album'|@translate} →</a></li>
{/if}

{if isset($U_MANAGE_ELEMENTS) }
  <li><a href="{$U_MANAGE_ELEMENTS}">{'manage album photos'|@translate}</a></li>
{/if}

  <li><a href="{$U_CHILDREN}">{'manage sub-albums'|@translate}</a></li>

{if isset($U_SYNC) }
  <li><a href="{$U_SYNC}">{'Synchronize'|@translate}</a> ({'Directory'|@translate} = {$CAT_FULL_DIR})</li>
{/if}

{if isset($U_DELETE) }
  <li><a href="{$U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete album'|@translate}</a></li>
{/if}

</ul>
      </td>
    </tr>
  </table>

</fieldset>

<fieldset>
  <legend>{'Properties'|@translate}</legend>
  <p>
    <strong>{'Name'|@translate}</strong>
    <br>
    <input type="text" class="large" name="name" value="{$CAT_NAME}" maxlength="60">
  </p>

  <p>
    <strong>{'Description'|@translate}</strong>
    <br>
    <textarea cols="50" rows="5" name="comment" id="comment" class="description">{$CAT_COMMENT}</textarea>
  </p>

{if isset($move_cat_options) }
  <p>
    <strong>{'Parent album'|@translate}</strong>
    <br>
    <select class="categoryDropDown" name="parent">
      <option value="0">------------</option>
      {html_options options=$move_cat_options selected=$move_cat_options_selected }
    </select>
  </p>
{/if}

  <p>
    <strong>{'Lock'|@translate}</strong>
    <br>
    {html_radios name='visible' values='true,false'|@explode output='No,Yes'|@explode|translate selected=$CAT_VISIBLE}
  </p>

  {if isset($CAT_COMMENTABLE)}
  <p>
    <strong>{'Comments'|@translate}</strong>
    <br>
    {html_radios name='commentable' values='false,true'|@explode output='No,Yes'|@explode|translate selected=$CAT_COMMENTABLE}
  </p>
  {/if}

  <p style="margin:0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
