<?php
$themeconf['colorscheme'] = 'clear';

/*gradients facebook '#3B5998','#2B4170' ; Google '#E64522','#C33219' ; Pinterest '#CB2027','#A0171C'; Turquoise: '#009CDA','#0073B2'*/
$skin = array(
	'BODY' => array(
			// REQUIRED
			'backgroundColor' 	=> '#141414',
			// REQUIRED
			'color' 						=> '#bbb',
		),

	'A' => array(
			// REQUIRED
			'color' 						=> '#ddd',
		),

	'A:hover' => array(
			'color' 						=> '#fff',
		),

	'controls' => array(
			'backgroundColor' 	=> '#d3d3d3',
			'color' 						=> '#000',
			'border' 						=> '1px solid gray',
	),

	'controls:focus' => array(
			'backgroundColor' 	=> '#f3f3f3',
			//'color' 						=> '#000',
			//'boxShadow' 	      => '0 0 2px white',
	),

	'menubar' => array(
			'backgroundColor' 	=> '#3F3F3F',
			//'gradient' 					=> array('#3B5998','#2B4170'),
			//'color' 						=> '#bbb',
			//'link'							=> array( 'color' => '#ddd' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
		),

	'dropdowns' => array(
			// REQUIRED - cannot be transparent
			'backgroundColor' 	=> '#010D00',
		),

	'pageTitle' => array(
			//'backgroundColor' 	=> '#3F3F3F',
			//'gradient' 					=> array('#3B5998','#2B4170'),
			//'color' 						=> '#bbb',
			//'link'							=> array( 'color' => '#ddd' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
		),

	'pictureBar' => array(
			//'backgroundColor' 	=> '#3F3F3F',
		),

	'widePictureBar' => array(
			//'backgroundColor' 	=> '#3F3F3F',
		),

	'pictureWideInfoTable' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),

	'comment' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),

	// should be white or around white
	/*'albumLegend' => array(
			'color' 	=> '#fff',
		),*/

);
?>
