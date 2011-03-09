{footer_script}{literal}
jQuery(document).ready(function(){
  function toggleResizeFields(prefix) {
    var checkbox = jQuery("#"+prefix+"_resize");
    var needToggle = jQuery("input[name^="+prefix+"_]").not(checkbox).not(jQuery("#hd_keep")).parents('tr');


    if (jQuery(checkbox).is(':checked')) {
      needToggle.show();

      if (prefix == "websize") {
        jQuery("#hd_keep").parents("fieldset").show();
      }
    }
    else {
      needToggle.hide();

      if (prefix == "websize") {
        jQuery("#hd_keep").parents("fieldset").hide();
      }
    }
  }

  toggleResizeFields("websize");
  jQuery("#websize_resize").click(function () {toggleResizeFields("websize")});

  toggleResizeFields("hd");
  jQuery("#hd_resize").click(function () {toggleResizeFields("hd")});

  function toggleHdFields() {
    var checkbox = jQuery("#hd_keep");
    var needToggle = jQuery("input[name^=hd_]").not(checkbox).parents('tr');

    if (jQuery(checkbox).is(':checked')) {
      needToggle.show();
      toggleResizeFields("hd");
    }
    else {
      needToggle.hide();
    }
  }

  toggleHdFields();
  jQuery("#hd_keep").click(function () {toggleHdFields()});
});
{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Upload Photos'|@translate}</h2>
</div>

<div id="photosAddContent">

<form id="uploadFormSettings" enctype="multipart/form-data" method="post" action="{$F_ACTION}" class="properties">

  <fieldset>
    <legend>{'Web size photo'|@translate}</legend>

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
  </fieldset>

  <fieldset>
    <legend>{'Thumbnail'|@translate}</legend>

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
  </fieldset>

{if $MANAGE_HD}
  <fieldset>
    <legend>{'High definition'|@translate}</legend>

    <table>
      <tr>
        <th><label for="hd_keep">{'Keep high definition'|@translate}</label></th>
        <td><input type="checkbox" name="hd_keep" id="hd_keep" {$values.hd_keep}></td>
      </tr>
      <tr>
        <th><label for="hd_resize">{'Resize'|@translate}</label></th>
        <td><input type="checkbox" name="hd_resize" id="hd_resize" {$values.hd_resize}></td>
      </tr>
      <tr>
        <th>{'Maximum Width'|@translate}</th>
        <td><input type="text" name="hd_maxwidth" value="{$values.hd_maxwidth}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Maximum Height'|@translate}</th>
        <td><input type="text" name="hd_maxheight" value="{$values.hd_maxheight}" size="4" maxlength="4"> {'pixels'|@translate}</td>
      </tr>
      <tr>
        <th>{'Image Quality'|@translate}</th>
        <td><input type="text" name="hd_quality" value="{$values.hd_quality}" size="3" maxlength="3"> %</td>
      </tr>
    </table>
  </fieldset>
{/if}

  <p>
    <input class="submit" type="submit" name="submit" value="{'Save Settings'|@translate}"/>
  </p>

</form>

</div> <!-- photosAddContent -->
