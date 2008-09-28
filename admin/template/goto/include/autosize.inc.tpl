{* $Id$ *}
{known_script id="jquery.growfield" src=$ROOT_URL|@cat:"template-common/lib/plugins/jquery.growfield.packed.js"}

{* Auto size and auto grow textarea *}
{literal}
<script type="text/javascript">
  jQuery().ready(function(){
    // Auto size and auto grow for all text area
    jQuery("TEXTAREA").growfield({
      animate: false
    });
  });
</script>
{/literal}
