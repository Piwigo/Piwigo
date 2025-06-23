#imageHeaderBar .imageNumber {
/* moved by prefilter from imageToolBar*/
	float: right;
}

#imageHeaderBar H2 {
	display: inline;
	text-align: center;
	padding: 0;
}

#imageToolBar {
	text-align: center;
	margin-bottom: 2px;
	padding-top: 2px;
	height: 28px;
{if !empty($skin.pictureBar.backgroundColor)}
	background-color: {$skin.pictureBar.backgroundColor};
{/if}
}

#imageToolBar .actionButtons {
	float: left;
}

#imageToolBar .navigationButtons   {
	float: right;
}

#imageToolBar .pwg-button {
	width:42px;
}

#theImage {
	text-align: center;
}

#imageInfos {
	position: relative; /*for IE7 positioning of "who can see this photo"*/
	min-height: 166px;
}

#linkPrev {
	float: left;
	margin: 0 10px 0 5px;
}

#linkNext {
	float: right;
	margin: 0 5px 0 10px;
	text-align: right;
}

DIV.thumbHover { /* first & last holders only*/
	width: {$SQUARE_WIDTH}px;
	height: {$SQUARE_WIDTH}px;
	border: 1px solid #ccc;
  padding: 0 5px;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.imageInfoTable UL { /*this is the album list*/
	margin: 0;
	padding: 0 0 0 1em;
	list-style-type: square;
}

.rateButton, .rateButtonSelected, .rateButtonStarFull, .rateButtonStarEmpty  {
	padding:0;
	border: 0 !important;
{if !empty($skin.controls.boxShadow) || !empty($skin.buttons.boxShadow) || !empty($skin.buttonsHover.boxShadow)}
	box-shadow: none !important;
{/if}
}

.rateButton, .rateButtonStarFull, .rateButtonStarEmpty {
	cursor: pointer;
}

.rateButtonSelected {
	font-weight: bold;
	font-size: 120%;
}

.rateButtonStarFull {
	background: transparent url('../../default/icon/rating-stars.gif') no-repeat -16px center !important;
	width:16px;
}

.rateButtonStarEmpty {
	background: transparent url('../../default/icon/rating-stars.gif') no-repeat 0 center !important;
	width:16px;
}

.imageInfoTable {
	display:table;
	margin: auto;
	font-family: Tahoma,Verdana,Helvetica,Arial,sans-serif;
}

.imageInfo {
	display: block;
	line-height: 20px;
  word-wrap: break-word;
}

.imageInfo DT {
	display:table-cell;
	text-align:right;
	font-weight:bold;
	padding-right:0.5em;
}

.imageInfo DD {
	display:table-cell;
	text-align:left;
	font-weight:normal;
}





@media screen {
/*picture page wide screen*/
.wide #theImage {
	display: inline;
	float: left;
	width: 76.1%; /*min default picture derivative width*/
}

.wide #imageInfos {
	margin-left: 76.5%; /*default picture derivative width + ~ 5px; must have enough space for thumbs*/
	border-radius: 8px 0 0 8px;
{if !empty($skin.pictureWideInfoTable.backgroundColor)}
	background-color: {$skin.pictureWideInfoTable.backgroundColor};
{/if}
}

.wide .navThumbs {
	min-width: {2*$SQUARE_WIDTH+2}px;
	width: 90%;
	max-width: {2*$SQUARE_WIDTH+40}px;
	height: {$SQUARE_WIDTH+4}px;
	margin: auto;
	padding-top: 10px;
}

.wide .navThumb {
	width: {$SQUARE_WIDTH}px;
	height: {$SQUARE_WIDTH}px;
	margin: 0 !important;
	overflow: hidden;
	text-align: left;
}

.wide .thumbHover {
	width: {$SQUARE_WIDTH}px;
	height: {$SQUARE_WIDTH}px;
	position: absolute;
}


.wide .prevThumbHover:hover { background: transparent url(../images/img_prev.png) no-repeat center center;}
.wide .nextThumbHover:hover { background: transparent url(../images/img_next.png) no-repeat center center;}

.wide .imageInfoTable {
	display: block;
	padding: 0 5px 0 10px;
	margin: 0; /*need this for ie7 override in fix-ie7*/
}

.wide .imageInfo DT {
	display: block;
	text-align: left;
	padding: 0;
}

.wide .imageInfo DD {
	display: block;
	text-align: left;
	margin: 0 0 5px 10px;
}

{if !empty($skin.widePictureBar)}
	.wide #imageToolBar {
{if !empty($skin.widePictureBar.backgroundColor)}
		background-color: {$skin.widePictureBar.backgroundColor};
{/if}
	}
{/if}
}

@media screen and (max-width:800px),
	screen and (-webkit-min-device-pixel-ratio:1.3) {
	.navThumb IMG {
		max-width: {($SQUARE_WIDTH/2)|intval}px;
		height: auto;
	}

	.wide .navThumbs {
		height: {18+($SQUARE_WIDTH/2)|intval}px;
		min-width: {$SQUARE_WIDTH+6}px;
		max-width: {$SQUARE_WIDTH+40}px;
	}

	.navThumb, .thumbHover { /* applies to wide&non wide including first & last*/
		width: {($SQUARE_WIDTH/2)|intval}px !important;
		height: {($SQUARE_WIDTH/2)|intval}px !important;
		line-height: 1; /*for first & last*/
	}

}




#imageActionsSwitch {
	display: none;
}
@media screen and (max-width:600px) {
	#imageActionsSwitch {
		display: block;
		text-align: left;
		float: left;
	}

	.actionButtonsWrapper {
		position: relative;
	}

	.actionButtonsWrapper .actionButtons {
		display: none;
		position: absolute;
		z-index: 1;
		min-width: 200px;
		background-color: {$skin.dropdowns.backgroundColor};
		padding-top: 10px;
		box-shadow: 2px 2px 5px gray;
		opacity: 0.95;
	}

	#imageToolBar .actionButtons .pwg-button {
		display: block;
		width: auto;
		text-align: left;
		height: 32px;
		padding-left: 5px;
		padding-right: 5px;
	}

	#imageToolBar .actionButtons .pwg-button-text {
		display: inline;
		margin-left: 5px;
		text-transform: capitalize;
	}

	.imageInfoTable {
		padding-top: 5px;
		clear: both;
	}
}

