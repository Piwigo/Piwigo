{combine_css path="themes/default/js/ui/theme/jquery.ui.button.css"}
{combine_css path="themes/elegant/admin/jquery.ui.button.css"}
{footer_script require='jquery.ui.button'}
{literal}
jQuery(document).ready(function(){
  jQuery( ".radio" ).buttonset();
});
{/literal}
{/footer_script}

<div class="titrePage">
  <h2>{'Elegant, Configuration Page'|@translate}</h2>
</div>
<form method="post" class="properties" action="" ENCTYPE="multipart/form-data" name="form" class="properties">
<div id="configContent">
  <fieldset>
    <legend>{'Panels options'|@translate}</legend>
    <p>{'Choose what should be the default state for each panel, or disable the animation:'|@translate}</p>
    <ul>
      <li class="radio" >
        <label for="p_main_menu"><span class="property">{'Main Menu Panel'|@translate}</span>&nbsp;</label>
        <input type="radio" id="p_main_menu_on" name="p_main_menu" value="on" {if $options.p_main_menu=="on"}checked="checked"{/if}><label for="p_main_menu_on">{'Displayed'|@translate}</label>
        <input type="radio" id="p_main_menu_off" name="p_main_menu" value="off" {if $options.p_main_menu=="off"}checked="checked"{/if}><label for="p_main_menu_off">{'Hidden'|@translate}</label>
        <input type="radio" id="p_main_menu_disabled" name="p_main_menu" value="disabled" {if $options.p_main_menu=="disabled"}checked="checked"{/if}><label for="p_main_menu_disabled">{'Disable the animation'|@translate}</label>
      </li>
      <li class="radio" >
        <label for="p_pict_descr"><span class="property">{'Photo Description Panel'|@translate}</span>&nbsp;</label>
        <input type="radio" id="p_pict_descr_on" name="p_pict_descr" value="on" {if $options.p_pict_descr=="on"}checked="checked"{/if}><label for="p_pict_descr_on">{'Displayed'|@translate}</label>
        <input type="radio" id="p_pict_descr_off" name="p_pict_descr" value="off" {if $options.p_pict_descr=="off"}checked="checked"{/if}><label for="p_pict_descr_off">{'Hidden'|@translate}</label>
        <input type="radio" id="p_pict_descr_disabled" name="p_pict_descr" value="disabled" {if $options.p_pict_descr=="disabled"}checked="checked"{/if}><label for="p_pict_descr_disabled">{'Disable the animation'|@translate}</label>
      </li>
      <li class="radio" >
        <label for="p_pict_comment"><span class="property">{'Comments Panel'|@translate}</span>&nbsp;</label>
        <input type="radio" id="p_pict_comment_on" name="p_pict_comment" value="on" {if $options.p_pict_comment=="on"}checked="checked"{/if}><label for="p_pict_comment_on">{'Displayed'|@translate}</label>
        <input type="radio" id="p_pict_comment_off" name="p_pict_comment" value="off" {if $options.p_pict_comment=="off"}checked="checked"{/if}><label for="p_pict_comment_off">{'Hidden'|@translate}</label>
        <input type="radio" id="p_pict_comment_disabled" name="p_pict_comment" value="disabled" {if $options.p_pict_comment=="disabled"}checked="checked"{/if}><label for="p_pict_comment_disabled">{'Disable the animation'|@translate}</label>
      </li>
    </ul>
  </fieldset>
</div>
<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit_elegant" />
</p>
</form>
