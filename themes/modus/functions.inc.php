<?php
function modus_css_gradient($gradient) {
	if (!empty($gradient))
	{
		$std = implode(',',$gradient);
		$gs=trim($gradient[0],'#'); $ge=trim($gradient[1],'#');
		return "filter: progid:DXImageTransform.Microsoft.gradient(startColorStr=#FF$gs,endColorStr=#FF$ge); /* IE to 9*/
	background-image: -moz-linear-gradient(top,$std); /* FF 3.16 to 15 */
	background-image: -webkit-linear-gradient(top,$std); /* Chrome, Safari */
	background-image: -ms-linear-gradient(top,$std); /* IE ? to 9 */
	background-image: -o-linear-gradient(top,$std); /* Opera 11 to 12 */
	background-image: linear-gradient(to bottom,$std); /* Standard must be last */";
	}
}

function modus_get_default_config()
{
  return array(
	'skin' => 'newspaper',
	'album_thumb_size' => 250,
  'index_photo_deriv'=>'2small',
  'index_photo_deriv_hdpi'=>'xsmall',
  'display_page_banner' => false,
);
}

function modus_smarty_prefilter($source)
{
	global $lang, $conf;

	$source = str_replace('<div id="imageHeaderBar">', '<div class=titrePage id=imageHeaderBar>', $source );
	$source = str_replace('<div id=imageHeaderBar>',   '<div class=titrePage id=imageHeaderBar>', $source );

	if (!isset($lang['modus_theme']))
		load_language('theme.lang', dirname(__FILE__).'/');

	// picture page actionButtons wrap for mobile
	if (strpos($source, '<div id="imageToolBar">')!==false || strpos($source, '<div id=imageToolBar>')!==false){
		if ( !($pos=strpos($source,'<div class="actionButtons">') ) )
			$pos = strpos($source,'<div class=actionButtons>');
		if ($pos !== false)
		{
			$source = substr_replace($source, '<div class=actionButtonsWrapper><a id=imageActionsSwitch class=pwg-button><span class="pwg-icon pwg-icon-ellipsis"></span></a>{combine_script version=1 id=\'modus.async\' path="themes/`$themeconf.id`/js/modus.async.js" load=\'async\'}', $pos, 0);
			$pos = strpos($source,'caddie', $pos+1);
			$pos = strpos($source,'</div>', $pos+1);
			$source = substr_replace($source, '</div>', $pos, 0);
		}
	}

	/* move imageNumber from imageToolBar to imageHeaderBar*/
	if (preg_match('#<div[ a-zA-Z"=]+id="?imageHeaderBar"?>#', $source, $matches, PREG_OFFSET_CAPTURE)
		&& preg_match('#<div class="?imageNumber"?>{\\$PHOTO}</div>#', $source, $matches2, PREG_OFFSET_CAPTURE, $matches[0][1]+20))
	{
		$source = substr_replace($source, '', $matches2[0][1], strlen($matches2[0][0]));
		$source = substr_replace($source, $matches2[0][0], $matches[0][1]+strlen($matches[0][0]),0);
	}

	if ( ($pos=strpos($source, '<ul class="categoryActions">'))!==false || ($pos=strpos($source, '<ul class=categoryActions>'))!==false){
		if ( ($pos2=strpos($source, '</ul>', $pos))!==false
			&& (substr_count($source, '<li>', $pos, $pos2-$pos) > 2) )
			$source = substr_replace($source, '<a id=albumActionsSwitcher class=pwg-button><span class="pwg-icon pwg-icon-ellipsis"></span></a>{combine_script version=1 id=\'modus.async\' path="themes/`$themeconf.id`/js/modus.async.js" load=\'async\'}', $pos, 0);
	}

	$re = preg_quote('<img title="{$cat.icon_ts.TITLE}" src="', '/')
			.'[^>]+'
			.preg_quote('/recent{if $cat.icon_ts.IS_CHILD_DATE}_by_child{/if}.png"', '/')
			.'[^>]+'
			.preg_quote('alt="(!)">', '/');
	$source = preg_replace('/'.$re.'/',
		'<span class=albSymbol title="{$cat.icon_ts.TITLE}">{if $cat.icon_ts.IS_CHILD_DATE}'.MODUS_STR_RECENT_CHILD.'{else}'.MODUS_STR_RECENT.'{/if}</span>',
		$source);

	$re = preg_quote('<img title="{$thumbnail.icon_ts.TITLE}" src="', '/')
		.'[^>]+'
		.preg_quote('/recent.png" alt="(!)">', '/');
	$source = preg_replace('/'.$re.'/',
		'<span class=albSymbol title="{$thumbnail.icon_ts.TITLE}">'.MODUS_STR_RECENT.'</span>',
		$source);

	return $source;
}

?>
