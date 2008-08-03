{literal}
<script type="text/javascript">

  //global var ; need to not have to initialize them every time a value is changed
  var objlang;
  var objnames = new Array('iamm_links_title');
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

</script>
{/literal}



<h3><span style="font-weight:normal"><a href="{$datas.lnk_list}" title="{'g002_configlinks'|@translate}">{'g002_linkslist'|@translate} </span></a> / {'g002_configlinks'|@translate}
</h3>


<form method="post" action="" class="general">
  <fieldset>
    <legend>{'g002_setting_link_block_menu'|@translate}</legend>

    {if isset($datas.language_list) and count($datas.language_list)}
      {foreach from=$datas.language_list key=name item=language_row}
        <input type="hidden" name="famm_links_title_{$language_row.LANG}"
                id="iamm_links_title_{$language_row.LANG}" value="{$language_row.MENUBARTIT}">
      {/foreach}
    {/if}

    <table class="formtable">
      <tr>
        <td>{'g002_setting_link_block_active'|@translate}</td>
        <td>
          <select name="famm_links_active" id="iamm_links_active">
            {html_options values=$datas.yesno_values output=$datas.yesno_labels selected=$datas.active_selected}
          </select>
        </td>
      </tr>

      <tr>
        <td>{'g002_setting_link_block_title'|@translate}</td>
        <td>
          <input type="text" id="iamm_links_title" value="" maxlength="50" onkeyup="apply_changes('iamm_links_title');" />
          <select onchange="change_lang();" id="islang">
            {html_options values=$datas.language_list_values output=$datas.language_list_labels selected=$datas.lang_selected}
          </select>
        </td>
      </tr>

    </table>


  </fieldset>

  <fieldset>
    <legend>{'g002_setting_link_links'|@translate}</legend>
    <table class="formtable">
      <tr>
        <td>{'g002_setting_link_show_icon'|@translate}</td>
        <td>
          <select name="famm_links_show_icons" id="iamm_links_show_icons">
            {html_options values=$datas.yesno_values output=$datas.yesno_labels selected=$datas.show_icons_selected}
          </select>
        </td>
      </tr>
    </table>
  </fieldset>

  <p>
    <input type="submit" name="famm_submit_apply" id="iamm_submit_apply" value="{'g002_apply'|@translate}" >
  </p>

  <input type="hidden" name="famm_modeedit" value="config">

</form>

<script type="text/javascript">
  init();
  change_lang();
</script>