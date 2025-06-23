<?php
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
			'backgroundColor' 	=> 'transparent',
			'color' 						=> 'inherit',
			'border' 						=> '1px solid gray',
	),

	'controls:focus' => array(
			'backgroundColor' 	=> '#2B4170',
			'color' 						=> '#fff',
			'boxShadow' 	      => '0 0 2px white',
	),

	'buttons' => array(
			'backgroundColor' 	=> '#2B4170',
			'gradient' 					=> array('#3B5998','#2B4170'),
			'color' 						=> '#ddd',
			'border' 						=> '1px solid gray',
	),

	'buttonsHover' => array(
			'color' 						=> '#fff',
			'boxShadow' 	      => '0 0 2px white',
	),

	'menubar' => array(
			'backgroundColor' 	=> '#2B4170',
			'gradient' 					=> array('#3B5998','#2B4170'),
			'color' 						=> '#ddd',
			'link'							=> array( 'color' => '#fff' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
		),

	'dropdowns' => array(
			// REQUIRED - cannot be transparent
			'backgroundColor' 	=> '#3F3F3F',
		),

	'pageTitle' => array(
			'backgroundColor' 	=> '#2B4170',
			'gradient' 					=> array('#2B4170','#3B5998'),
			'color' 						=> '#ddd',
			'link'							=> array( 'color' => '#fff' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
			'textShadowColor'   => 'rgba(0,0,0,0.8)',
		),

	/*'pictureBar' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),*/

	'widePictureBar' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),

	'pictureWideInfoTable' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),

	'comment' => array(
			'backgroundColor' 	=> '#3F3F3F',
		),

	'searchResultItem' => array(
			'backgroundColor' 	=> '#333333',
			'color'		=> '#bbbbbb',
		),

	// should be white or around white
	/*'albumLegend' => array(
			'color' 	=> '#fff',
		),*/

);
?>