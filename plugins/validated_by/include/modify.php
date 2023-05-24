<?php

// security
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// Add a prefilter
add_event_handler('loc_begin_admin', 'VB_set_prefilter_modify', 50 );

// Send data to database when submit button is clicked
add_event_handler('loc_begin_admin_page', 'VB_modify_submit', 45 );

// Change the variables used by the function that changes the template
add_event_handler('loc_begin_admin_page', 'VB_add_modify_vars_to_template');


function VB_set_prefilter_modify()
{
	global $template;
	$template->set_prefilter('picture_modify', 'VB_modify');
}


function VB_add_modify_vars_to_template()
{
	if (isset($_GET['page']) and 'photo' == $_GET['page'] and isset($_GET['image_id']))
	{
		global $template;

		//load_language('plugin.lang', dirname(__FILE__).'/');
		
		// Get the current validator
		$image_id = $_GET['image_id'];
		$query = sprintf(
			'SELECT `vb_name`
			FROM %s
			WHERE `image_id` = %d
			;',
			VB_NAMES, $image_id);
		$result = pwg_query($query);
        $name_vb = ''; // Default is ''
        $result = pwg_db_fetch_assoc($result);
		if (isset($result))  // if $result not empty
		{
			$name_vb = $result['vb_name'];  // $name_vb : the name of validator fetched from database
		}
        $template->assign('name_vb', $name_vb);  // replace the value of $name_vb in VB_modify
	}
}


function VB_modify($content)
{
	$search = "#<strong>{'Creation date'#"; // Not ideal, but ok for now :) edit by adam : and im just copying it XD

	// We use the <tr> from the Creation date, and give them a new <tr>
	$replacement = '<strong>{\'Validated By\'|@translate}</strong>
		<br>
			<input type="text" class="large" id="vb_name" name="vb_name" value="{$name_vb}">
		</p>
	
	</p>
  <p>
		<strong>{\'Creation date\'';

    return preg_replace($search, $replacement, $content);
}


function VB_modify_submit()
{
  if (isset($_GET['page']) and 'photo' == $_GET['page'] and isset($_GET['image_id']))
	{
		if (isset($_POST['submit']))
		{
			// The data from the submit
			$image_id = $_GET['image_id'];
			$vb_name = $_POST['vb_name'];

			// Delete the Validator if it allready exists
			$query = sprintf(
				'DELETE
				FROM %s
				WHERE `image_id` = %d
				;',
				VB_NAMES, $image_id);
			pwg_query($query);

			// If you assign no validator, dont put it in the table
			if ($vb_name != '') {
				// Insert the validator
				$query = sprintf(
					'INSERT INTO %s (image_id, vb_name)
					VALUES (%d, "%s")
					;',
					VB_NAMES, $image_id, $vb_name);
				pwg_query($query);
			}
		}
	}
}
?>