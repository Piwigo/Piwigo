<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<!-- BEGIN confirmation -->
<div class="info">{L_CONFIRM}</div>
<!-- END confirmation -->

<form method="post" action="{F_ACTION}">

<p class="confMenu">
  <!-- BEGIN confmenu_item -->
  <a class="{confmenu_item.CLASS}" href="{confmenu_item.URL}">{confmenu_item.NAME}</a>
  <!-- END confmenu_item -->
</p>

<table width="100%" align="center">
  <!-- BEGIN line -->
  <tr>
    <td width="50%">
      <span class="confLineName">{line.NAME} :</span>
      <br />
      <span class="confLineInfo">{line.INFO}</span>
    </td>
    <td class="confLineField">

      <!-- BEGIN textfield -->
      <input type="text" size="{line.textfield.SIZE}" maxlength="{line.textfield.SIZE}" name="{line.textfield.NAME}" value="{line.textfield.VALUE}" />
      <!-- END textfield -->

      <!-- BEGIN radio -->
      <input type="radio" class="radio" name="{line.radio.NAME}" value="{line.radio.VALUE}" {line.radio.CHECKED} />{line.radio.OPTION}
      <!-- END radio -->

      <!-- BEGIN select -->
      <select name="{line.select.NAME}">
        <!-- BEGIN select_option -->
        <option value="{line.select.select_option.VALUE}" {line.select.select_option.SELECTED}>{line.select.select_option.OPTION}</option>
        <!-- END select_option -->
      </select>
      <!-- END select -->

    </td>
  </tr>
  <!-- END line -->
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" class="bouton" value="{L_SUBMIT}" />
    </td>
  </tr>
</table>

</form>
