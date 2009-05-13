{* $Id: /piwigo/trunk/template/yoga/mail/text/html/footer.tpl 7094 2009-04-22T19:32:00.030733Z rub  $ *}
</div> <!-- content -->
<div id="copyright">
<hr>
  {'Sent by'|@translate}
  <a href="{$GALLERY_URL}">{$GALLERY_TITLE}</a>

 {* Please, do not remove this copyright. If you really want to,
      contact us on http://piwigo.org to find a solution on how
      to show the origin of the script...*}
  - {'powered_by'|@translate}
  <a href="http://piwigo.org" class="Piwigo">
  <span class="Piwigo">Piwigo</span></a>
  {$VERSION}

  - {'send_mail'|@translate}
  <a href="mailto:{$MAIL}?subject={$TITLE_MAIL}">{'Webmaster'|@translate}</a>

</div> <!-- copyright -->
</div> <!-- the_page -->

</body>
</html>
