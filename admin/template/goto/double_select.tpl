{* $Id$ *}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.resizable" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.resizable.packed.js"}

<script type="text/javascript">
  jQuery().ready(function(){ldelim}
    jQuery(".doubleSelect select.categoryList").resizable({ldelim}
    handles: "w,e",
    animate: true,
    animateDuration: "slow",
    animateEasing: "swing",
    preventDefault: true,
    preserveCursor: true,
    autoHide: true,
    ghost: true
    });
  });
</script>

<table class="doubleSelect">
  <tr>
    <td>
      <h3>{$L_CAT_OPTIONS_TRUE}</h3>
      <select class="categoryList" name="cat_true[]" multiple="multiple" size="30">
        {html_options options=$category_option_true selected=$category_option_true_selected}
      </select>
      <p><input class="submit" type="submit" value="&raquo;" name="falsify" style="font-size:15px;" {$TAG_INPUT_ENABLED}/></p>
    </td>

    <td>
      <h3>{$L_CAT_OPTIONS_FALSE}</h3>
      <select class="categoryList" name="cat_false[]" multiple="multiple" size="30">
        {html_options options=$category_option_false selected=$category_option_false_selected}
      </select>
      <p><input class="submit" type="submit" value="&laquo;" name="trueify" style="font-size:15px;" {$TAG_INPUT_ENABLED}/></p>
    </td>
  </tr>
</table>
