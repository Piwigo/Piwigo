<?php
/***************************************************************************
 *                 style.php is a part of PhpWebGallery                    *
 *                            -------------------                          *
 *   last update          : Friday, November 1, 2002                       *
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
$user['style'] = '<style type="text/css">
      a {
        text-decoration:none;
      }
      a:hover {
        text-decoration:underline;
      }
      a.back, body {
        color:'.$user['couleur_text_fond'].';
      }
      body,table,input {
        font-family:arial,sans-serif;
        font-size:12px;
      }
      .imgLink {
        border:1px solid '.$user['couleur_text_fond'].';
      }
      .titrePage,.titreMenu,.menu,.info, a {
        color:'.$user['couleur_text'].';
      }
      .titreMenu,.menu,.info {
        margin-bottom:5px;
        white-space:nowrap;
      }
      .menu,.titrePage,.info {
        margin-left:2px;
        margin-right:2px;
      }
      .menuInfoCat {
        font-family:sans-serif;
        font-size:11px;
      }
      .totalImages {
        text-align:center;
        margin-top:5px;
        font-family:sans-serif;
        font-size:11px;
      }
      .titreMenu {
        font-weight:600;
        text-align:center;
      }
      .info {
        text-align:right;
      }
      .titrePage {
        white-space:nowrap;
        font-weight:500;
        font-size:18px;
        text-align:center;
      }
      .comments,.infoCat,.navigationBar {
        margin-top:10px;
        margin-bottom:10px;
      }
      .comments {
        text-align:justify;
        font-style:italic;
      }
      .navigationBar {
        text-align:center;
      }
      .infoCat {
        text-align:left;
      }
      .thumbnail {
        font-size:11px;
        text-align:center;
      }
      .copyright {
        font-size:11px;
        text-align:center;
        font-family:sans-serif;
        letter-spacing:0.3mm;
      }
      .commentImage {
        font-weight:bold;
        text-align:center;
        font-size:17px;
      }
      .bouton {
        background:#EEEEEE;
      }
      input {
        border-width:1;
        border-color:#000000;
        background:#ffffff;
        color: #000000;
      }
      body {';
$image = './theme/'.$user['theme'].'/background.gif';
if ( @is_file( $image ) )
{
  $user['style'].= '
        background-image:url('.$image.');';
}
else
{
  $user['style'].= '
        background-color:'.$user['couleur_fond'].';';
}
$user['style'].= '
        margin:5px;
      }
      table {
        border-collapse:collapse;
      }
      table.thumbnail {
        border-collapse:separate;
      }
      td {
        font-family:sans-serif;
        padding:0;
      }
      .errors {
        text-align:left;
        margin-top:5px;
        margin-bottom:5px;
        background-color:red;
        font-weight:bold;
        border:1px solid black;
        color:white;
      }
    </style>';