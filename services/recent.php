<?php

$where = ( $user['forbidden_categories'] == '') ? '' :
  'ic.`category_id` NOT IN ('.$user['forbidden_categories'].')';
$list = implode(',', $final);
if ( $where !== '' and $list !== '' )
{
  $where .= ' AND ';
}
$where .= ( $list == '') ? '' :
  'i.`id` IN ('. $list .')';
$query='
  SELECT DISTINCT (i.`id`),
         i.`path` , i.`file` , i.`date_available` ,
         i.`date_creation`, i.`tn_ext` , i.`name` ,
         i.`filesize` , i.`storage_category_id` , i.`average_rate`,
         i.`comment` , i.`author` , i.`hit` ,i.`width` ,
         i.`height`
     FROM `'.IMAGES_TABLE.'` AS i
     INNER JOIN `'.IMAGE_CATEGORY_TABLE.'` 
           AS ic ON i.`id` = ic.`image_id`
     WHERE '. $where .' 
';
/* recent = Date_available desc order */
$query .= ' ORDER BY i.`date_available` DESC, RAND() DESC ';
$query .= ' LIMIT 0 , '. $limit .';';
// echo $query . '<br />';
$result = pwg_query( $query );

$template->assign_vars(
  array(
    'TITLE' => 'recent',
    )
  );
$template->assign_block_vars(
  'row', array()
    );
$template->assign_block_vars(
  'row.Normal',
  array(
    'WIDTH'=> 682,
    'HEIGH'=> 682,
    'URL'=> 'http://www.monsite.com/pwg/galleries/shared/cat/image.jpg',
    )
  );
$template->assign_block_vars(
  'row',
  array(
    'ID'=> 22,
    'CAPTION'=> 'L\'image que je veux',
    'DATE'=> '18/12/2006',
    'COMMENT'=> 'Voila voili voilou ! Voila voili voilou !',
    )
  );  
?>
