{literal}
<script type="text/javascript">

  function load_list(do_action, item, permut)
  {
    /*
      do_action
        'list' : just load list
        'permut' : permut items in list
        'delete' : delete the item in list
    */
    var doc = document.getElementById("iurls");

    action_todo='';
    if(do_action=='permut')
    {
      action_todo='links_permut&fItem='+item+'&fPermut='+permut;
    }
    else if(do_action=='delete')
    {
      if(confirm('{/literal}{'g002_confirm_delete_link'|@translate}{literal}'))
      {
        action_todo='links_delete&fItem='+item;
      }
    }
    else
    {
      action_todo='links_list';
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


<h3>{'g002_linkslist'|@translate} / <span style="font-weight:normal"><a href="{$datas.lnk_config}" title="{'g002_configlinks'|@translate}">{'g002_configlinks'|@translate}</a></span>
</h3>

[{$datas.nburl}]<br/>
<a href="{$datas.lnk_create}" title="{'g002_addlink'|@translate}">{'g002_addlink'|@translate}</a>

<br/>

<div id="iurls"></div>


<script type="text/javascript">
  load_list('list', 0, 0);
</script>