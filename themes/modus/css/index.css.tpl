/* Thumbnail "elastic" layout */
.thumbnails {
	margin: 1em 0;
	padding: 0;
	list-style: none;
	text-align: center;	/* to center the whole collection in .content */
}
.thumbnails LI { display: inline }

.thumbnails .wrap1 {
	margin: 0 7px 5px 7px;
	display: table-cell; display: inline-table;
	display: inline-block;/* Why 3 display option ??? */
	vertical-align: top;	/* OK with Opera and IE6 not Geko */
	text-align: center;		/* to center the thumbnail and legend in Geko/Opera */
}
.thumbnails .wrap2 {
	margin: 0;			/* important reset the margins */
	display: table-cell;/* block prevents vertical-align here */
	vertical-align: middle;/* Ok with Opera and Geko not IE6 */
	border-radius: 4px;	/* round corners with CSS3 compliant browsers */
	border: 1px solid gray;
}

.thumbnails .wrap2 A {
	display: block;
}
.thumbnails IMG {
	vertical-align: middle;
}

.thumbnails .thumbLegend {
	overflow: hidden;/* oversized legend is clipped */
	height: 4em;    /* legend height (don't set auto to be Gecko friendly)*/
}

{if $smarty.const.MODUS_POP}
.popDesc {
	display: none
}
{else}
.overDesc {
	color: white;
	background-color: rgba(0,0,0,0.5);

	position: absolute;
	height: 1.5em;
	width: 100%;
	left: 0; /* for IE7, all others use 0 by default*/
	bottom: -100px;
	overflow: hidden;
	text-align: center;
	text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
	line-height: 1.4;
	font-weight: bold;
	white-space: nowrap;
	text-overflow: ellipsis;
	transition: all 200ms ease-out 0s;
}

LI:hover>.overDesc {
	bottom: -1px; /* -1 for IE8 which has some rounding issues */
}
{/if}



/* Category thumbnails on main page, CSS code inspired from MOD subcatify */
.thumbnailCategories {
	margin: 0;
	padding: 0;
	list-style: none;
	overflow: hidden;
	width: 100%;
}

.thumbnailCategories LI {
	margin: 0;
	padding: 0;
	float: left;
	width: 49.7%;    /* 49.7% for 2 per line, 33.2% for 3 per line*/
}

.thumbnailCategory {
	display:block;
	padding: 2px 0 0 2px;
	margin: 5px;
	border: 1px solid gray;
  display:flex;
  flex-wrap: wrap;
}

.thumbnailCategory .description {
	font-size: 90%;
	overflow-y: auto;
	/*width: inherit;*/
  max-height:85%;
  /* max-width:70%; */
  flex: 1 0;
  width:70%;
  margin-right:10px;
}

.thumbnailCategory .description H3 {
	text-align: left;
	margin: 0;
	padding: 0.1em;
	font-size: 120%;
}

.thumbnailCategory .illustration{
  flex: 1 0;
  max-width:30%;
}

/* width */
.thumbnailCategory .description::-webkit-scrollbar {
  width: 10px;
}

/* Track */
.thumbnailCategory .description::-webkit-scrollbar {
  background: rgba(255,255,255, 0.2); 
  border-radius:15px;
}

/* Handle */
.thumbnailCategory .description::-webkit-scrollbar-thumb {
  background: rgba(255,255,255, 0.5); 
  border-radius:15px;
}

/* Handle on hover */
.thumbnailCategory .description::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

.thumbnailCategory .illustration img{
  object-fit: cover;
}

.illustration { /*also used by comment lists*/
	text-align: left;
	margin: 2px 0 0 2px;
	float: left;
}

.thumbnails .wrap2:hover,
.thumbnailCategory:hover {
	border-color: {$skin.A.color};	/* thumbnails border color when mouse cursor is over it */
}



/*Album thumbnails*/
.albThumbs {
	list-style: none;
	padding: 0;
	text-align: center;
}

