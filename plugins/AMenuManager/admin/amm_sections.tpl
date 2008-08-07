{literal}
<script type="text/javascript">

  function load_list(do_action, item, position)
  {
    /*
      do_action
        'list' : just load list
        'permut' : permut items in list
        'delete' : delete the item in list
    */
    var doc = document.getElementById("isections");

    action_todo='';
    if(do_action=='position')
    {
      action_todo='sections_position&fItem='+item+'&fPosition='+position;
    }
    else if(do_action=='showhide')
    {
      action_todo='sections_showhide&fItem='+item;
    }
    else
    {
      action_todo='sections_list';
    }

    if(action_todo!='')
    {
      http_request=create_httpobject('get', '', '{/literal}{$datas.AMM_AJAX_URL_LIST}{literal}'+action_todo, false);
      http_request.send(null);
      doc.innerHTML=http_request.responseText;
    }
  }

</script>
{/literal}


<h3>
{foreach from=$datas.LIST item=data}
  {$data.separator}
  {if $data.link!=''}
    <span style="font-weight:normal"><a href="{$data.link}" title="{$data.label|@translate}">
  {/if}
  {$data.label|@translate}
  {if $data.link!=''}
    </a></span>
  {/if}
{/foreach}
</h3>

<div id="isections"></div>


<script type="text/javascript">
  load_list('list', 0, 0);
</script>