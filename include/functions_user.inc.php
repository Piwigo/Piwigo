<?php
/***************************************************************************
 *                           functions_user.inc.php                        *
 *                            --------------------                         *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

// validate_mail_address verifies whether the given mail address has the
// right format. ie someone@domain.com "someone" can contain ".", "-" or
// even "_". Exactly as "domain". The extension doesn't have to be
// "com". The mail address can also be empty.
// If the mail address doesn't correspond, an error message is returned.
function validate_mail_address( $mail_address )
{
  global $lang;

  if ( $mail_address == '' )
  {
    return '';
  }
  $regex = '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.[a-z]+$/';
  if ( !preg_match( $regex, $mail_address ) )
  {
    return $lang['reg_err_mail_address'];
  }
}

function register_user(
  $login, $password, $password_conf, $mail_address, $status = 'guest' )
{
  global $lang;

  $error = array();
  $i = 0;
  // login must not
  //      1. be empty
  //      2. start ou end with space character
  //      3. include ' or " characters
  //      4. be already used
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
    $query.= ' from '.PREFIX_TABLE.'users';
    $query.= " where username = '".$login."';";
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) > 0 )
    {
      $error[$i++] = $lang['reg_err_login5'];
    }
  }
  // given password must be the same as the confirmation
  if ( $password != $password_conf )
  {
    $error[$i++] = $lang['reg_err_pass'];
  }

  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' )
  {
    $error[$i++] = $error_mail_address;
  }

  // if no error until here, registration of the user
  if ( sizeof( $error ) == 0 )
  {
    // 1. retrieving default values, the ones of the user "guest"
    $infos = array( 'nb_image_line', 'nb_line_page', 'language',
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
    $query.= ' from '.PREFIX_TABLE.'users';
    $query.= " where username = 'guest';";
    $row = mysql_fetch_array( mysql_query( $query ) );
    // 2. adding new user
    $query = 'insert into '.PREFIX_TABLE.'users';
    $query.= ' (';
    $query.= ' username,password,mail_address,status';
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      $query.= ','.$infos[$i];
    }
    $query.= ') values (';
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
      $query.= ',';
      if ( $row[$infos[$i]] == '' )
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= "'".$row[$infos[$i]]."'";
      }
    }
    $query.= ');';
    mysql_query( $query );
    // 3. retrieving the id of the newly created user
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'users';
    $query.= " WHERE username = '".$login."';";
    $row = mysql_fetch_array( mysql_query( $query ) );
    $user_id = $row['id'];
    // 4. adding access to the new user, the same as the user "guest"
    $query = 'SELECT cat_id';
    $query.= ' FROM '.PREFIX_TABLE.'user_access as ua';
    $query.=      ','.PREFIX_TABLE.'users as u ';
    $query.= ' where u.id = ua.user_id';
    $query.= " and u.username = 'guest';";
    $result = mysql_query( $query );
    while( $row = mysql_fetch_array( $result ) )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'user_access';
      $query.= ' (user_id,cat_id) VALUES';
      $query.= ' ('.$user_id.','.$row['cat_id'].');';
      mysql_query ( $query );
    }
  }
  return $error;
}

function update_user( $user_id, $mail_address, $status,
                      $use_new_password = false, $password = '' )
{
  $error = array();
  $i = 0;
  
  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' )
  {
    $error[$i++] = $error_mail_address;
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'UPDATE '.PREFIX_TABLE.'users';
    $query.= " SET status = '".$status."'";
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
    $query.= ' WHERE id = '.$user_id;
    $query.= ';';
    mysql_query( $query );
  }
  return $error;
}

function check_login_authorization()
{
  global $user,$lang,$conf,$page;

  if ( $user['is_the_guest']
       and ( $conf['access'] == 'restricted' or $page['cat'] == 'fav' ) )
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
function get_restrictions( $user_id, $user_status,
                           $check_invisible, $use_groups = true )
{
  // 1. retrieving ids of private categories
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = mysql_query( $query );
  $privates = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $privates, $row['id'] );
  }
  // 2. retrieving all authorized categories for the user
  $authorized = array();
  // 2.1. retrieving authorized categories thanks to personnal user
  //      authorization
  $query = 'SELECT cat_id';
  $query.= ' FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $authorized, $row['cat_id'] );
  }
  // 2.2. retrieving authorized categories thanks to group authorization to
  //      which the user is a member
  if ( $use_groups )
  {
    $query = 'SELECT ga.cat_id';
    $query.= ' FROM '.PREFIX_TABLE.'user_group as ug';
    $query.= ', '.PREFIX_TABLE.'group_access as ga';
    $query.= ' WHERE ug.group_id = ga.group_id';
    $query.= ' AND ug.user_id = '.$user_id;
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      array_push( $authorized, $row['cat_id'] );
    }
    $authorized = array_unique( $authorized );
  }

  $forbidden = array();
  foreach ( $privates as $private ) {
    if ( !in_array( $private, $authorized ) )
    {
      array_push( $forbidden, $private );
    }
  }

  if ( $check_invisible )
  {
    // 3. adding to the restricted categories, the invisible ones
    if ( $user_status != 'admin' )
    {
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'categories';
      $query.= " WHERE visible = 'false';";
      $result = mysql_query( $query );
      while ( $row = mysql_fetch_array( $result ) )
      {
        array_push( $forbidden, $row['id'] );
      }
    }
  }
  return array_unique( $forbidden );
}

// The get_all_restrictions function returns an array with all the
// categories id which are restricted for the user. Including the
// sub-categories and invisible categories
function get_all_restrictions( $user_id, $user_status )
{
  $restricted_cats = get_restrictions( $user_id, $user_status, true );
  foreach ( $restricted_cats as $restricted_cat ) {
    $sub_restricted_cats = get_subcats_id( $restricted_cat );
    foreach ( $sub_restricted_cats as $sub_restricted_cat ) {
      array_push( $restricted_cats, $sub_restricted_cat );
    }
  }
  return $restricted_cats;
}

// The function is_user_allowed returns :
//      - 0 : if the category is allowed with this $restrictions array
//      - 1 : if this category is not allowed
//      - 2 : if an uppercat category is not allowed
function is_user_allowed( $category_id, $restrictions )
{
  $lowest_category_id = $category_id;

  $is_root = false;
  while ( !$is_root and !in_array( $category_id, $restrictions ) )
  {
    $query = 'SELECT id_uppercat';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    if ( $row['id_uppercat'] == '' ) $is_root = true;
    $category_id = $row['id_uppercat'];
  }

  if ( in_array( $lowest_category_id, $restrictions ) ) return 1;
  if ( in_array( $category_id,        $restrictions ) ) return 2;
  // this user is allowed to go in this category
  return 0;
}
?>