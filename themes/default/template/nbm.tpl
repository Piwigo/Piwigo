{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content">
	<div class="titrePage">
		<ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
        <span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
      </a></li>
    </ul>
		<h2>{'Notification'|@translate}</h2>
	</div>

{include file='infos_errors.tpl'}

</div>
