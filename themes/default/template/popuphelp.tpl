<div id="content" class="content">
	<div class="titrePage">
	<ul class="categoryActions">
		<li><a href="#" onclick="window.close();" title="{'Close this window'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-close">&nbsp;</span><span class="pwg-button-text">exit</span>
		</a></li>
	</ul>
	<h2><span id="homeLink"><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}</span>{$PAGE_TITLE}</h2>
	</div>

{$HELP_CONTENT}

<p id="closeLink" style="display:none">
    <a href="#" onclick="window.close();">{'Close this window'|@translate}</a>
</p>

{footer_script require='jquery'}
if (window.opener || window.name) {
	jQuery("#closeLink").show();
	jQuery("#homeLink").hide();
}
{/footer_script}
</div> <!-- content -->


