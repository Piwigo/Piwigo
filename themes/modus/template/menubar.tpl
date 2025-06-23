{footer_script require='jquery'}
		var h = jQuery("#theHeader div.banner").css("height");
		var d = jQuery("#menuSwitcher").css("padding-top");

		jQuery(document).ready(function(){
			if( jQuery('#theHeader div.banner').is(':visible') && jQuery("body").css("display") == "flex"){
				jQuery("#menuSwitcher").css("padding-top",parseInt(h)+parseInt(d));
			};
		});
{/footer_script}


{* basically a copy of menubar.tpl with manual inclusion of menubar_xxx.tpl *}
{if !empty($blocks)}
<aside id=menubar>
	{foreach from=$blocks key=id item=block}{if ($id!="mbIdentification" && $id!="mbMenu")}
	<dl id={$id}>
		{if $id=="mbLinks"}
{* ============ mbLinks ========== *}
{if isset($block->data) and count($block->data)==1}
<dt><a href="{$block->data[0].URL}">{$block->data[0].LABEL}</a></dt>
{else}
<dt><a>{'Links'|@translate}</a></dt>
<dd>
	<ul>{strip}
		{foreach from=$block->data item=link}
			<li>
				<a href="{$link.URL}" class="external"{if isset($link.new_window)} onclick="window.open(this.href, '{$link.new_window.NAME}','{$link.new_window.FEATURES}'); return false;"{/if}>
				{$link.LABEL}
				</a>
			</li>
		{/foreach}
	{/strip}</ul>
</dd>
{/if}

		{elseif $id=="mbTags"}
{* ============ mbTags ========== *}
<dt><a>{'Related tags'|@translate}</a></dt>
<dd>
	<div id=menuTagCloud>
		{foreach from=$block->data item=tag}{strip}
			<a class="tagLevel{if isset($tag.level)}{$tag.level}{/if}" href=
			{if isset($tag.U_ADD)}
				"{$tag.U_ADD}" title="{$tag.counter|@translate_dec:'%d photo is also linked to current tags':'%d photos are also linked to current tags'}" rel=nofollow>+
			{else}
				"{$tag.URL}" title="{'display photos linked to this tag'|@translate}">
			{/if}
				{$tag.name}</a>{/strip}
		{/foreach}
	</div>
</dd>

		{elseif $id=="mbSpecials"}
{* ============ mbSpecials ========== *}
<dt><a>{'Explore'|@translate}</a></dt>
<dd>
	<ul>{strip}
		{foreach from=$block->data item=link}
		<li><a href="{$link.URL}" title="{$link.TITLE}"{if isset($link.REL)} {$link.REL}{/if}>{$link.NAME}</a></li>
		{/foreach}
		{if isset($blocks.mbMenu)}
		<hr>
		{foreach from=$blocks.mbMenu->data item=link}{if is_array($link)}
		<li><a href="{$link.URL}" title="{if isset($link.TITLE)}{$link.TITLE}{/if}"{if isset($link.REL)} {$link.REL}{/if}>{$link.NAME}</a>{if isset($link.COUNTER)} ({$link.COUNTER}){/if}</li>
		{/if}{/foreach}
		{/if}
	{/strip}</ul>
