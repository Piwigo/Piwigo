{html_style}
.browsePath ul li span a:before, .browsePath ul li.last:before
{ldelim}
  content: "{$LEVEL_SEPARATOR}";
}
{/html_style}
{footer_script}
  var p_main_menu = "{$elegant.p_main_menu}", p_pict_descr = "{$elegant.p_pict_descr}", p_pict_comment = "{$elegant.p_pict_comment}";
  jQuery(document).ready(function() {
      jQuery(".browsePath").jBreadCrumb();
  });
{/footer_script}
{if $BODY_ID=='thePicturePage'}
	{combine_script id='elegant.scripts_pp' load='footer' require='jquery' path='themes/elegant/scripts_pp.js'}
{else}
	{combine_script id='elegant.scripts' load='footer' require='jquery' path='themes/elegant/scripts.js'}
{/if}
{combine_script id='jquery.jBreadCrumb' load='footer' require='jquery' path='themes/elegant/js/jquery.jBreadCrumb.js'}


<!--[if lt IE 8]>
		<link rel="stylesheet" type="text/css" href="{$ROOT_URL}themes/elegant/fix-ie7.css">
	<![endif]-->
