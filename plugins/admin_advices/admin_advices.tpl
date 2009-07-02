<div class="content">

  <h3>{'An_advice_about'|@translate} {$ADVICE_ABOUT}</h3>
  <h4>{$ADVICE_TEXT}</h4>
  <table summary="Admin advices summary">
  {if isset($More)}
  <tr>
    <td style="text-align: left; width: 50%;">
    {foreach from=$More item=advice}
      {$advice}<BR />
    {/foreach}
    <br />
    </td>
    <td style="text-align: right; width: 20%;">
    {if isset($thumbnail.IMAGE)}
      <a href="{$thumbnail.U_MODIFY}" title="{'link_info_image'|@translate}">
      <img class="thumbnail" src="{$thumbnail.IMAGE}"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"></a>
      </td><td style="text-align: left;">
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.NAME}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Name'|@translate}<br />
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.COMMENT}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Description'|@translate}<br />
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.AUTHOR}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Author'|@translate}<br />
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.CREATE_DATE}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Creation date'|@translate}<br />
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.METADATA}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Metadata'|@translate}<br />
      <img src="{$themeconf.admin_icon_dir}/{$thumbnail.TAGS}check.png"
           alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}"> {'Tags'|@translate} ({$thumbnail.NUM_TAGS})
    {/if}
    </td>
  </tr>
  {/if}
  </table>
  <div class="summary">
    <h4>External summary</h4>
    <ul>Database Analysis<br />
      {if ($pwgsize != $size)}
      - Space used by {$prefixTable} tables: {$pwgsize}<br />
      {/if}
      - Space used by all tables: {$size}<br />
      - {$checked_tables}<br />
      - Unused allocated space by {$prefixTable} tables: {$pwgspacef}<br />
      {if ($spacef > 0 and $pwgspacef != $spacef)} 
        - Unused allocated space: {$spacef}<br />
      {/if}
      {if ($spacef > 0)}
      Useful links: 
        <a class="internal" href="{$U_maintenance}">{'repair and optimize database'|@translate}</a> - 
        {else}
      MySQL documentation: 
      {/if}
        <a class="external" href="http://dev.mysql.com/doc/" onclick="window.open(this.href, ''); return false;">MySQL</a>
      <br /><br />
    Templates generated on {$smarty.now|date_format:"%A, %B %e, %Y - %r"} by 
    <a class="external" href="http://www.smarty.net/" onclick="window.open(this.href, ''); return false;">Smarty</a>
    {$smarty.version}<br />
    Animations FX by <span class="jQuery"><a class="external" href="http://jquery.com/" onclick="window.open(this.href, ''); return false;">jQuery</a>&nbsp;</span>
    </ul>
  </div>
</div>
<script type="text/javascript">// <![CDATA[
{literal}$(document).ready(function() {
	$("img.thumbnail").fadeTo("slow", 0.6); // Opacity on page load
	$(".jQuery").append($().jquery);
	$("img.thumbnail").hover(function(){
		$(this).fadeTo("slow", 1.0); // Opacity on hover
	},function(){
   		$(this).fadeTo("slow", 0.6); // Opacity on mouseout
	});
});{/literal}
// ]]>
</script>