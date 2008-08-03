
{if isset($datas.urls) and count($datas.urls)}
  <table class="table2 littlefont">
    <tr class="throw">
      <th width='15'>&nbsp;</th>
      <th>{'g002_label'|@translate}</th>
      <th>{'g002_url'|@translate}</th>
      <th>{'g002_mode'|@translate}</th>
      <th>{'g002_visible'|@translate}</th>
      <th colspan=4>&nbsp;</th>
    </tr>

    {foreach from=$datas.urls key=name item=url}
      <tr>
        <td>{if $url.img!=""}<img src='{$url.img}'/>{else}&nbsp;{/if}</td>
        <td>{$url.label}</td>
        <td>{$url.url}</td>
        <td>{$url.mode}</td>
        <td style="text-align:center;">{$url.visible}</td>
        <td width="15px">{if $url.up}<a style="cursor:pointer;" onclick="load_list('permut', {$url.ID}, {$url.IDPREV})"><img src='{$plugin.PATH}/admin/go-up.png'/></a>{else}&nbsp;{/if}</td>
        <td width="15px">{if $url.down}<a style="cursor:pointer;" onclick="load_list('permut', {$url.ID}, {$url.IDNEXT})"><img src='{$plugin.PATH}/admin/go-down.png'/></a>{else}&nbsp;{/if}</td>
        <td width="15px"><a href="{$url.edit}"><img src='{$themeconf.icon_dir}/category_edit.png'/></a></td>
        <td width="15px"><a style="cursor:pointer;" onclick="load_list('delete', {$url.ID}, 0)"><img src='{$themeconf.icon_dir}/delete.png'/></a></td>
      </tr>
    {/foreach}

  </table>
{/if}
