<?php

//
// Start output of page
//
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/header.vtp' );
$vtp->setGlobalVar( $handle, 'charset', $lang['charset'] );
$vtp->setGlobalVar( $handle, 'style', './template/'.$user['template'].'/'.$user['template'].'.css');

  // refresh
  if ( isset( $refresh ) && $refresh >0 && isset($url_link))
  {
    $vtp->addSession( $handle, 'refresh' );
    $vtp->setVar( $handle, 'refresh.time', $refresh );
    $url = $url_link.'&amp;slideshow='.$refresh;
    $vtp->setVar( $handle, 'refresh.url', add_session_id( $url ) );
    $vtp->closeSession( $handle, 'refresh' );
  }
  
$vtp->setGlobalVar( $handle, 'title', $title );
$vtp->setVarF( $handle, 'header', './template/'.$user['template'].'/header.htm' );

//
// Generate the page
//

$code = $vtp->Display( $handle, 0 );
echo $code;
?>