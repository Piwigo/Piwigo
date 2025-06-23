#menubar UL {
	list-style: none;
	padding: 0 0 0 2px;
	margin: 0; /*various default user agent css*/
}

{if !isset($loaded_plugins['rv_menutree'])}
#mbCategories UL {
	list-style-type: square;
	padding-left: 8px;
}
{/if}

#menubar LI.selected>A {
	font-weight: bold;
}

#menubar .menuInfoCat {
	padding:0px 5px;
	font-size: 90%;
	border-radius: 20px;
	font-weight: bold;
{if !empty($skin.menubar.badgeBackgroundColor)}
	background-color: {$skin.menubar.badgeBackgroundColor};
{/if}
{if !empty($skin.menubar.badgeColor)}
	color: {$skin.menubar.badgeColor};
{/if}
}

#menubar .menuInfoCat::before {
  content:'[';
}
#menubar .menuInfoCat::after {
  content:']';
}

#menubar .menuInfoCatByChild {
	font-size: 80%;
	font-style: italic;
}

#menubar INPUT {
	text-indent: 2px;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#quickconnect FIELDSET {
	margin: 0 5px 0 0;
	padding: 0 5px;
}


#menuTagCloud {
	text-align: center;
}

#menuTagCloud A {
	white-space: nowrap;
	margin-right: 5px;
}





/* Horizontal menu */
#menubar {
	margin: 0;
	width: 100%;
	padding: 5px 0 8px;
	background-color: {$skin.menubar.backgroundColor};
{if !empty($skin.menubar.gradient)}
	{$skin.menubar.gradient|cssGradient}
{/if}
{if !empty($skin.menubar.color)}
	color: {$skin.menubar.color};
{/if}
}

{if !empty($skin.menubar.link.color)}
#menubar DT A {
	color: {$skin.menubar.link.color};
}
{/if}

{if !empty($skin.menubar.linkHover.color)}
#menubar DT A:hover {
	color: {$skin.menubar.linkHover.color};
}
{/if}

#menubar DL {
	display: inline;
	float: left;
	margin: 0 0.25em;
	padding: 0 0.25em;
}

#menubar DT {
	display: inline;
	cursor: pointer;
	font-size: 120%;
	font-weight: bold;
	text-align: center;
}

#menubar DD {
	display: none;
	position: absolute;
	margin: 0;
	padding: 10px;
	line-height: 150%;
	max-width: 300px;
	box-shadow: 2px 2px 5px gray;
	background-color: {$skin.dropdowns.backgroundColor};
{if !empty($skin.menubar.color)}
	color: {$skin.BODY.color};
{/if}
}

#menubar DD A {
		font-size: 14px;
}


#menubar DL:hover > DD {
	display: block;
	z-index: 5;
}

#content {
	clear: both;
}

#qsearchInput {
	width: 13%;
	max-width: 180px;
}




#menuSwitcher {
	display: none;
}

@media screen and (max-width:980px) {
	#mbProfile {
		display: none !important;
	}
	#mbTags {
		display: none !important;
	}
}

@media screen and (max-width:840px) {
	#mbMostVisited {
		display: none !important;
	}
}

@media screen and (max-width:640px) {
	#mbBestRated {
		display: none !important;
	}

	#menuSwitcher {
		display: block;
		position: absolute;
		padding-top: 2px;
{if !empty($skin.pageTitle.link.color)}
		color: {$skin.pageTitle.link.color};/*switcher is outside page title so not inherited*/
{/if}
	}

{if !empty($skin.pageTitle.linkHover.color)}
	#menuSwitcher:hover {
		color: {$skin.pageTitle.linkHover.color};/*switcher is outside page title so not inherited*/
	}
{/if}

	.contentWithMenu .titrePage H2,
	.contentWithMenu .browsePath {
		text-indent: 25px; /*make space for menu switcher*/
		letter-spacing: -0.5px;
	}

	.titrePage H2:first-line,
	.browsePath:first-line {
		line-height: 28px; /*long bread crumbs go on second line and would run into menu switcher*/
	}

	#menubar {
		display: none;
		position: absolute;
		width: auto;
		box-shadow: 2px 2px 5px gray;
		opacity: 0.95;
		z-index: 5;
		min-width: 40%;
{if $skin.menubar.backgroundColor != $skin.dropdowns.backgroundColor}
		background-color: {$skin.dropdowns.backgroundColor};
{/if}
{if !empty($skin.menubar.gradient)}
		background-image: none;
{/if}
{if !empty($skin.menubar.color)}
		color: inherit;
{/if}
	}

{if !empty($skin.menubar.link.color)}
	#menubar DT A {
			color: {$skin.A.color}; /*update link color because background is dropdowns; don't care about hover as this is mobile probably...*/
	}
{/if}

	#menubar DL {
		display: block;
		float: none;
		margin-top: 4px;
		margin-bottom: 4px;
	}

	#menubar DT {
		display: block;
		text-align: left;
		font-size: 20px;
		font-weight: normal;
	}

	#menubar DL:hover > DD { /*reset large wifth hover effect*/
		display: none;
	}
	
	#menubar DD {
		position: static;
		box-shadow: none; /*reset std*/
		padding-top: 5px; /*reduce from standard*/
	}

	#qsearchInput {
		width: 100%;
		max-width: none;
		margin: 5px 0;
	}

}
