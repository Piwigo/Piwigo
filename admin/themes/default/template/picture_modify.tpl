{include file='include/autosize.inc.tpl'}
{include file='include/dbselect.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}

{footer_script}{literal}
jQuery(document).ready(function() {
  jQuery(".chzn-select").chosen();
});
{/literal}{/footer_script}

{combine_script id='jquery.tokeninput' load='async' require='jquery' path='themes/default/js/plugins/jquery.tokeninput.js'}
{footer_script require='jquery.tokeninput'}
jQuery(document).ready(function() {ldelim}
  jQuery("#tags").tokenInput(
    [{foreach from=$tags item=tag name=tags}{ldelim}"name":"{$tag.name|@escape:'javascript'}","id":"{$tag.id}"{rdelim}{if !$smarty.foreach.tags.last},{/if}{/foreach}],
    {ldelim}
      hintText: '{'Type in a search term'|@translate}',
      noResultsText: '{'No results'|@translate}',
      searchingText: '{'Searching...'|@translate}',
      newText: ' ({'new'|@translate})',
      animateDropdown: false,
      preventDuplicates: true,
      allowCreation: true
    }
  );
});
{/footer_script}

{footer_script}
pwg_initialization_datepicker("#date_creation_day", "#date_creation_month", "#date_creation_year", "#date_creation_linked_date", "#date_creation_action_set");
{/footer_script}

<h2>{$TITLE} &#8250; {'Edit photo'|@translate} {$TABSHEET_TITLE}</h2>

<form action="{$F_ACTION}" method="post" id="catModify">

  <fieldset>
    <legend>{'Informations'|@translate}</legend>

    <table>

      <tr>
        <td id="albumThumbnail">
          <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
        </td>
        <td id="albumLinks" style="width:400px;vertical-align:top;">
          <ul style="padding-left:15px;margin:0;">
            <li>{$INTRO.file}</li>
            <li>{$INTRO.add_date}</li>
            <li>{$INTRO.added_by}</li>
            <li>{$INTRO.size}</li>
            <li>{$INTRO.stats}</li>
            <li>{$INTRO.id}</li>
          </ul>
        </td>
        <td style="vertical-align:top;">
          <ul style="padding-left:15px;margin:0;">
          {if isset($U_JUMPTO) }
            <li><a href="{$U_JUMPTO}">{'jump to photo'|@translate} →</a></li>
          {/if}
          {if !url_is_remote($PATH)}
            <li><a href="{$U_SYNC}">{'Synchronize metadata'|@translate}</a></li>

            <li><a href="{$U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete photo'|@translate}</a></li>
          {/if}
          </ul>
        </td>
      </tr>
    </table>

  </fieldset>

  <fieldset>
    <legend>{'Properties'|@translate}</legend>

    <p>
      <strong>{'Title'|@translate}</strong>
      <br>
      <input type="text" class="large" name="name" value="{$NAME|@escape}">
    </p>

    <p>
      <strong>{'Author'|@translate}</strong>
      <br>
      <input type="text" class="large" name="author" value="{$AUTHOR}">
    </p>

    <p>
      <strong>{'Creation date'|@translate}</strong>
      <br>
      <select id="date_creation_day" name="date_creation_day">
        <option value="0">--</option>
{section name=day start=1 loop=32}
        <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$DATE_CREATION_DAY_VALUE}selected="selected"{/if}>{$smarty.section.day.index}</option>
{/section}
      </select>

      <select id="date_creation_month" name="date_creation_month">
        {html_options options=$month_list selected=$DATE_CREATION_MONTH_VALUE}
      </select>

      <input id="date_creation_year" name="date_creation_year" type="text" size="4" maxlength="4" value="{$DATE_CREATION_YEAR_VALUE}">
      <input id="date_creation_linked_date" name="date_creation_linked_date" type="hidden" size="10" disabled="disabled">
    <a href="#" id="unset_date_creation" style="display:none">unset</a>
    </p>

    <p>
      <strong>{'Linked albums'|@translate}</strong>
      <br>
      <select data-placeholder="Select albums..." class="chzn-select" multiple style="width:700px;" name="associate[]">
        {html_options options=$associate_options selected=$associate_options_selected}
      </select>
    </p>

    <p>
      <strong>{'Representation of albums'|@translate}</strong>
      <br>
      <select data-placeholder="Select albums..." class="chzn-select" multiple style="width:700px;" name="represent[]">
        {html_options options=$represent_options selected=$represent_options_selected}
      </select>
    </p>

    <p>
      <strong>{'Tags'|@translate}</strong>
      <br>
<select id="tags" name="tags">
{foreach from=$tag_selection item=tag}
  <option value="{$tag.id}" class="selected">{$tag.name}</option>
{/foreach}
</select>
    </p>

    <p>
      <strong>{'Description'|@translate}</strong>
      <br>
      <textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea>
    </p>

    <p>
      <strong>{'Who can see this photo?'|@translate}</strong>
      <br>
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
   </p>

  <p style="margin:40px 0 0 0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
