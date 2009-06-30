</ul>
<ul class="categoryActions">
<li class="menuf">
	<div>
		<ul>
			<li>
				<a rel="nofollow" href="#">
					<img class="flags" src="{$lang_switch.Active.img}" alt="{$lang_switch.Active.alt}" title="{$lang_switch.Active.alt}"/>
				</a>
				<!--[if lte IE 6]>
					<a rel="nofollow" href="#">
						<img class="flags" src="{$lang_switch.Active.img}" alt="{$lang_switch.Active.alt}" title="{$lang_switch.Active.alt}"/>
						<table>
							<tr>
								<td>
				<![endif]-->
									<ul class="flag-pan">
									{foreach from=$lang_switch.flags key=code item=flag name=f}
										<li>
											<a rel="nofollow" href="{$SCRIPT_NAME}{$flag.url}">
												<img class="flags" src="{$flag.img}" alt="{$flag.alt}" title="{$flag.alt}"/>
											</a>
										</li>
									{/foreach}
									</ul>
				<!--[if lte IE 6]>
								</td>
							</tr>
						</table>
					</a>
				<![endif]-->
			</li>
		</ul>
	</div>
</li>
{html_head}
{if $themeconf.template=='yoga' and $themeconf.theme=='Sylvia'}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}{$LANGUAGE_SWITCH_PATH|@cat:'language_switch.css'}"> 
{else}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}{$LANGUAGE_SWITCH_PATH|@cat:'language_switch-default.css'}"> 
{/if}
{if Componant_exists($LANGUAGE_SWITCH_PATH, 'language_switch-local.css')}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}{$LANGUAGE_SWITCH_PATH|@cat:'language_switch-local.css'}"> 
{/if}
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}{$LANGUAGE_SWITCH_PATH|@cat:'language_switch-ie6.css'}"> 
<![endif]-->
{/html_head}