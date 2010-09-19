{include file='include/autosize.inc.tpl'}
{include file='include/dbselect.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{known_script id="jquery.fcbkcomplete" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.fcbkcomplete.js"}
{literal}
<script type="text/javascript">
  $(document).ready(function() {
    $("#tags").fcbkcomplete({
      json_url: "admin.php?fckb_tags=1",
      cache: false,
      filter_case: false,
      filter_hide: true,
      firstselected: true,
      filter_selected: true,
      maxitems: 100,
      newel: true
    });
  });
</script>
{/literal}

{literal}
<script type="text/javascript">
  pwg_initialization_datepicker("#date_creation_day", "#date_creation_month", "#date_creation_year", "#date_creation_linked_date", "#date_creation_action_set");
</script>
{/literal}

<h2>{'Modify informations about a picture'|@translate}</h2>

<img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">

<ul class="categoryActions">
  {if isset($U_JUMPTO) }
  <li><a href="{$U_JUMPTO}" title="{'jump to image'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_jump-to.png" class="button" alt="{'jump to image'|@translate}"></a></li>
  {/if}
  {if !url_is_remote($path)}
  <li><a href="{$U_SYNC}" title="{'synchronize'|@translate}" {$TAG_INPUT_ENABLED}><img src="{$themeconf.admin_icon_dir}/sync_metadata.png" class="button" alt="{'synchronize'|@translate}"></a></li>
  {/if}
</ul>

<form action="{$F_ACTION}" method="post" id="properties">

  <fieldset>
    <legend>{'Informations'|@translate}</legend>

    <table>

      <tr>
        <td><strong>{'Path'|@translate}</strong></td>
        <td>{$PATH}</td>
      </tr>

      <tr>
        <td><strong>{'Post date'|@translate}</strong></td>
        <td>{$REGISTRATION_DATE}</td>
      </tr>

      <tr>
        <td><strong>{'Dimensions'|@translate}</strong></td>
        <td>{$DIMENSIONS}</td>
      </tr>

      <tr>
        <td><strong>{'Filesize'|@translate}</strong></td>
        <td>{$FILESIZE}</td>
      </tr>

{if isset($HIGH_FILESIZE) }
      <tr>
        <td><strong>{'High filesize'|@translate}</strong></td>
        <td>{$HIGH_FILESIZE}</td>
      </tr>
{/if}

      <tr>
        <td><strong>{'Storage album'|@translate}</strong></td>
        <td>{$STORAGE_CATEGORY}</td>
      </tr>

      {if isset($related_categories) }
      <tr>
        <td><strong>{'Linked albums'|@translate}</strong></td>
        <td>
          <ul>
            {foreach from=$related_categories item=name}
            <li>{$name}</li>
            {/foreach}
          </ul>
        </td>
      </tr>
      {/if}

    </table>

  </fieldset>

  <fieldset>
    <legend>{'Properties'|@translate}</legend>

    <table>

      <tr>
        <td><strong>{'Name'|@translate}</strong></td>
        <td><input type="text" class="large" name="name" value="{$NAME}"></td>
      </tr>

      <tr>
        <td><strong>{'Author'|@translate}</strong></td>
        <td><input type="text" class="large" name="author" value="{$AUTHOR}"></td>
      </tr>

      <tr>
        <td><strong>{'Creation date'|@translate}</strong></td>
        <td>
          <label><input type="radio" name="date_creation_action" value="unset"> {'unset'|@translate}</label>
          <input type="radio" name="date_creation_action" value="set" id="date_creation_action_set"> {'set to'|@translate}
          <select id="date_creation_day" name="date_creation_day">
            <option value="0">--</option>
            {section name=day start=1 loop=32}
              <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$DATE_CREATION_DAY_VALUE}selected="selected"{/if}>{$smarty.section.day.index}</option>
            {/section}
          </select>
          <select id="date_creation_month" name="date_creation_month">
            {html_options options=$month_list selected=$DATE_CREATION_MONTH_VALUE}
          </select>
          <input id="date_creation_year"
                 name="date_creation_year"
                 type="text"
                 size="4"
                 maxlength="4"
                 value="{$DATE_CREATION_YEAR_VALUE}">
          <input id="date_creation_linked_date" name="date_creation_linked_date" type="hidden" size="10" disabled="disabled">
        </td>
      </tr>

      <tr>
        <td><strong>{'Tags'|@translate}</strong></td>
        <td>
<select id="tags" name="tags">
{foreach from=$tags item=tag}
  <option value="{$tag.value}" class="selected">{$tag.caption}</option>
{/foreach}
</select>
        </td>
      </tr>


      <tr>
        <td><strong>{'Description'|@translate}</strong></td>
        <td><textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea></td>
      </tr>

  <tr>
    <td><strong>{'Who can see this photo?'|@translate}</strong></td>
    <td>
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </td>
  </tr>

    </table>

    <p style="text-align:center;">
      <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}>
      <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
    </p>

  </fieldset>

</form>

<form id="associations" method="post" action="{$F_ACTION}#associations">
  <fieldset>
    <legend>{'Association to categories'|@translate}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{'Associated'|@translate}</h3>
          <select class="categoryList" name="cat_associated[]" multiple="multiple" size="30">
            {html_options options=$associated_options}
          </select>
          <p><input class="submit" type="submit" value="&raquo;" name="dissociate" style="font-size:15px;" {$TAG_INPUT_ENABLED}></p>
        </td>

        <td>
          <h3>{'Dissociated'|@translate}</h3>
          <select class="categoryList" name="cat_dissociated[]" multiple="multiple" size="30">
            {html_options options=$dissociated_options}
          </select>
          <p><input class="submit" type="submit" value="&laquo;" name="associate" style="font-size:15px;" {$TAG_INPUT_ENABLED}></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>

<form id="representation" method="post" action="{$F_ACTION}#representation">
  <fieldset>
    <legend>{'Representation of categories'|@translate}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{'Represents'|@translate}</h3>
          <select class="categoryList" name="cat_elected[]" multiple="multiple" size="30">
            {html_options options=$elected_options}
          </select>
          <p><input class="submit" type="submit" value="&raquo;" name="dismiss" style="font-size:15px;" {$TAG_INPUT_ENABLED}></p>
        </td>

        <td>
          <h3>{'Does not represent'|@translate}</h3>
          <select class="categoryList" name="cat_dismissed[]" multiple="multiple" size="30">
            {html_options options=$dismissed_options}
          </select>
          <p><input class="submit" type="submit" value="&laquo;" name="elect" style="font-size:15px;" {$TAG_INPUT_ENABLED}></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>
