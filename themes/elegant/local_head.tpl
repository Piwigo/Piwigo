{if $BODY_ID=='thePicturePage'}
	{combine_script id='elegant.scripts_pp' load='header' require='jquery' path='themes/elegant/scripts_pp.js'}
{else}
	{combine_script id='elegant.scripts' load='header' require='jquery' path='themes/elegant/scripts.js'}
{/if}