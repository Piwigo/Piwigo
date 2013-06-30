{combine_css path="themes/default/js/ui/theme/jquery.ui.button.css"}
{combine_css path="themes/smartpocket/admin/jquery.ui.button.css"}
{footer_script require='jquery.ui.button'}
{literal}
jQuery(document).ready(function(){
  jQuery( ".radio" ).buttonset();
});
{/literal}
{/footer_script}

<div class="titrePage">
  <h2>{'Smartpocket, Configuration Page'|@translate}</h2>
</div>
<form method="post" class="properties" action="" ENCTYPE="multipart/form-data" name="form" class="properties">
<div id="configContent">
  <fieldset>
    <legend>{'Slideshow Options'|@translate}</legend>
    <ul>
      <li class="radio" >
        <label for="loop"><span class="property">{'Loop the slideshow'|@translate}</span>&nbsp;</label>
        <input type="radio" id="loop_true" name="loop" value="true" {if $options.loop}checked="checked"{/if}><label for="loop_true">{'Yes'|@translate}</label>
        <input type="radio" id="loop_false" name="loop" value="false" {if !$options.loop}checked="checked"{/if}><label for="loop_false">{'No'|@translate}</label>
      </li>
      <li class="radio" >
        <label for="autohide"><span class="property">{'Autohide the bar of the slideshow'|@translate}</span>&nbsp;</label>
        <input type="radio" id="autohide_on" name="autohide" value="5000" {if $options.autohide==5000}checked="checked"{/if}><label for="autohide_on">{'Yes'|@translate}</label>
        <input type="radio" id="autohide_off" name="autohide" value="0" {if $options.autohide==0}checked="checked"{/if}><label for="autohide_off">{'No'|@translate}</label>
      </li>
    </ul>
  </fieldset>
</div>
<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit_smartpocket" />
</p>
</form>
