            {* <!-- end $CONTENT --> *}
          </td></tr>

          <tr><td id="footer">
            {* <!-- begin FOOTER --> *}
{* <!-- Please, do not remove this copyright. If you really want to,
contact us on http://piwigo.org to find a solution on how
to show the origin of the script... --> *}
      
            {'Sent by'|translate} <a href="{$GALLERY_URL}">{$GALLERY_TITLE}</a>
            - {'Powered by'|translate} <a href="{$PHPWG_URL}" class="Piwigo">Piwigo</a>
            {if not empty($VERSION)}{$VERSION}{/if}
            
            - {'Contact'|translate}
            <a href="mailto:{$CONTACT_MAIL}?subject={'A comment on your site'|translate|escape:url}">{'Webmaster'|@translate}</a>
            {* <!-- end FOOTER --> *}
          </td></tr>
        </table>

      </td></tr>
    </table>
  </body>
</html>