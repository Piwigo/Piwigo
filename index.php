<?php
include_once( './include/mysql.inc.php' );
include_once( './include/functions.inc.php' );
database_connection();
// récupération des informations de configuration du site
$query  = 'select acces ';
$query .= 'from '.PREFIX_TABLE.'config;';
$row = mysql_fetch_array( mysql_query( $query ) );
$url = 'category';
if ( $row['acces'] == 'restreint' )
{
  $url = 'identification';
}
// redirection
$url.= '.php';
header( 'Request-URI: '.$url );  
header( 'Content-Location: '.$url );  
header( 'Location: '.$url );
exit();
?>