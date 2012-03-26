{if $BODY_ID=='thePicturePage'}
	{combine_script id='elegant.scripts_pp' load='footer' require='jquery' path='themes/elegant/scripts_pp.js'}
{else}
	{combine_script id='elegant.scripts' load='footer' require='jquery' path='themes/elegant/scripts.js'}
{/if}