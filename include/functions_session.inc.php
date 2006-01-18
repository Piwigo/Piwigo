<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if (isset($conf['session_save_handler']) 
  and ($conf['session_save_handler'] == 'db')) 
{
  session_set_save_handler('pwg_session_open', 
    'pwg_session_close',
    'pwg_session_read',
    'pwg_session_write',
    'pwg_session_destroy',
    'pwg_session_gc'
  );
}
if (isset($conf['session_use_cookies'])) 
{ 
  ini_set('session.use_cookies', $conf['session_use_cookies']);
}
if (isset($conf['session_use_only_cookies']))
{
  ini_set('session.use_only_cookies', $conf['session_use_only_cookies']);
}
if (isset($conf['session_use_trans_sid']))
{
  ini_set('session.use_trans_sid', intval($conf['session_use_trans_sid']));
}
if (isset($conf['session_name']))
{
  ini_set('session.name', $conf['session_name']);
}

function pwg_session_open($path, $name) 
{
  return true;
}

function pwg_session_close() 
{
  pwg_session_gc();
  return true;
}

function pwg_session_read($session_id) 
{
  $query = '
SELECT data FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$session_id.'\'';
  $result = pwg_query($query);
  if ($result) 
  {
    $row = mysql_fetch_assoc($result);
    return $row['data'];
  } 
  else 
  {
    return '';
  }
}

function pwg_session_write($session_id, $data) 
{
  $query = '
SELECT id FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$session_id.'\'';
  $result = pwg_query($query);
  if (mysql_num_rows($result)) 
  {
    $query = '
UPDATE '.SESSIONS_TABLE.' SET expiration = now()
  WHERE id = \''.$session_id.'\'';    
    pwg_query($query);
  } 
  else 
  {
    $query = '
INSERT INTO '.SESSIONS_TABLE.'(id,data,expiration)
  VALUES(\''.$session_id.'\',\''.$data.'\',now())';
    pwg_query($query);    
  }
  return true;
}

function pwg_session_destroy($session_id) 
{
  $query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE id = '.$session_id;
  pwg_query($query);
  return true;
}

function pwg_session_gc() 
{
  global $conf;

  $query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(expiration) > '
  .$conf['session_length'];
  pwg_query($query);
  return true;
}
?>
