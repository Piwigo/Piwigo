<?php

// security
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// Add an event handler for a prefilter
add_event_handler('loc_begin_picture', 'vb_set_prefilter_add_to_pic_info', 55 );

// Change the variables used by the function that changes the template
add_event_handler('loc_begin_picture', 'vb_add_image_vars_to_template');

// Add the prefilter to the template
function vb_set_prefilter_add_to_pic_info()
{
	global $template;
	$template->set_prefilter('picture', 'vb_add_to_pic_info');
}

// Insert the template for the validated by display
function vb_add_to_pic_info($content)
{
	// Add the information after the author - so before the createdate
	$search = '#<div id="datepost" class="imageInfo">#';
	
	$replacement = '
	<div id="name_vb" class="imageInfo">
		<dt>{\'Validated By\'|@translate}</dt>
		<dd>
{if $NAME_VB}
			{$NAME_VB}
{else}
      {\'N/A\'|@translate}
{/if}
    </dd>
	</div>
    <div id="datepost" class="imageInfo">';

	return preg_replace($search, $replacement, $content, 1);
}

// Assign values to the variables in the template
function vb_add_image_vars_to_template()
{
	global $page, $template, $prefixeTable;

	// Show block only on the photo page
	if ( !empty($page['image_id']) )
	{
		// Get the validator name
		$query = sprintf('
		  SELECT vb_name
		  FROM %s 
		  WHERE image_id = %d
		;',
		VB_NAMES, $page['image_id']);
		$result = pwg_query($query);
		$row = pwg_db_fetch_assoc($result);
		$name = '';
		if (isset($row))
        {
            $name = $row['vb_name'];
        }
				
		// Sending data to the template
        $template->assign('NAME_VB', $name);
	}
}

?>