</dd>

		{else}
		{if not empty($block->template)}
		{include file=$block->template }
		{else}
		{$block->raw_content}
		{/if}
		{/if}
	</dl>
	{/if}{/foreach}
{* ============ Horizontal menu specificities ========== *}
{if isset($blocks.mbSpecials->data.most_visited)}
<dl id="mbMostVisited"><dt><a href="{$blocks.mbSpecials->data.most_visited.URL}" title="{$blocks.mbSpecials->data.most_visited.TITLE}">{$blocks.mbSpecials->data.most_visited.NAME}</a></dt></dl>
{/if}
{if isset($blocks.mbSpecials->data.best_rated)}
<dl id="mbBestRated"><dt><a href="{$blocks.mbSpecials->data.best_rated.URL}" title="{$blocks.mbSpecials->data.best_rated.TITLE}">{$blocks.mbSpecials->data.best_rated.NAME}</a></dt></dl>
{/if}
{if isset($blocks.mbSpecials->data.recent_pics)}
<dl><dt><a href="{$blocks.mbSpecials->data.recent_pics.URL}" title="{$blocks.mbSpecials->data.recent_pics.TITLE}">{$blocks.mbSpecials->data.recent_pics.NAME}</a></dt></dl>
{/if}
<dl style="float:none">
	<form style="margin:0;display:inline" action="{$ROOT_URL}qsearch.php" method=get id=quicksearch onsubmit="return this.q.value!='';">
		<input type="text" name=q id=qsearchInput placeholder="{'Search'|@translate|escape:'html'}..." {if !empty($QUERY_SEARCH)} value="{$QUERY_SEARCH}"{/if}>
	</form>
</dl>
{if isset($U_LOGIN)}
<dl style="float:right;margin-top:3px">
	<dt style="font-size:100%;font-weight:normal;padding-left:15px{*to avoid loosing hover*}"><a href="{$U_LOGIN}" rel=nofollow>{'Login'|@translate}</a></dt>
	<dd style="right:0">
		<ul>
		<li><a href="{$U_LOGIN}" rel="nofollow">{'Login'|@translate}</a></li>
		{if isset($U_REGISTER)}
		<li><a href="{$U_REGISTER}" title="{'Create a new account'|@translate}" rel="nofollow">{'Register'|@translate}</a></li>
		{/if}
		<li><a href="{$U_LOST_PASSWORD}" title="{'Forgot your password?'|@translate}" rel="nofollow">{'Forgot your password?'|@translate}</a></li>
		</ul>
{strip}
		<form method=post action="{$U_LOGIN}" id=quickconnect>
		<fieldset>
		<legend>{'Quick connect'|@translate}</legend>
		<p>
		<label for=userX>{'Username'|@translate}</label><br>
		<input type=text name=username id=userX value="" style="width:99%">
		</p>

		<p><label for=passX>{'Password'|@translate}</label><br>
		<input type=password name=password id=passX style="width:99%">
		</p>

		{if $AUTHORIZE_REMEMBERING}
		<p><label>
		{'Auto login'|@translate}&nbsp;<input type=checkbox name=remember_me value=1>
		</label></p>
		{/if}

		<p>
		<input type=hidden name=redirect value="{$smarty.server.REQUEST_URI|@urlencode}">
		<input type=submit name=login value="{'Submit'|@translate}">
		</p>

		</fieldset>
		</form>
{/strip}
	</dd>
</dl>
{/if}
{if isset($U_LOGOUT)}
<dl style="float:right;margin-top:3px">
	<dt style="font-size:100%;font-weight:normal">
	{if isset($USERNAME)}{'Hello'|@translate} {if isset($U_PROFILE)}<a href="{$U_PROFILE}">{/if}{$USERNAME}{if isset($U_PROFILE)}</a>{/if} ! &nbsp;{/if}
	<a href="{$U_LOGOUT}">{'Logout'|@translate}</a>
	{if isset($U_PROFILE)}
	<a id="mbProfile" href="{$U_PROFILE}" title="{'customize the appareance of the gallery'|@translate}">{'Customize'|@translate}</a>
	{/if}
	{if isset($U_ADMIN)}
	<a href="{$U_ADMIN}" title="{'available for administrators only'|@translate}">{'Admin'|@translate}</a>
	{/if}
	</dt>
</dl>
{/if}

</aside>
{/if}
<a id="menuSwitcher" class="pwg-button" title="{'Menu'|@translate}"><span class="pwg-icon pwg-icon-menu"></span></a>
{combine_script id='zzz.d1.menu' load='async' path="themes/`$themeconf.id`/js/menuh.js" require="jquery" version=0}
