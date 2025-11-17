{* 
          Warning : This is the admin pages footer only 
          don't be confusing with the public page footer
*}
</div>{* <!-- pwgMain --> *}

{if isset($footer_elements)}
{foreach from=$footer_elements item=elt}
  {$elt}
{/foreach}
{/if}

{if isset($debug.QUERIES_LIST)}
<div id="debug">
  {$debug.QUERIES_LIST}
</div>
{/if}

<div id="footer">
  <div>
    <a class="externalLink tiptip piwigo-logo" href="{$PHPWG_URL}" title="{'Visit Piwigo project website'|translate}"><img src="admin/themes/default/images/piwigo-grey.svg"></a>
    {if isset($DISPLAY_BELL) and $DISPLAY_BELL}
    <span id="whats_new_notification" class="icon-blue tiptip" onclick="show_user_whats_new()" title="{'What\'s new in version %s'|translate:$WHATS_NEW_MAJOR_VERSION}">
      <i class="icon-bell"></i>
    </span>
    {/if}
  </div>
  <div id="pageInfos">
    {if isset($debug.TIME) }
    {'Page generated in'|translate} {$debug.TIME} ({$debug.NB_QUERIES} {'SQL queries in'|translate} {$debug.SQL_TIME}) -
    {/if}

    {'Contact'|translate}
    <a href="mailto:{$CONTACT_MAIL}?subject={'A comment on your site'|translate|escape:url}">{'Webmaster'|translate}</a>
  </div>{* <!-- pageInfos --> *}

</div>{* <!-- footer --> *}
</div>{* <!-- the_page --> *}

{if (isset($SHOW_WHATS_NEW) and $SHOW_WHATS_NEW) or (isset($DISPLAY_BELL) and $DISPLAY_BELL)}
<div id="whats_new">
    <div id="whats_new_popin">
      <a class="icon-cancel close_whats_new" onClick="hide_user_whats_new()"></a>
      <h3>{'What\'s new in version %s'|translate:$WHATS_NEW_MAJOR_VERSION}</h3>
      <div>
        <div class="whats_new_block_container">
          <div class="whats_new_block icon-blue whats_new_block_w_60">
            <h4>{'Brand new login & preferences with Standard pages !'|translate}</h4>
            <a href="{$RELEASE_NOTE_URL}" target="_blank"><img src="{$WHATS_NEW_IMGS.1}"></a>
          </div>
          <div class="whats_new_block icon-purple whats_new_block_w_40">
            <h4>{'Two factor authentication is here'|translate}</h4>
            <a href="{$RELEASE_NOTE_URL}" target="_blank"><img src="{$WHATS_NEW_IMGS.2}"></a>
          </div>
        </div>
        <div class="whats_new_block_container">
          <div class="whats_new_block icon-green whats_new_block_w_100">
            <h4>{'UI redesigns that make a difference'|translate}</h4>
            <a href="{$RELEASE_NOTE_URL}" target="_blank"><img src="{$WHATS_NEW_IMGS.3}"></a>
          </div>
          {* <div class="whats_new_block icon-yellow">
            <h4>{'Even more filters for the gallery search engine'|translate}</h4>
            <a href="{$RELEASE_NOTE_URL}" target="_blank"><img src="{$WHATS_NEW_IMGS.4}"></a>
          </div>
        </div> *}
      </div>
      <div class="whats_new_buttons">
        <button onClick="hide_user_whats_new()"><i class="icon-thumbs-up"></i> {'Ok, got it!'|translate}</button>
        <a class="buttonLike" href="{$RELEASE_NOTE_URL}" target="_blank"><i class="icon-book"></i> {'Read the release note'|translate}</a>
      </div>
    </div>
</div>
{/if}

<style>


</style>


{combine_script id='jquery.tipTip' load='footer' path='themes/default/js/plugins/jquery.tipTip.minified.js'}
{footer_script require='jquery.tipTip'}
jQuery('.tiptip').tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200
});

jQuery('a.externalLink').click(function() {
  window.open(jQuery(this).attr("href"));
  return false;
});

function hide_user_whats_new() {
  $.ajax({
    url: "ws.php?format=json&method=pwg.users.preferences.set",
    type: "POST",
    dataType: "JSON",
    data: {
      param: 'show_whats_new_{$WHATS_NEW_MAJOR_VERSION}',
      value: false,
    }
  })
  $('#whats_new').hide();
}

function show_user_whats_new() {
  $('#whats_new').show();
}

{if isset($SHOW_WHATS_NEW) && $SHOW_WHATS_NEW}
  show_user_whats_new()
{/if}



{/footer_script}

<!-- BEGIN get_combined -->
{get_combined_scripts load='footer'}
<!-- END get_combined -->

</body>
</html>