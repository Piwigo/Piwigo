<?php
/***************************************************************************
 *                                profile.php                              *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
// customize appearance of the site for a user
//----------------------------------------------------------- personnal include
include_once( './include/init.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
if ( $user['is_the_guest'] )
{
  echo '<div style="text-align:center;">'.$lang['only_members'].'<br />';
  echo '<a href="./identification.php">'.$lang['ident_title'].'</a></div>';
  exit();
}
//-------------------------------------------------------------- initialization
check_cat_id( $_GET['cat'] );
//------------------------------------------------------ update & customization
$infos = array( 'nb_image_line', 'nb_line_page', 'language',
                'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
                'short_period', 'long_period', 'template', 'mail_address' );
// mise à jour dans la base de données des valeurs
// des paramètres pour l'utilisateur courant
//    - on teste si chacune des variables est passée en argument à la page
//    - ce qui signifie que l'on doit venir de la page de personnalisation
$errors = array();
if ( isset( $_POST['submit'] ) )
{
  $int_pattern = '/^\d+$/';
  if ( $_POST['maxwidth'] != ''
       and ( !preg_match( $int_pattern, $_POST['maxwidth'] )
             or $_POST['maxwidth'] < 50 ) )
  {
    array_push( $errors, $lang['err_maxwidth'] );
  }
  if ( $_POST['maxheight']
       and ( !preg_match( $int_pattern, $_POST['maxheight'] )
             or $_POST['maxheight'] < 50 ) )
  {
    array_push( $errors, $lang['err_maxheight'] );
  }
  // periods must be integer values, they represents number of days
  if ( !preg_match( $int_pattern, $_POST['short_period'] )
       or !preg_match( $int_pattern, $_POST['long_period'] ) )
  {
    array_push( $errors, $lang['err_periods'] );
  }
  else
  {
    // long period must be longer than short period
    if ( $_POST['long_period'] <= $_POST['short_period']
         or $_POST['short_period'] <= 0 )
    {
      array_push( $errors, $lang['err_periods_2'] );
    }
  }
  $mail_error = validate_mail_address( $_POST['mail_address'] );
  if ( $mail_error != '' )
  {
    array_push( $errors, $mail_error );
  }
  if ( $_POST['use_new_pwd'] == 1 )
  {
    // password must be the same as its confirmation
    if ( $_POST['password'] != $_POST['passwordConf'] )
    {
      array_push( $errors, $lang['reg_err_pass'] );
    }
  }

  if ( count( $errors ) == 0 )
  {
    $query = 'UPDATE '.PREFIX_TABLE.'users';
    $query.= ' SET ';
    foreach ( $infos as $i => $info ) {
      if ( $i > 0 ) $query.= ',';
      $query.= $info;
      $query.= ' = ';
      if ( $_POST[$info] == '' ) $query.= 'NULL';
      else                       $query.= "'".$_POST[$info]."'";
    }
    $query.= ' WHERE id = '.$user['id'];
    $query.= ';';
    mysql_query( $query );

    if ( $_POST['use_new_pwd'] == 1 )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'users';
      $query.= " SET password = '".md5( $_POST['password'] )."'";
      $query.= ' WHERE id = '.$user['id'];
      $query.= ';';
      mysql_query( $query );
    }
    // redirection
    $url = 'category.php?cat='.$page['cat'].'&expand='.$_GET['expand'];
    if ( $page['cat'] == 'search' )
    {
      $url.= '&search='.$_GET['search'].'&mode='.$_GET['mode'];
    }
    $url = add_session_id( $url, true );
    header( 'Request-URI: '.$url );  
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
}
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/profile.vtp' );
initialize_template();
$tpl = array( 'customize_page_title','customize_title','password','new',
              'reg_confirm','submit' );
templatize_array( $tpl, 'lang', $handle );
//----------------------------------------------------------------- form action
$url = './profile.php?cat='.$page['cat'].'&amp;expand='.$page['expand'];
if ( $page['cat'] == 'search' )
{
  $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
}
$vtp->setGlobalVar( $handle, 'form_action', add_session_id( $url ) );
//-------------------------------------------------------------- errors display
if ( count( $errors ) != 0 )
{
  $vtp->addSession( $handle, 'errors' );
  foreach ( $errors as $error ) {
    $vtp->addSession( $handle, 'li' );
    $vtp->setVar( $handle, 'li.li', $error );
    $vtp->closeSession( $handle, 'li' );
  }
  $vtp->closeSession( $handle, 'errors' );
}
//---------------------------------------------------- number of images per row
if ( in_array( 'nb_image_line', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_nb_image_per_row'] );
  $vtp->addSession( $handle, 'select' );
  $vtp->setVar( $handle, 'select.name', 'nb_image_line' );
  for ( $i = 0; $i < sizeof( $conf['nb_image_row'] ); $i++ )
  {
    $vtp->addSession( $handle, 'option' );
    $vtp->setVar( $handle, 'option.option', $conf['nb_image_row'][$i] );
    if ( $conf['nb_image_row'][$i] == $user['nb_image_line'] )
    {
      $vtp->setVar( $handle, 'option.selected', ' selected="selected"' );
    }
    $vtp->closeSession( $handle, 'option' );
  }
  $vtp->closeSession( $handle, 'select' );
  $vtp->closeSession( $handle, 'line' );
}
//------------------------------------------------------ number of row per page
if ( in_array( 'nb_line_page', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_nb_row_per_page'] );
  $vtp->addSession( $handle, 'select' );
  $vtp->setVar( $handle, 'select.name', 'nb_line_page' );
  for ( $i = 0; $i < sizeof( $conf['nb_row_page'] ); $i++ )
  {
    $vtp->addSession( $handle, 'option' );
    $vtp->setVar( $handle, 'option.option', $conf['nb_row_page'][$i] );
    if ( $conf['nb_row_page'][$i] == $user['nb_line_page'] )
    {
      $vtp->setVar( $handle, 'option.selected', ' selected="selected"' );
    }
    $vtp->closeSession( $handle, 'option' );
  }
  $vtp->closeSession( $handle, 'select' );
  $vtp->closeSession( $handle, 'line' );
}
//-------------------------------------------------------------------- template
if ( in_array( 'template', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_template'] );
  $vtp->addSession( $handle, 'select' );
  $vtp->setVar( $handle, 'select.name', 'template' );
  $option = get_dirs( './template/' );
  for ( $i = 0; $i < sizeof( $option ); $i++ )
  {
    $vtp->addSession( $handle, 'option' );
    $vtp->setVar( $handle, 'option.option', $option[$i] );
    if ( $option[$i] == $user['template'] )
    {
      $vtp->setVar( $handle, 'option.selected', ' selected="selected"' );
    }
    $vtp->closeSession( $handle, 'option' );
  }
  $vtp->closeSession( $handle, 'select' );
  $vtp->closeSession( $handle, 'line' );
}
//-------------------------------------------------------------------- language
if ( in_array( 'language', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_language'] );
  $vtp->addSession( $handle, 'select' );
  $vtp->setVar( $handle, 'select.name', 'language' );
  $option = get_languages( './language/' );
  for ( $i = 0; $i < sizeof( $option ); $i++ )
  {
    $vtp->addSession( $handle, 'option' );
    $vtp->setVar( $handle, 'option.option', $option[$i] );
    if( $option[$i] == $user['language'] )
    {
      $vtp->setVar( $handle, 'option.selected', ' selected="selected"' );
    }
    $vtp->closeSession( $handle, 'option' );
  }
  $vtp->closeSession( $handle, 'select' );
  $vtp->closeSession( $handle, 'line' );
}
//---------------------------------------------------------------- short period
if ( in_array( 'short_period', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_short_period'] );
  $vtp->addSession( $handle, 'text' );
  $vtp->setVar( $handle, 'text.name', 'short_period' );
  $vtp->setVar( $handle, 'text.value', $user['short_period'] );
  $vtp->closeSession( $handle, 'text' );
  $vtp->closeSession( $handle, 'line' );
}
//----------------------------------------------------------------- long period
if ( in_array( 'long_period', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_long_period'] );
  $vtp->addSession( $handle, 'text' );
  $vtp->setVar( $handle, 'text.name', 'long_period' );
  $vtp->setVar( $handle, 'text.value', $user['long_period'] );
  $vtp->closeSession( $handle, 'text' );
  $vtp->closeSession( $handle, 'line' );
}
//--------------------------------------------------------- max displayed width
if ( in_array( 'maxwidth', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['maxwidth'] );
  $vtp->addSession( $handle, 'text' );
  $vtp->setVar( $handle, 'text.name', 'maxwidth' );
  $vtp->setVar( $handle, 'text.value', $user['maxwidth'] );
  $vtp->closeSession( $handle, 'text' );
  $vtp->closeSession( $handle, 'line' );
}
//-------------------------------------------------------- max displayed height
if ( in_array( 'maxheight', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['maxheight'] );
  $vtp->addSession( $handle, 'text' );
  $vtp->setVar( $handle, 'text.name', 'maxheight' );
  $vtp->setVar( $handle, 'text.value', $user['maxheight'] );
  $vtp->closeSession( $handle, 'text' );
  $vtp->closeSession( $handle, 'line' );
}
//---------------------------------------------------------------- mail address
if ( in_array( 'mail_address', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['mail_address'] );
  $vtp->addSession( $handle, 'text' );
  $vtp->setVar( $handle, 'text.name', 'mail_address' );
  $vtp->setVar( $handle, 'text.value', $user['mail_address'] );
  $vtp->closeSession( $handle, 'text' );
  $vtp->closeSession( $handle, 'line' );
}
//----------------------------------------------------- expand all categories ?
if ( in_array( 'expand', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_expand'] );
  $vtp->addSession( $handle, 'group' );
  $vtp->addSession( $handle, 'radio' );
  $vtp->setVar( $handle, 'radio.name', 'expand' );
  $vtp->setVar( $handle, 'radio.value', 'true' );
  $checked = '';
  if ( $user['expand'] )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $handle, 'radio.checked', $checked );
  $vtp->setVar( $handle, 'radio.option', $lang['yes'] );
  $vtp->closeSession( $handle, 'radio' );
  $vtp->addSession( $handle, 'radio' );
  $vtp->setVar( $handle, 'radio.name', 'expand' );
  $vtp->setVar( $handle, 'radio.value', 'false' );
  $checked = '';
  if ( !$user['expand'] )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $handle, 'radio.checked', $checked );
  $vtp->setVar( $handle, 'radio.option', $lang['no'] );
  $vtp->closeSession( $handle, 'radio' );
  $vtp->closeSession( $handle, 'group' );
  $vtp->closeSession( $handle, 'line' );
}
//---------------------------------- show number of comments on thumbnails page
if ( in_array( 'show_nb_comments', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_show_nb_comments'] );
  $vtp->addSession( $handle, 'group' );
  $vtp->addSession( $handle, 'radio' );
  $vtp->setVar( $handle, 'radio.name', 'show_nb_comments' );
  $vtp->setVar( $handle, 'radio.value', 'true' );
  $checked = '';
  if ( $user['show_nb_comments'] )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $handle, 'radio.checked', $checked );
  $vtp->setVar( $handle, 'radio.option', $lang['yes'] );
  $vtp->closeSession( $handle, 'radio' );
  $vtp->addSession( $handle, 'radio' );
  $vtp->setVar( $handle, 'radio.name', 'show_nb_comments' );
  $vtp->setVar( $handle, 'radio.value', 'false' );
  $checked = '';
  if ( !$user['show_nb_comments'] )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $handle, 'radio.checked', $checked );
  $vtp->setVar( $handle, 'radio.option', $lang['no'] );
  $vtp->closeSession( $handle, 'radio' );
  $vtp->closeSession( $handle, 'group' );
  $vtp->closeSession( $handle, 'line' );
}
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>