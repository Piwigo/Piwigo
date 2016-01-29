{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
jQuery(document).ready(function() {
  jQuery("select[name=who]").change(function () {
    checkWhoOptions();
  });

  checkWhoOptions();

  function checkWhoOptions() {
    var option = jQuery("select[name=who] option:selected").val();
    jQuery(".who_option").hide();
    jQuery(".who_" + option).show();
  }

  jQuery(".who_option select").selectize({
    plugins: ['remove_button']
  });

  jQuery("form#categoryNotify").submit(function(e) {
    var who_selected = false;
    var who_option = jQuery("select[name=who] option:selected").val();

    if (jQuery(".who_" + who_option + " select").length > 0) {
      if (jQuery(".who_" + who_option + " select option:selected").length > 0) {
         who_selected = true;
      }
    }

    if (!who_selected) {
      jQuery(".actionButtons .errors").show();
      e.preventDefault();
    }
    else {
      jQuery(".actionButtons .errors").hide();
      console.log("form can be submited");
    }
  });
});
{/footer_script}

{html_style}
.who_option {
  margin-top:5px;
}

span.errors {
  background-image:none;
  padding:2px 5px;
  margin:0;
  border-radius:5px;
}
{/html_style}

<div class="titrePage">
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form action="{$F_ACTION}" method="post" id="categoryNotify">

<fieldset id="emailCatInfo">
  <legend>{'Send mail to users'|@translate}</legend>

  <p>
    <strong>{'Recipients'|@translate}</strong>
    <select name="who">
      <option value="group">{'Group'|translate}</option>
      <option value="users">{'Users'|translate}</option>
    </select>
  </p>

  <p class="who_option who_group">
{if isset($group_mail_options)}
    <select name="group" placeholder="{'Type in a search term'|translate}" style="width:524px;">
      {html_options options=$group_mail_options}
    </select>
{elseif isset($no_group_in_gallery) and $no_group_in_gallery}
    {'There is no group in this gallery.'|@translate} <a href="admin.php?page=group_list" class="externalLink">{'Group management'|@translate}</a>
{else}
    {'No group is permitted to see this private album'|@translate}.
    <a href="{$permission_url}" class="externalLink">{'Permission management'|@translate}</a>
{/if}
    </p>

    <p class="who_option who_users">
{if isset($user_options)}
    <select name="users[]" multiple placeholder="{'Type in a search term'|translate}" style="width:524px;">
      {html_options options=$user_options selected=$user_options_selected}
    </select>
{else}
    {'No user is permitted to see this private album'|@translate}.
    <a href="{$permission_url}" class="externalLink">{'Permission management'|@translate}</a>
{/if}
    </p>

  <p>
    <strong>{'Complementary mail content'|@translate}</strong>
    <br>
    <textarea cols="50" rows="5" name="mail_content" id="mail_content" class="description">{$MAIL_CONTENT}</textarea>
  </p>

{if isset($auth_key_duration)}
  <p>
  {'Each email sent will contain its own automatic authentication key on links, valid for %s.'|translate:$auth_key_duration}
  <br>{'For security reason, authentication keys do not work for administrators.'|translate}
  </p>
{/if}

  <p class="actionButtons">
    <input class="submit" type="submit" value="{'Send'|@translate}" name="submitEmail">
    <span class="errors" style="display:none">&#x2718; {'No recipient selected'|translate}</span>
  </p>

</fieldset>

</form>
