<?php
$handle = $vtp->Open( './template/'.$user['template'].'/footer.vtp' );

//------------------------------------------------------------- generation time
$time = get_elapsed_time( $t2, get_moment() );
$vtp->setGlobalVar( $handle, 'time', $time );

$vtp->setGlobalVar( $handle, 'generation_time', $lang['generation_time'] );
$vtp->setGlobalVar( $handle, 'version', $conf['version'] );
$vtp->setGlobalVar( $handle, 'site_url', $conf['site_url'] );
$vtp->setVarF( $handle, 'footer', './template/'.$user['template'].'/footer.htm' );

//
// Generate the page
//

$code = $vtp->Display( $handle, 0 );
echo $code;
?>