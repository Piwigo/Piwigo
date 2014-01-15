{html_style}
.graphicalCheckbox {
  font-size:16px;
  line-height:16px;
}

input[type=checkbox] {
  display:none;
}
{/html_style}

{footer_script}{literal}
jQuery(document).ready(function() {
  jQuery('form li label input[type=checkbox]').change(function() {
    jQuery(this).prev().toggleClass('icon-check icon-check-empty');
  });
});
{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Smartpocket, Configuration Page'|@translate}</h2>
</div>
<form method="post" class="properties" action="" ENCTYPE="multipart/form-data" name="form" class="properties">
<div id="configContent">
  <fieldset>
    <legend>{'Slideshow Options'|@translate}</legend>
    <ul>

      <li><label>
        <span class="property">{'Loop the slideshow'|@translate}</span>&nbsp;
        <span class="graphicalCheckbox icon-check{if not $options.loop}-empty{/if}">&nbsp;</span>
        <input type="checkbox" name="loop"{if $options.loop} checked="checked"{/if}>
      </label></li>

      <li><label>
        <span class="property">{'Autohide the bar of the slideshow'|@translate}</span>&nbsp;
        <span class="graphicalCheckbox icon-check{if $options.autohide != 5000}-empty{/if}">&nbsp;</span>
        <input type="checkbox" name="autohide"{if $options.autohide == 5000} checked="checked"{/if}>
      </label></li>

    </ul>
  </fieldset>
</div>
<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit_smartpocket" />
</p>
</form>