.albThumbs LI {
	display: inline-block;
	position: relative;
	overflow: hidden;
	margin: 5px 5px;
	width: {$MODUS_ALBUM_THUMB_SIZE}px;
	height: {$MODUS_ALBUM_THUMB_SIZE}px;
}

.albImg {
	display: block;
	position: relative
  top: 0px;
  left: 0px;
}

.albLegend {
	height: 4em;
	width: 100%;
	text-align: left;
	position: absolute;
	left: 0; /* for IE7, all others use 0 by default*/
	bottom: -1px; /* -1 for IE8 which has some rounding issues */
	overflow: hidden;
	text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
	line-height: 1.4;
	text-indent: 5px;

{if !empty($skin.albumLegend.color)}
	color: {$skin.albumLegend.color};
{/if}

	filter: progid:DXImageTransform.Microsoft.gradient(startColorStr=#08000000,endColorStr=#90000000); /* IE to 9*/
	background-image: -moz-linear-gradient(top,rgba(0,0,0,0.01),rgba(0,0,0,0.5)); /* FF 3.16 to 15 */
	background-image: -webkit-linear-gradient(top,rgba(0,0,0,0.01),rgba(0,0,0,0.5)); /* Chrome, Safari */
	background-image: -ms-linear-gradient(top,rgba(0,0,0,0.01),rgba(0,0,0,0.5)); /* IE ? to 9 */
	background-image: -o-linear-gradient(top,rgba(0,0,0,0.01),rgba(0,0,0,0.5)); /* Opera 11 to 12 */
	background-image: linear-gradient(to bottom,rgba(0,0,0,0.01),rgba(0,0,0,0.5)); /* Standard must be last */
}

.albLegend H4 {
	margin: 1em 0 2px;
	overflow: hidden;
}

.albLegendRight {
	float: right;
	margin-right: 2px !important;
}

@media {cssResolution min=1.3}{
	.albThumbs LI {
		width:{($MODUS_ALBUM_THUMB_SIZE/1.5)|intval}px;height:{($MODUS_ALBUM_THUMB_SIZE/1.5)|intval}px
	}

	.albImg {
		max-height: 100%;
	}

	.albLegend {
		text-indent: 2px;
		letter-spacing: -0.5px;
		word-spacing:-1px
	}
}

.albSymbol { /* used also in other contexts*/
	font-size: 16px;
	line-height: 20px;
	cursor: pointer;
}

{if $MODUS_ALBUM_THUMB_SIZE>222 && $MODUS_ALBUM_THUMB_SIZE<360}
@media (max-width:320px), {cssResolution min=1.3}{
	.albThumbs LI {
		width: 156px;
		height: 156px;
		margin: 5px 1px;
	}
}
{/if}


/* begin chronology/calendar elements*/
.calendarViews {
	float: right;
	margin: 5px 5px 0 0;
}

.calItem {
	font-weight: bold;
	margin: 0 1px;
	padding: 0 1px;
	border: 1px solid gray;
	text-decoration: none !important;
}

.calendarCalBar {
	margin: 10px;
}

.calCalHead {
	font-weight: bold;
	font-size: 110%;
	margin: 0 2px;
}

.calCal { margin: 0 2px; }

/* nice looking month calendar*/
TABLE.calMonth {
	border: 0;
	border-collapse: collapse;
	margin-bottom: 10px;	/*<-IE ignores this */
}

TD.calDayCellFull, TD.calDayCellEmpty {
	vertical-align: top;
	font-weight: bold;
	font-size: 18px;
	border: 1px solid gray;
}

.calBackDate {
	padding-left: 3px;
	padding-top: 0;
	z-index: 2;
	position: absolute;
	color: #000;
}

.calForeDate {
	padding-left: 4px;
	padding-top: 1px;
	z-index: 3;
	position: absolute;
	color: #fff;
}
