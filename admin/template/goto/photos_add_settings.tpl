{literal}
<script>
$(document).ready(function(){
  function toggleResizeFields() {
    var checkbox = $("#websize_resize");
    var needToggle = $("input[name^=websize_]").not(checkbox).parents('tr');

    if ($(checkbox).is(':checked')) {
      needToggle.show();
    }
    else {
      needToggle.hide();
    }
  }

  toggleResizeFields();
  $("#websize_resize").click(function () {toggleResizeFields()});
});
</script>
{/literal}

<div class="titrePage" style="height:25px">
  <h2>{'Upload Photos'|@translate}</h2>
</div>

<form id="uploadFormSettings" enctype="multipart/form-data" method="post" action="{$F_ACTION}" class="properties">

  <div class="formField">
    <div class="formFieldTitle">{'Web size photo'|@translate}</div>

    <table>
      <tr>
        <th><label for="websize_resize">{'Resize'|@translate}</label></th>
        <td><input type="checkbox" name="websize_resize" id="websize_resize" {$values.websize_resize}></td>
      </tr>
      <tr>
        <th>{'Maximum Width'|@translate}</th>
        <td><input type="text" name="websize_maxwidth" value="{$values.websize_maxwidth}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Maximum Height'|@translate}</th>
        <td><input type="text" name="websize_maxheight" value="{$values.websize_maxheight}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Image Quality'|@translate}</th>
        <td><input type="text" name="websize_quality" value="{$values.websize_quality}" size="3" maxlength="3"> %</td>
      </tr>
    </table>
  </div>

  <div class="formField">
    <div class="formFieldTitle">{'Thumbnail'|@translate}</div>

    <table>
      <tr>
        <th>{'Maximum Width'|@translate}</th>
        <td><input type="text" name="thumb_maxwidth" value="{$values.thumb_maxwidth}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Maximum Height'|@translate}</th>
        <td><input type="text" name="thumb_maxheight" value="{$values.thumb_maxheight}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Image Quality'|@translate}</th>
        <td><input type="text" name="thumb_quality" value="{$values.thumb_quality}" size="3" maxlength="3"> %</td>
      </tr>
    </table>
  </div>

  <p>
    <input class="submit" type="submit" name="submit" value="{'Save Settings'|@translate}"/>
  </p>

</form>
