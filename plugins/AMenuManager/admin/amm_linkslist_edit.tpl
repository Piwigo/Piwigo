{literal}
<script type="text/javascript">

  function change_selected_icon()
  {
    var doc = document.getElementById("iamm_icon");

    var icon_list = Array();

    {/literal}
    {foreach from=$datas.icons_values key=name item=icon}
    icon_list.push('{$icon.img}');
    {/foreach}
    {literal}

    doc.style.backgroundImage='url('+icon_list[doc.options.selectedIndex]+')';
  }

</script>
{/literal}


{if isset($datas.modeedit)}

  <h3>
  {if $datas.modeedit=='create'}
    {'g002_createoflink'|@translate}
  {else}
    {'g002_editoflink'|@translate}
  {/if}
  / <span style="font-weight:normal"><a href="{$datas.lnk_list}">{'g002_linkslist'|@translate}</a></span></h3>
  </h3>

  <form method="post" action="" class="general">
    <fieldset>
      <table class="formtable">
        <tr>
          <td>{'g002_label'|@translate}<td>
          <td><input type='text' name="famm_label" id='iamm_label' value='{$datas.label}' maxlength=50 size=50></td>
        </tr>

        <tr>
          <td>{'g002_url'|@translate}<td>
          <td><input type='text' name="famm_url" id='iamm_url' value='{$datas.url}' maxlength=255 size=50></td>
        </tr>

        <tr>
          <td>{'g002_icon'|@translate}<td>
          <td>
            <select name="famm_icon" id="iamm_icon" onchange="change_selected_icon();" style="background-image:url('{$datas.icons_img}');background-position:2px 1px;background-repeat:no-repeat;padding-left:18px;">
              {foreach from=$datas.icons_values key=name item=icon}
                <option value="{$icon.value}" style="background: transparent url('{$icon.img}') no-repeat scroll 0px 0px;padding-left:20px;" {if $icon.value==$datas.icons_selected}selected{/if}>{$icon.label}</option>
              {/foreach}
            </select>
          </td>
        </tr>

        <tr>
          <td>{'g002_mode'|@translate}<td>
          <td>
            <select name="famm_mode" id="iamm_mode">
              {html_options values=$datas.mode_values output=$datas.mode_labels selected=$datas.mode_selected}
            </select>
          </td>
        </tr>

        <tr>
          <td>{'g002_visible'|@translate}<td>
          <td>
            <select name="famm_visible" id="iamm_visible">
              {html_options values=$datas.visible_values output=$datas.visible_labels selected=$datas.visible_selected}
            </select>
          </td>
        </tr>

      </table>
    </fieldset>


    {if $datas.modeedit=='create'}
      <p>
        <input type="submit" name="famm_submit_create" id="iamm_submit_create" value="{'g002_createthelink'|@translate}" >
      </p>
    {/if}

   {if $datas.modeedit=='modify'}
      <p>
        <input type="submit" name="famm_submit_modify" id="iamm_submit_modify" value="{'g002_editthelink'|@translate}" >
      </p>
    {/if}

    <input type="hidden" name="famm_modeedit" value="{$datas.modeedit}">
    <input type="hidden" name="famm_id" value="{$datas.id}">

  </form>

{/if}