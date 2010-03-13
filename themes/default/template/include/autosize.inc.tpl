{known_script id="jquery" src=$ROOT_URL|@cat:"themes/default/js/jquery.packed.js"}
{known_script id="jquery.autogrow" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.autogrow-textarea.js"}

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
