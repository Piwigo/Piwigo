{if !empty($navbar)}
{include file='navigation_bar.tpl'|@get_extent:'navbar'}
{else}
<div class="pwg_footer ui-bar-a">
  <h6>
	{'Powered by'|@translate}	<a href="{$PHPWG_URL}" class="Piwigo">Piwigo</a>
	{$VERSION}
	{if isset($CONTACT_MAIL)}
	- {'Contact'|@translate}
	<a href="mailto:{$CONTACT_MAIL}?subject={'A comment on your site'|@translate|@escape:url}">{'Webmaster'|@translate}</a>
	{/if}
  <br>{'Display'|@translate} :
    <b>{'Mobile'|@translate}</b> | <a href="{$TOGGLE_MOBILE_THEME_URL}">{'Classic'|@translate}</a>
  </h6>
</div>
{/if}
{get_combined_scripts load='footer'}
</div><!-- /page -->

</body>
</html>