<?php
$template->set_filenames(array('tail'=>'footer.tpl'));

//------------------------------------------------------------- generation time

$time = get_elapsed_time( $t2, get_moment() );

$template->assign_vars(array(
	'L_GEN_TIME' => $lang['generation_time'],
	'S_TIME' =>  $time, 
	'S_VERSION' => $conf['version'],
	'U_SITE' => add_session_id( $conf['site_url'] )
	)
	);
	
if (DEBUG)
{
	$template->assign_block_vars('debug', array());
}

//
// Generate the page
//

$template->pparse('tail');
?>