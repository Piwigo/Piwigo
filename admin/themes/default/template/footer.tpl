{* 
          Warning : This is the admin pages footer only 
          don't be confusing with the public page footer
*}
</div> <!-- pwgMain -->
{if isset($footer_elements)}
{foreach from=$footer_elements item=v}
{$v}
{/foreach}
{/if}
{if isset($debug.QUERIES_LIST)}
<div id="debug">
{$debug.QUERIES_LIST}
</div>
{/if}

<div id="footer">
  <div id="piwigoInfos">
 {* Please, do not remove this copyright. If you really want to,
      contact us on http://piwigo.org to find a solution on how
      to show the origin of the script...
  *}

  {'Powered by'|@translate}
  <a href="{$PHPWG_URL}" title="Visit Piwigo project website">
  <span class="Piwigo">Piwigo</span></a>
  {$VERSION}
  | <a class="external" href="{$pwgmenu.WIKI}" title="Read Piwigo Documentation" onclick="window.open(this.href, ''); return false;">{'Documentation'|@translate}</a>
  | <a class="external" href="{$pwgmenu.FORUM}" title="Get Support on Piwigo Forum" onclick="window.open(this.href, ''); return false;">{'Support'|@translate}</a>
  </div> <!-- piwigoInfos -->

  <div id="pageInfos">
 {if isset($debug.TIME) }
 {'SQL queries in'|@translate} {$debug.TIME} ({$debug.NB_QUERIES} {'SQL queries in'|@translate} {$debug.SQL_TIME}) -
 {/if}


  {'Contact'|@translate}
  <a href="mailto:{$CONTACT_MAIL}?subject={'A comment on your site'|@translate|@escape:url}">{'Webmaster'|@translate}</a>
  </div> <!-- pageInfos -->

</div> <!-- footer -->
</div> <!-- the_page -->

{literal}
<script type='text/javascript'>
  $(function() {
    $('#pwgHead A, #footer A, .themeActions A').tipTip({
        'delay' : 0,
        'fadeIn' : 200,
        'fadeOut' : 200,
    });
  });
</script>
{/literal}

</body>
</html>