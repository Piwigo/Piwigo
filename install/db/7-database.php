<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-09-21 00:04:57 +0200 (mer, 21 sep 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 870 $
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'Anonymous rating';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.PREFIX_TABLE.'rate DROP PRIMARY KEY;'
;
pwg_query($query);

$query ='
ALTER TABLE '.PREFIX_TABLE.'rate ADD COLUMN anonymous_id VARCHAR(45) NOT NULL DEFAULT \'\' AFTER element_id;'
;
pwg_query($query);

$query ='
ALTER TABLE '.PREFIX_TABLE.'rate ADD COLUMN date DATE NOT NULL AFTER rate;'
;
pwg_query($query);

$query ='
UPDATE '.PREFIX_TABLE.'rate SET date=NOW() WHERE date<"1990-01-01";'
;
pwg_query($query);

$query = '
ALTER TABLE '.PREFIX_TABLE.'rate ADD PRIMARY KEY (element_id, user_id, anonymous_id);'
;
pwg_query($query);

echo
"\n"
.'Table '.PREFIX_TABLE.'rate upgraded'
."\n"
;
?>
