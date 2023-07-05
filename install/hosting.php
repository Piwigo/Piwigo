<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+


/* PHP5 configuration for known servers */
$hosting = array(

  //1and1
    'kundenserver.de' => 'AddType x-mapp-php5 .php',

  //Apinc
    'apinc.org' => 'AddHandler x-httpd-php5 .php',

  //Free
    'free.fr' => 'php 1',

  //Lost Oasis
    'lost-oasis.net' => 'PHP_Version 5.0',
    'lo-data.net' => 'PHP_Version 5.0',

  //MediaTemple
    'gridserver.com' => 'AddHandler php5-script .php',

  //Online
    'online.net' => 'AddType application/x-httpd-php5 .php',

  //Ouvaton
    'web.ocsa-data.net' => 'AddHandler application/x-suexec-php5 .php',

  //OVH
    'ovh.net' => 'SetEnv PHP_VER 5',

  //Strato
    'rzone.de' => 'AddType application/x-httpd-php5 .php',

  //Web1.fr - NFrance
    'nfrance.com' => 'AddHandler php-fastcgi5 .php',
);
?>