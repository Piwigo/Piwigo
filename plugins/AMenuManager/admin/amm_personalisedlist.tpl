{literal}
<script type="text/javascript">

  function load_list(do_action, item)
  {
    /*
      do_action
        'list' : just load list
        'delete' : delete the item in list
    */
    var doc = document.getElementById("isections");

    action_todo='';
    if(do_action=='delete')
    {
      if(confirm('{/literal}{'g002_confirm_delete_link'|@translate}{literal}'))
      {
        action_todo='personalised_delete&fItem='+item;
      }
    }
    else
    {
      action_todo='personalised_list';
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


<h3>{'g002_personalisedlist'|@translate}</h3>

[{$datas.nbsections}]<br/>
<a href="{$datas.lnk_create}" title="{'g002_addsection'|@translate}">{'g002_addsection'|@translate}</a>

<br/>

<div id="isections"></div>


<script type="text/javascript">
  load_list('list', 0);
</script>