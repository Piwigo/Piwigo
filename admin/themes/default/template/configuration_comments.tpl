{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
(function(){
  var targets = {
    'input[name="comments_validation"]' : '#email_admin_on_comment_validation',
    'input[name="user_can_edit_comment"]' : '#email_admin_on_comment_edition',
    'input[name="user_can_delete_comment"]' : '#email_admin_on_comment_deletion'
  };

  for (selector in targets) {
    var target = targets[selector];

    jQuery(target).toggle(jQuery(selector).is(':checked'));

    (function(target){
      jQuery(selector).on('change', function() {
        jQuery(target).toggle($(this).is(':checked'));
      });
    })(target);
  };

  function check_activate_comments() {
    jQuery("#comments_param_container").toggle(jQuery("input[name=activate_comments]").is(":checked"));
  }
  check_activate_comments();
  jQuery("input[name=activate_comments]").on("change", function() {
    check_activate_comments();
  });
}());
{/footer_script}

<h2>{'Piwigo configuration'|translate} {$TABSHEET_TITLE}</h2>

<form method="post" action="{$F_ACTION}" class="properties">

<div id="configContent">

  <fieldset id="commentsConf" class="no-border">
    <ul>
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="activate_comments" id="activate_comments"{if ($comments.activate_comments)} checked="checked"{/if}>
          {'Activate comments'|translate}
        </label>
      </li>
    </ul>

    <ul id="comments_param_container">
      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="comments_forall" {if ($comments.comments_forall)}checked="checked"{/if}>
          {'Comments for all'|translate}
        </label>
      </li>

      <li>
        <label>
          {'Number of comments per page'|translate}
          <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{$comments.NB_COMMENTS_PAGE}">
        </label>
      </li>

      <li>
        <label>
          {'Default comments order'|translate}
          <select name="comments_order">
            {html_options options=$comments.comments_order_options selected=$comments.comments_order}
          </select>
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="comments_validation" {if ($comments.comments_validation)}checked="checked"{/if}>
          {'Validation'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="comments_author_mandatory" {if ($comments.comments_author_mandatory)}checked="checked"{/if}>
          {'Username is mandatory'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="comments_email_mandatory" {if ($comments.comments_email_mandatory)}checked="checked"{/if}>
          {'Email address is mandatory'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="comments_enable_website" {if ($comments.comments_enable_website)}checked="checked"{/if}>
          {'Allow users to add a link to their website'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="user_can_edit_comment" {if ($comments.user_can_edit_comment)}checked="checked"{/if}>
          {'Allow users to edit their own comments'|translate}
        </label>
      </li>

      <li>
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="user_can_delete_comment" {if ($comments.user_can_delete_comment)}checked="checked"{/if}>
          {'Allow users to delete their own comments'|translate}
        </label>
      </li>

      <li id="notifyAdmin">
        <strong>{'Notify administrators when a comment is'|translate}</strong>

        <label id="email_admin_on_comment_validation" class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="email_admin_on_comment_validation" {if ($comments.email_admin_on_comment_validation)}checked="checked"{/if}>
          {'pending validation'|translate}
        </label>

        <label class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="email_admin_on_comment" {if ($comments.email_admin_on_comment)}checked="checked"{/if}>
          {'added'|translate}
        </label>

        <label id="email_admin_on_comment_edition" class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="email_admin_on_comment_edition" {if ($comments.email_admin_on_comment_edition)}checked="checked"{/if}>
          {'modified'|translate}
        </label>

        <label id="email_admin_on_comment_deletion" class="font-checkbox no-bold">
          <span class="icon-check"></span>
          <input type="checkbox" name="email_admin_on_comment_deletion" {if ($comments.email_admin_on_comment_deletion)}checked="checked"{/if}>
          {'deleted'|translate}
        </label>
      </li>
    </ul>
  </fieldset>

</div> <!-- configContent -->

<p class="formButtons">
  <input type="submit" name="submit" value="{'Save Settings'|translate}">
</p>

</form>