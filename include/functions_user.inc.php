<?php
/***************************************************************************
 *                           functions_user.inc.php                        *
 *                            --------------------                         *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
function validate_mail_address( $mail_address )
{
  global $lang;

  $output = '';
  // le mail doit être conforme à qqch du type : nom@serveur.com
  if ( $mail_address != ''
       and !ereg( "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
                 $mail_address ) )
  {
    $output = $lang['reg_err_mail_address'];
  }

  return $output;
}

function register_user( $login, $password, $password_conf,
                        $mail_address, $status = 'visiteur' )
{
  global $prefixeTable;

  $error = array();
  $i = 0;
  // le login ne doit pas
  //      1. être vide
  //      2. commencer ou se terminer par un espace
  //      3. comporter les caractères ' ou "
  //      4. être déjà utilisé
  if ( $login == '' )
  {
    $error[$i++] = $lang['reg_err_login1'];
  }
  if ( ereg( "^.* $", $login) )
  {
    $error[$i++] = $lang['reg_err_login2'];
  }
  if ( ereg( "^ .*$", $login ) )
  {
    $error[$i++] = $lang['reg_err_login3'];
  }
  if ( ereg( "'", $login ) or ereg( "\"", $login ) )
  {
    $error[$i++] = $lang['reg_err_login4'];
  }
  else
  {
    $query = 'select id';
    $query.= ' from '.$prefixeTable.'users';
    $query.= " where pseudo = '".$login."';";
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) > 0 )
    {
      $error[$i++] = $lang['reg_err_login5'];
    }
  }
  // on vérifie que le password rentré correspond bien
  // à la confirmation faite par l'utilisateur
  if ( $password != $password_conf )
  {
    $error[$i++] = $lang['reg_err_pass'];
  }

  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' )
  {
    $error[$i++] = $error_mail_address;
  }
  
  // on enregistre le nouvel utilisateur si aucune
  //erreur détectée dans les paramètres
  if ( sizeof( $error ) == 0 )
  {
    // 1.récupération des valeurs par défaut de l'application
    $infos = array( 'nb_image_line', 'nb_line_page', 'theme', 'language',
                    'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
                    'short_period', 'long_period', 'template' );
    $query = 'select';
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
    }
    $query.= ' from '.$prefixeTable.'users';
    $query.= " where pseudo = 'visiteur';";
    $row = mysql_fetch_array( mysql_query( $query ) );
    // 2.ajout du nouvel utilisateur
    $query = 'insert into '.$prefixeTable.'users';
    $query.= ' (';
    $query.= ' pseudo,password,mail_address,status';
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      $query.= ','.$infos[$i];
    }
    $query.= ' values (';
    $query.= " '".$login."'";
    $query.= ",'".md5( $password )."'";
    if ( $mail_address != '' )
    {
      $query.= ",'".$mail_address."'";
    }
    else
    {
      $query.= ',NULL';
    }
    $query.= ",'".$status."'";
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      $query.= ','.$row[$infos[$i]];
    }
    $query.= ');';
    mysql_query( $query );
    // 3. récupérer l'identifiant de l'utilisateur nouvellement créé
    $query = 'select id';
    $query.= ' from '.$prefixeTable.'users';
    $query.= " where pseudo = '".$login."';";
    $row = mysql_fetch_array( mysql_query( $query ) );
    $user_id = $row['id'];
    // 4.ajouter les restrictions au nouvel utilisateur,
    //   les mêmes que celles de l'utilisateur par défaut
    $query = 'select cat_id';
    $query.= ' from '.$prefixeTable.'restrictions as r';
    $query.=      ','.$prefixeTable.'users as u ';
    $query.= ' where u.id = r.user_id';
    $query.= " and u.pseudo = 'visiteur';";
    $result = mysql_query( $query );
    while( $row = mysql_fetch_array( $result ) )
    {
      $query = 'insert into '.$prefixeTable.'restrictions';
      $query.= ' (user_id,cat_id) values';
      $query.= ' ('.$user_id.','.$row['cat_id'].');';
      mysql_query ( $query );
    }
  }
  return $error;
}

function update_user( $user_id, $mail_address, $status,
                      $use_new_password = false, $password = '' )
{
  global $prefixeTable;

  $error = array();
  $i = 0;
  
  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' )
  {
    $error[$i++] = $error_mail_address;
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'update '.$prefixeTable.'users';
    $query.= " set status = '".$status."'";
    if ( $use_new_password )
    {
      $query.= ", password = '".md5( $password )."'";
    }
    $query.= ', mail_address = ';
    if ( $mail_address != '' )
    {
      $query.= "'".$mail_address."'";
    }
    else
    {
      $query.= 'NULL';
    }
    $query.= ' where id = '.$user_id;
    $query.= ';';
    mysql_query( $query );
  }
  return $error;
}

function check_login_authorization()
{
  global $user,$lang,$conf,$page;
  if ( $user['is_the_guest']
       and ( $conf['acces'] == 'restreint' or $page['cat'] == 'fav' ) )
  {
    echo '<div style="text-align:center;">'.$lang['only_members'].'<br />';
    echo '<a href="./identification.php">'.$lang['ident_title'].'</a></div>';
    exit();
  }
}
        
// The function get_restrictions returns an array with the ids of the
// restricted categories for the user.
// If the $check_invisible parameter is set to true, invisible categories
// are added to the restricted one in the array.
function get_restrictions( $user_id, $user_status, $check_invisible )
{
  global $prefixeTable;
                
  // 1. getting the ids of the restricted categories
  $query = "select cat_id";
  $query.= " from $prefixeTable"."restrictions";
  $query.= " where user_id = $user_id;";
  $result = mysql_query( $query );
  $i = 0;
  $restriction = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    $restriction[$i++] = $row['cat_id'];
  }
  if ( $check_invisible )
  {
    // 2. adding to the restricted categories, the invisible ones
    if ( $user_status != "admin" )
    {
      $query = 'select id';
      $query.= ' from '.$prefixeTable.'categories';
      $query.= " where status='invisible';";
      $result = mysql_query( $query );
      while ( $row = mysql_fetch_array( $result ) )
      {
        $restriction[$i++] = $row['id'];
      }
    }
  }
  return $restriction;
}

// The get_all_restrictions function returns an array with all the
// categories id which are restricted for the user. Including the
// sub-categories and invisible categories
function get_all_restrictions( $user_id, $user_status )
{
  global $prefixeTable;
                
  $restricted_cat = get_restrictions( $user_id, $user_status, true );
  $i = sizeof( $restricted_cat );
  for ( $k = 0; $k < sizeof( $restricted_cat ); $k++ )
  {
    $sub_restricted_cat = get_subcats_id( $restricted_cat[$k] );
    for ( $j = 0; $j < sizeof( $sub_restricted_cat ); $j++ )
    {
      $restricted_cat[$i++] = $sub_restricted_cat[$j];
    }
  }
  return $restricted_cat;
}

// The function is_user_allowed returns :
//      - 0 : if the category is allowed with this $restrictions array
//      - 1 : if this category is not allowed
//      - 2 : if an uppercat category is not allowed
function is_user_allowed( $category_id, $restrictions )
{
  global $user,$prefixeTable;
                
  $lowest_category_id = $category_id;
                
  $is_root = false;
  while ( !$is_root and !in_array( $category_id, $restrictions ) )
  {
    $query = "select id_uppercat";
    $query.= " from $prefixeTable"."categories";
    $query.= " where id = $category_id;";
    $row = mysql_fetch_array( mysql_query( $query ) );
    if ( $row['id_uppercat'] == "" )
    {
      $is_root = true;
    }
    $category_id = $row['id_uppercat'];
  }
                
  if ( in_array( $lowest_category_id, $restrictions ) )
  {
    return 1;
  }
  if ( in_array( $category_id, $restrictions ) )
  {
    return 2;
  }
  // this user is allowed to go in this category
  return 0;
}
?>