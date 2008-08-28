
<!-- random picture menu bar -->
<dt>{$block->get_title()}</dt>
<dd>
  <div class="illustration" style="text-align:center;padding:5px;font-size:85%;">
    {if $block->data.IMGNAME!="" and $block->data.SHOWNAME=="o"}{$block->data.IMGNAME}<br/>{/if}
    {if $block->data.IMGCOMMENT!="" and $block->data.SHOWCOMMENT=="o"}{$block->data.IMGCOMMENT}<br/>{/if}
    <a href="{$block->data.LINK}"><img src="{$block->data.IMG}"/></a>
    {if $block->data.IMGNAME!="" and $block->data.SHOWNAME=="u"}<br/>{$block->data.IMGNAME}{/if}
    {if $block->data.IMGCOMMENT!="" and $block->data.SHOWCOMMENT=="u"}<br/>{$block->data.IMGCOMMENT}{/if}
  </div>
</dd>