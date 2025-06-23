<?php
/*gradients facebook '#3B5998','#2B4170' ; Google '#E64522','#C33219' ; Pinterest '#CB2027','#A0171C'*/
$themeconf['colorscheme'] = 'clear';

$skin = array(
	'BODY' => array(
			// REQUIRED
			'backgroundColor' 	=> '#fff',
			// REQUIRED
			'color' 						=> '#000',
		),
		
	'A' => array(
			// REQUIRED
			'color' 						=> '#00f',
		),

	'A:hover' => array(
			'color' 						=> '#000',
		),

	'menubar' => array(
			'backgroundColor' 	=> '#C33219', 
			'gradient' 					=> array('#E64522','#C33219'),
			'color' 						=> '#bbb',
			'link'							=> array( 'color' => '#ddd' ),
			'linkHover'					=> array( 'color' => '#fff' ),
		),

	'dropdowns' => array(
			// REQUIRED - cannot be transparent
			'backgroundColor' 	=> '#f2f2f2',
		),

	'pageTitle' => array(
			'backgroundColor' 	=> '#2B4170', 
			'gradient' 					=> array('#3B5998','#2B4170'),
			'color' 						=> '#bbb',
			'link'							=> array( 'color' => '#ddd' ),
			'linkHover'					=> array( 'color' => '#fff' ),
		),

	'pictureBar' => array(
			'backgroundColor' 	=> '#ccc',
		),

	'widePictureBar' => array(
			'backgroundColor' 	=> '#dca',
		),

	'pictureWideInfoTable' => array(
			'backgroundColor' 	=> '#ccc',
		),

	'comment' => array(
			'backgroundColor' 	=> '#ccc',
		),

	// should be white or around white
	'albumLegend' => array(
			'color' 	=> '#fff',
		),

);
?>