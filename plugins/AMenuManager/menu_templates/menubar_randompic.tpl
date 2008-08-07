
<!-- random picture menu bar -->
{if $section.NAME!=""}
  <dt>{$section.NAME|@translate}</dt>
{/if}
<dd>
  <div class="illustration" style="text-align:center;padding:5px;font-size:85%;">
    {if $section.ITEMS.IMGNAME!="" and $section.ITEMS.SHOWNAME=="o"}{$section.ITEMS.IMGNAME}<br/>{/if}
    {if $section.ITEMS.IMGCOMMENT!="" and $section.ITEMS.SHOWCOMMENT=="o"}{$section.ITEMS.IMGCOMMENT}<br/>{/if}
    <a href="{$section.ITEMS.LINK}"><img src="{$section.ITEMS.IMG}"/></a>
    {if $section.ITEMS.IMGNAME!="" and $section.ITEMS.SHOWNAME=="u"}<br/>{$section.ITEMS.IMGNAME}{/if}
    {if $section.ITEMS.IMGCOMMENT!="" and $section.ITEMS.SHOWCOMMENT=="u"}<br/>{$section.ITEMS.IMGCOMMENT}{/if}
  </div>
</dd>