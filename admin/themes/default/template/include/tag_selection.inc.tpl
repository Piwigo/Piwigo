{literal}
<script type="text/javascript">
$(document).ready(function(){
  $(".tagSelection label").click(function () {
    var parent = $(this).parent('li');
    var checkbox = $(this).children("input[type=checkbox]");

    if ($(checkbox).is(':checked')) {
       $(parent).addClass("tagSelected"); 
    }
    else {
       $(parent).removeClass('tagSelected'); 
    }
  });
});
</script>
{/literal}
