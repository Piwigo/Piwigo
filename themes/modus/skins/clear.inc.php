<?php
$themeconf['colorscheme'] = 'clear';

/*gradients facebook '#3B5998','#2B4170' ; Google '#E64522','#C33219' ; Pinterest '#CB2027','#A0171C'; Turquoise: '#009CDA','#0073B2'*/
$skin = array(
	'BODY' => array(
			// REQUIRED
			'backgroundColor' 	=> '#eee',
			// REQUIRED
			'color' 						=> '#444',
		),

	'A' => array(
			// REQUIRED
			'color' 						=> '#222',
		),

	'A:hover' => array(
			'color' 						=> '#000',
		),

	'menubar' => array(
			'backgroundColor' 	=> '#ccc',
			//'gradient' 					=> array('#3B5998','#2B4170'),
			'color' 						=> '#444',
			'link'							=> array( 'color' => '#222' ),
			'linkHover'					=> array( 'color' => '#000' ),
		),

	'dropdowns' => array(
			// REQUIRED - cannot be transparent
			'backgroundColor' 	=> '#ccc',
		),

	'pageTitle' => array(
			'backgroundColor' 	=> '#ccc',
			//'gradient' 					=> array('#A0171C','#CB2027'),
			'color' 						=> '#444',
			'link'							=> array( 'color' => '#222' ),
			'linkHover'					=> array( 'color' => '#000' ),
		),

	'pictureBar' => array(
			'backgroundColor' 	=> '#ccc',
		),

	/*'widePictureBar' => array(
			'backgroundColor' 	=> '#ccc',
		),*/

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