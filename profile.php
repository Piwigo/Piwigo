<?php
/***************************************************************************
 *                    profile.php is a part of PhpWebGallery               *
 *                            -------------------                          *
 *   last update          : Tuesday, July 16, 2002                         *
 *   email                : pierrick@z0rglub.com                           *
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
$infos = array( 'nb_image_line', 'nb_line_page', 'theme', 'language',
                'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
                'short_period', 'long_period', 'template', 'mail_address' );
// mise à jour dans la base de données des valeurs
// des paramètres pour l'utilisateur courant
//    - on teste si chacune des variables est passée en argument à la page
//    - ce qui signifie que l'on doit venir de la page de personnalisation
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $i = 0;
  if ( $_POST['maxwidth'] != '' )
  {
    if ( !ereg( "^[0-9]{2,}$", $_POST['maxwidth'] )
         || $_POST['maxwidth'] < 50 )
    {
      $error[$i++] = $lang['err_maxwidth'];
    }
  }
  if ( $_POST['maxheight'] != '' )
  {
    if ( !ereg( "^[0-9]{2,}$", $_POST['maxheight'] )
         || $_POST['maxheight'] < 50 )
    {
      $error[$i++] = $lang['err_maxheight'];
    }
  }
  // les période doivent être des entiers, il représentent des nombres de jours
  if ( !ereg( "^[0-9]*$", $_POST['short_period'] )
       || !ereg("^[0-9]*$", $_POST['long_period'] ) )
  {
    $error[$i++] = $lang['err_periods'];
  }
  else
  {
    // la période longue doit être supérieure à la période courte
    if ( $_POST['long_period'] <= $_POST['short_period']
         || $_POST['short_period'] <= 0 )
    {
      $error[$i++] = $lang['err_periods_2'];
    }
  }
  // le mail doit être conforme à qqch du type : nom@serveur.com
  if( $_POST['mail_address'] != ""
      && !ereg( "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
                $_POST['mail_address'] ) )
  {
    $error[$i++] = $lang['reg_err_mail_address'];
  }
  if ( $_POST['use_new_pwd'] == 1 )
  {
    // on vérifie que le password rentré correspond bien
    // à la confirmation faite par l'utilisateur
    if ( $_POST['password'] != $_POST['passwordConf'] )
    {
      $error[$i++] = $lang['reg_err_pass'];
    }
  }

  if ( sizeof( $error ) == 0 )
  {
    $tab_theme = explode( ' - ', $_POST['theme'] );
    $_POST['theme'] = $tab_theme[0].'/'.$tab_theme[1];

    $query = 'update '.$prefixeTable.'users';
    $query.= ' set';
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      if ( $i > 0 )
      {
        $query.= ',';
      }
      else
      {
        $query.= ' ';
      }
      $query.= $infos[$i];
      $query.= ' = ';
      if ( $_POST[$infos[$i]] == '' )
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= "'".$_POST[$infos[$i]]."'";
      }
    }
    $query.= ' where id = '.$user['id'];
    $query.= ';';
    mysql_query( $query );

    if ( $_POST['use_new_pwd'] == 1 )
    {
      $query = 'update '.$prefixeTable.'users';
      $query.= " set password = '".md5( $_POST['password'] )."'";
      $query.= ' where id = '.$user['id'];
      $query.= ';';
      mysql_query( $query );
      echo '<br />'.$query;
    }
    // redirection
    $url = 'category.php?cat='.$page['cat'].'&expand='.$_GET['expand'];
    if ( $page['cat'] == 'search' )
    {
      $url.= '&search='.$_GET['search'];
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
// language
$vtp->setGlobalVar( $handle, 'customize_page_title',
                    $lang['customize_page_title'] );
$vtp->setGlobalVar( $handle, 'customize_title',  $lang['customize_title'] );
$vtp->setGlobalVar( $handle, 'password',         $lang['password'] );
$vtp->setGlobalVar( $handle, 'new',              $lang['new'] );
$vtp->setGlobalVar( $handle, 'reg_confirm',      $lang['reg_confirm'] );
$vtp->setGlobalVar( $handle, 'submit',           $lang['submit'] );
// user
$vtp->setGlobalVar( $handle, 'page_style',       $user['style'] );
// structure
$vtp->setGlobalVar( $handle, 'frame_start',      get_frame_start() );
$vtp->setGlobalVar( $handle, 'frame_begin',      get_frame_begin() );
$vtp->setGlobalVar( $handle, 'frame_end',        get_frame_end() );
//----------------------------------------------------------------- form action
$url = './profile.php?cat='.$page['cat'].'&amp;expand='.$page['expand'];
if ( $page['cat'] == 'search' )
{
  $url.= '&amp;search='.$_GET['search'];
}
$vtp->setGlobalVar( $handle, 'form_action', add_session_id( $url ) );
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $vtp->addSession( $handle, 'errors' );
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $vtp->addSession( $handle, 'li' );
    $vtp->setVar( $handle, 'li.li', $error[$i] );
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
//----------------------------------------------------------------------- theme
if ( in_array( 'theme', $infos ) )
{
  $vtp->addSession( $handle, 'line' );
  $vtp->setVar( $handle, 'line.name', $lang['customize_theme'] );
  $vtp->addSession( $handle, 'select' );
  $vtp->setVar( $handle, 'select.name', 'theme' );
  $option = get_themes( './theme/' );
  for ( $i = 0; $i < sizeof( $option ); $i++ )
  {
    $vtp->addSession( $handle, 'option' );
    $vtp->setVar( $handle, 'option.option', $option[$i] );
    if ( $option[$i] == str_replace( '/', ' - ', $user['theme'] ) )
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
  $vtp->setVar( $handle, 'line.name', $lang['reg_mail_address'] );
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