{* $Id: /piwigo/trunk/admin/template/goto/include/autosize.inc.tpl 6493 2008-10-04T13:54:54.894514Z patdenice  $ *}
{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="jquery.autogrow" src=$ROOT_URL|@cat:"template-common/lib/plugins/jquery.autogrow-textarea.js"}

{* Auto size and auto grow textarea *}
{literal}
<script type="text/javascript">
  jQuery().ready(function(){
    jQuery('textarea').css('overflow-y', 'hidden');
    // Auto size and auto grow for all text area
    jQuery('textarea').autogrow();
  });
</script>
{/literal}
