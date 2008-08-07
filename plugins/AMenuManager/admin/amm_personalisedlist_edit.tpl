{literal}
<script type="text/javascript">

  //global var ; need to not have to initialize them every time a value is changed
  var objlang;
  var objnames = new Array('iamm_personalised_title', 'iamm_personalised_content');
  var objinput = new Array();   //input text from form => objinput[name]
  var objhidden = new Array();  //input hidden from form => objhidden[name][lang]

  function init()
  {
    objlang = document.getElementById('islang');
    for(i=0;i<objnames.length;i++)
    {
      objinput[i] = document.getElementById(objnames[i]);
      objhidden[i] = new Array();
      for(j=0;j<objlang.options.length;j++)
      {
        objhidden[i][j] = document.getElementById(objnames[i]+'_'+objlang.options[j].value);
      }
    }
  }

  function change_lang()
  {
    for(i=0;i<objnames.length;i++)
    {
      objinput[i].value = objhidden[i][objlang.options.selectedIndex].value;
    }
  }

  function apply_changes(input_id)
  {
    var obj=document.getElementById(input_id);
    objhidden[objnames.indexOf(input_id)][objlang.options.selectedIndex].value = obj.value;
  }

  function do_translation()
  {
    var inputid = document.getElementById('iamm_personalised_title');
    var tolang = objlang.options[objlang.options.selectedIndex].value.substr(0,2);

    google_translate(inputid.value, '{/literal}{$datas.fromlang}{literal}', tolang, inputid, 'value', apply_changes, inputid.id);
  }

</script>
{/literal}


{if isset($datas.modeedit)}

  <h3>
  {if $datas.modeedit=='create'}
    {'g002_createofpersonalised'|@translate}
  {else}
    {'g002_editofpersonalised'|@translate}
  {/if}
  / <span style="font-weight:normal"><a href="{$datas.personalised_list}">{'g002_personalisedlist'|@translate}</a></span></h3>
  </h3>


<form method="post" action="" class="general">
  <fieldset>
    <legend>{'g002_setting_personalised_properties'|@translate}</legend>

    <table class="formtable">
      <tr>
        <td>{'g002_setting_block_active'|@translate}</td>
        <td>
          <select name="famm_personalised_visible" id="iamm_personalised_visible">
            {html_options values=$datas.yesno_values output=$datas.yesno_labels selected=$datas.visible_selected}
          </select>
        </td>
      </tr>

      <tr>
        <td>{'g002_setting_personalised_nfo'|@translate}</td>
        <td>
          <input type="text" name="famm_personalised_nfo" id="iamm_personalised_nfo" value="{$datas.nfo}" maxlength="25"/>
        </td>
      </tr>

    </table>

  </fieldset>

  <fieldset>
    <legend>{'g002_setting_block_menu'|@translate}</legend>

    {if isset($datas.language_list) and count($datas.language_list)}
      {foreach from=$datas.language_list key=name item=language_row}
        <input type="hidden" name="famm_personalised_title_{$language_row.LANG}"
                id="iamm_personalised_title_{$language_row.LANG}" value="{$language_row.MENUBARTIT}">
        <input type="hidden" name="famm_personalised_content_{$language_row.LANG}"
                id="iamm_personalised_content_{$language_row.LANG}" value="{$language_row.MENUBARCONTENT}">
      {/foreach}
    {/if}

    <table class="formtable">

      <tr>
        <td>{'g002_setting_block_langchoice'|@translate}</td>
        <td>
          <select onchange="change_lang();" id="islang">
            {html_options values=$datas.language_list_values output=$datas.language_list_labels selected=$datas.lang_selected}
          </select><br>
        </td>
      </tr>

      <tr>
        <td colspan=2><hr/></td>
      </tr>

      <tr>
        <td>{'g002_setting_block_title'|@translate}</td>
        <td>
          <input type="text" id="iamm_personalised_title" value="" maxlength="50" onkeyup="apply_changes('iamm_personalised_title');" onblur="apply_changes('iamm_personalised_title');"/>
        </td>
      </tr>
      <tr>
        <td></td>
        <td style="font-size:80%;">
          <a style="cursor:pointer;" onclick="do_translation()">{'g002_translate'|@translate}</a>
        </td>
      </tr>

      <tr>
        <td>{'g002_setting_personalised_content'|@translate}</td>
        <td>
          <textarea id="iamm_personalised_content" cols=80 rows=25 onkeyup="apply_changes('iamm_personalised_content');" /></textarea>
        </td>
      </tr>


    </table>

  </fieldset>


  <p>
    <input type="submit" name="famm_submit_apply" id="iamm_submit_apply" value="{'g002_apply'|@translate}" >
  </p>

  <input type="hidden" name="famm_modeedit" value="{$datas.modeedit}">
  <input type="hidden" name="famm_id" value="{$datas.id}">

</form>


<script type="text/javascript">
  init();
  change_lang();
</script>

{/if}