<?php
$themeconf['colorscheme'] = 'clear';

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
			'backgroundColor' 	=> '#3F3F3F',
			'color' 						=> '#fff',
			'boxShadow' 	      => '0 0 2px white',
	),

	'buttons' => array(
			'backgroundColor' 	=> '#0073B2',
			'gradient' 					=> array('#009CDA','#0073B2'),
			'color' 						=> '#ddd',
			'border' 						=> '1px solid gray',
	),

	'buttonsHover' => array(
			'color' 						=> '#fff',
			'boxShadow' 	      => '0 0 2px white',
	),

	'menubar' => array(
			'backgroundColor' 	=> '#0073B2',
			'gradient' 					=> array('#009CDA','#0073B2'),
			'color' 						=> '#ddd',
			'link'							=> array( 'color' => '#fff' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
		),

	'dropdowns' => array(
			// REQUIRED - cannot be transparent
			'backgroundColor' 	=> '#2D2D2D',
		),

	'pageTitle' => array(
			'backgroundColor' 	=> '#009CDA',
			'gradient' 					=> array('#0073B2','#009CDA'),
			'color' 						=> '#ddd',
			'link'							=> array( 'color' => '#fff' ),
			//'linkHover'					=> array( 'color' => '#fff' ),
			'textShadowColor'   => '#000',
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

	// should be white or around white
	/*'albumLegend' => array(
			'color' 	=> '#fff',
		),*/

);
?>