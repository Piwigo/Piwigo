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

function register_user( $login, $password, $password_conf,
                        $mail_address, $status = 'guest' )
{
  global $lang;

  $error = array();
  $i = 0;
  // login must not
  //      1. be empty
  //      2. start ou end with space character
  //      3. include ' or " characters
  //      4. be already used
  if ( $login == '' )            $error[$i++] = $lang['reg_err_login1'];
  if ( ereg( "^.* $", $login) )  $error[$i++] = $lang['reg_err_login2'];
  if ( ereg( "^ .*$", $login ) ) $error[$i++] = $lang['reg_err_login3'];

  if ( ereg( "'", $login ) or ereg( "\"", $login ) )
    $error[$i++] = $lang['reg_err_login4'];
  else
  {
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'users';
    $query.= " WHERE username = '".$login."'";
    $query.= ';';
    $result = mysql_query( $query );
    if ( mysql_num_rows($result) > 0 ) $error[$i++] = $lang['reg_err_login5'];
  }
  // given password must be the same as the confirmation
  if ( $password != $password_conf ) $error[$i++] = $lang['reg_err_pass'];

  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' ) $error[$i++] = $error_mail_address;

  // if no error until here, registration of the user
  if ( sizeof( $error ) == 0 )
  {
    // 1. retrieving default values, the ones of the user "guest"
    $infos = array( 'nb_image_line', 'nb_line_page', 'language',
                    'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
                    'short_period', 'long_period', 'template',
                    'forbidden_categories' );
    $query = 'SELECT ';
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      if ( $i > 0 ) $query.= ',';
      $query.= $infos[$i];
    }
    $query.= ' FROM '.PREFIX_TABLE.'users';
    $query.= " WHERE username = 'guest'";
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    // 2. adding new user
    $query = 'INSERT INTO '.PREFIX_TABLE.'users';
    $query.= ' (';
    $query.= ' username,password,mail_address,status';
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      $query.= ','.$infos[$i];
    }
    $query.= ') values (';
    $query.= " '".$login."'";
    $query.= ",'".md5( $password )."'";
    if ( $mail_address != '' ) $query.= ",'".$mail_address."'";
    else                       $query.= ',NULL';
    $query.= ",'".$status."'";
    for ( $i = 0; $i < sizeof( $infos ); $i++ )
    {
      $query.= ',';
      if ( $row[$infos[$i]] == '' ) $query.= 'NULL';
      else                          $query.= "'".$row[$infos[$i]]."'";
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
    // 5. associate new user to the same groups that the guest
    $query = 'SELECT group_id';
    $query.= ' FROM '.PREFIX_TABLE.'user_group AS ug';
    $query.= ',     '.PREFIX_TABLE.'users      AS u';
    $query.= " WHERE u.username = 'guest'";
    $query.= ' AND ug.user_id = u.id';
    $query.= ';';
    $result = mysql_query( $query );
    while( $row = mysql_fetch_array( $result ) )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'user_group';
      $query.= ' (user_id,group_id) VALUES';
      $query.= ' ('.$user_id.','.$row['group_id'].')';
      $query.= ';';
      mysql_query ( $query );
    }
    // 6. has the same categories informations than guest
    $query = 'SELECT category_id,date_last,nb_sub_categories';
    $query.= ' FROM '.PREFIX_TABLE.'user_category AS uc';
    $query.= ',     '.PREFIX_TABLE.'users         AS u';
    $query.= " WHERE u.username = 'guest'";
    $query.= ' AND uc.user_id = u.id';
    $query.= ';';
    $result = mysql_query( $query );
    while( $row = mysql_fetch_array( $result ) )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'user_category';
      $query.= ' (user_id,category_id,date_last,nb_sub_categories) VALUES';
      $query.= ' ('.$user_id.','.$row['category_id'];
      $query.= ",'".$row['date_last']."',".$row['nb_sub_categories'].')';
      $query.= ';';
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

  if ( $user['is_the_guest'])
  {
  if ( $conf['access'] == 'restricted' || (isset($page['cat']) && $page['cat'] == 'fav' ) )
  {
    echo '<div style="text-align:center;">'.$lang['only_members'].'<br />';
    echo '<a href="./identification.php">'.$lang['ident_title'].'</a></div>';
    exit();
  }
  }
}
?>