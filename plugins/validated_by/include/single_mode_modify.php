<?php

// Add event handlers for the prefilter
add_event_handler('loc_end_element_set_unit', 'VB_set_prefilter_batch_single', 55 );

// Send data to database after submit button is clicked
add_event_handler('loc_begin_element_set_unit', 'VB_batch_single_submit', 50 );

// Change the variables used by the function that changes the template
add_event_handler('loc_end_element_set_unit', 'VB_add_batch_single_vars_to_template');

// Add a prefilter to the template
function VB_set_prefilter_batch_single()
{
	global $template;
	$template->set_prefilter('batch_manager_unit', 'VB_batch_single');
}

// Insert the copyright selector to the template
function VB_batch_single($content)
{
	$search = "#<td><strong>{'Creation date'#";

	// We use the <tr> from the Creation date, and give them a new <tr>
	$replacement = '<td><strong>{\'Validated By\'|@translate}</strong></td>
		<td>
			<input type="text" class="large" id="vb_name-{$element.ID}" name="vb_name-{$element.ID}" value="{$VB_names[$element.ID]}">
		</td>
	</tr>
	
	<tr>
		<td><strong>{\'Creation date\'';

  return preg_replace($search, $replacement, $content);
}

// Assign the variables to the Smarty template
function VB_add_batch_single_vars_to_template()
{
	global $template;

	//load_language('plugin.lang', dirname(__FILE__).'/');

	// Get the validator for each element
	$query = sprintf(
		'SELECT `image_id`, `vb_name`
		FROM %s
		;',
		VB_NAMES);
	$result = pwg_query($query);
	
	$VB_names = array();  // array of validators fetched from database
	if (isset($result))
	{
		while ($row = pwg_db_fetch_assoc($result))
		{
			$VB_names[$row['image_id']] = $row['vb_name'];  // eg : $VB_names[2] = 'John Cena';
		}
	}
	
  	// Assign the validator to the template
	$template->assign('VB_names', $VB_names);

}

// Catch the submit and update the validated by table
function VB_batch_single_submit()
{
	if (isset($_POST['submit']))
	{
		// The image id's:
		$collection = explode(',', $_POST['element_ids']);

		// Delete all existing id's of which the validated by is going to be set
		if (count($collection) > 0) {
			$query = sprintf(
				'DELETE
				FROM %s
				WHERE image_id IN (%s)
				;',
				VB_NAMES, implode(',', $collection));
			pwg_query($query);
		}

		// Add all validated by to an array
		$edits = array();
		foreach ($collection as $image_id) {
			// The validated by names
			$vbName = pwg_db_real_escape_string($_POST['vb_name-'.$image_id]);

			// If you assign no validated by, dont put them in the table
			if ($vbName != '') {
				array_push(
					$edits,
					array(
						'image_id' => $image_id,
						'vb_name' => $vbName,
					)
				);
			}
		}

		if (count($edits) > 0) {
			// Insert the array to the database
			mass_inserts(
				VB_NAMES,        		// Table name
				array_keys($edits[0]),  // Columns
				$edits                  // Data
			);
		}
	}
}

?>