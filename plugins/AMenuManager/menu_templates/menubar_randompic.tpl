
<!-- random picture menu bar -->
<dt>{$block->get_title()}</dt>
<dd>
  <div class="illustration" style="text-align:center;padding:5px;font-size:85%;">
    {if $block->data.IMGNAME!="" and $block->data.SHOWNAME=="o"}{$block->data.IMGNAME}<br/>{/if}
    {* No strip_tags because comment could have those for good reasons *}
    {* Over comment is limited to 127 characters for look only *}
    {if $block->data.IMGCOMMENT!="" and $block->data.SHOWCOMMENT=="o" and strlen($block->data.IMGCOMMENT) < 128}{$block->data.IMGCOMMENT}<br/>{/if}
    <a href="{$block->data.LINK}"><img src="{$block->data.IMG}"/></a>
    {if $block->data.IMGNAME!="" and $block->data.SHOWNAME=="u"}<br/>{$block->data.IMGNAME}{/if}
    {* Under comment is limited to 255 characters *}
    {if $block->data.IMGCOMMENT!="" and $block->data.SHOWCOMMENT=="u" and strlen($block->data.IMGCOMMENT) < 256}<br/>{$block->data.IMGCOMMENT}{/if}
  </div>
</dd>