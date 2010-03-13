<script type="text/javascript" src="template-common/lib/jquery.packed.js"></script>
{literal}
<script type="text/javascript">
$(function() {
    $option_selected = $('#dblayer option:selected').attr('value');
    if ($option_selected=='sqlite' || $option_selected=='pdo-sqlite') {
       $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().hide();
    }
    $('#dblayer').change(function() {
        $db = this;
        if ($db.value=='sqlite' || $db.value=='pdo-sqlite') {
           $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().hide();
        } else {
           $('input[name=dbhost],input[name=dbuser],input[name=dbpasswd]').parent().parent().show();
        }
      });
  });
</script>
{/literal}
