BODY {
	margin: 0;
	padding: 0;
	font-size: 13px;
	font-family: Arial,Helvetica,sans-serif;
	min-width: 300px; /*responsive layout*/
	background-color: {$skin.BODY.backgroundColor};
	color: {$skin.BODY.color};
}

A {
	text-decoration:none;
	color: {$skin.A.color};
}

A:hover {
	text-decoration:underline;
	color: {$skin['A:hover'].color};
}

A .pwg-icon,
.pwg-icon {
	opacity: 0.9;
  font-size:17px!important;
}
A:hover .pwg-icon {
	opacity: 1;
}

IMG {
	border: 0; /*IE <= 9 adds border for linked images*/
}

H2 {
	margin: 0;
	padding: 2px 5px 3px 0;
	text-align: left;
	font-size: 20px;
	font-weight: normal;
}

BLOCKQUOTE {
	margin: 8px 10px; /*reduce default user agent margin too large for mobiles*/
}

INPUT, SELECT {
	margin: 0;
	font-size: 1em;	/* <= some browsers don't set it correctly */
}

TABLE {	/* horizontaly centered */
	margin-left: auto;
	margin-right: auto;
}


FORM { padding: 0; margin: 0; }


{if !empty($skin.controls)}
INPUT[type="text"], INPUT[type="password"], SELECT, TEXTAREA
{if empty($skin.buttons)}
,INPUT[type="button"], INPUT[type="submit"], INPUT[type="reset"]
{/if}
{
{if !empty($skin.controls.backgroundColor)}
	background-color: {$skin.controls.backgroundColor};
{/if}
{if !empty($skin.controls.gradient)}
	{$skin.controls.gradient|cssGradient};
{/if}
{if !empty($skin.controls.color)}
	color: {$skin.controls.color};
{/if}
{if !empty($skin.controls.border)}
	border: {$skin.controls.border};
{/if}
}
{/if}

{if !empty($skin['controls:focus'])}
INPUT:focus, TEXTAREA:focus {
{if !empty($skin['controls:focus'].backgroundColor)}
	background-color: {$skin['controls:focus'].backgroundColor};
{/if}
{if !empty($skin['controls:focus'].color)}
	color: {$skin['controls:focus'].color};
{/if}
{if !empty($skin['controls:focus'].boxShadow)}
	box-shadow: {$skin['controls:focus'].boxShadow};
{/if}
}
{/if}


{if !empty($skin.buttons)}
INPUT[type="button"], INPUT[type="submit"], INPUT[type="reset"] {
{if !empty($skin.buttons.backgroundColor)}
	background-color: {$skin.buttons.backgroundColor};
{/if}
{if !empty($skin.buttons.gradient)}
	{$skin.buttons.gradient|cssGradient};
{/if}
{if !empty($skin.buttons.color)}
	color: {$skin.buttons.color};
{/if}
{if !empty($skin.buttons.border)}
	border: {$skin.buttons.border};
{/if}
}
{/if}

{if !empty($skin.buttonsHover)}
INPUT[type="button"]:hover, INPUT[type="submit"]:hover, INPUT[type="reset"]:hover {
{if !empty($skin.buttonsHover.backgroundColor)}
	background-color: {$skin.buttonsHover.backgroundColor};
{/if}
{if !empty($skin.buttonsHover.gradient)}
	{$skin.buttonsHover.gradient|cssGradient};
{/if}
{if !empty($skin.buttonsHover.color)}
	color: {$skin.buttonsHover.color};
{/if}
{if !empty($skin.buttonsHover.boxShadow)}
	box-shadow: {$skin.buttonsHover.boxShadow};
{/if}
{if !empty($skin.buttonsHover.border)}
	border: {$skin.buttonsHover.border};
{/if}
}
{/if}

FIELDSET {
	padding: 1em;
	margin: 1em 0.5em;
	border: 1px solid gray;
}

LEGEND {
	font-style: italic;
	color: inherit; /*for IE*/
}


/**
 * Content
 */

.titrePage {
	padding: 3px 10px;
	line-height: 24px;
{if !empty($skin.pageTitle.backgroundColor)}
	background-color: {$skin.pageTitle.backgroundColor};
{/if}
{if !empty($skin.pageTitle.gradient)}
	{$skin.pageTitle.gradient|cssGradient}
{/if}
{if !empty($skin.pageTitle.color)}
	color: {$skin.pageTitle.color};
{/if}
}

{if !empty($skin.pageTitle.link.color)}
.titrePage A {
	color: {$skin.pageTitle.link.color};
}
{/if}

{if !empty($skin.pageTitle.linkHover.color)}
.titrePage A:hover {
	color: {$skin.pageTitle.linkHover.color};
}
{/if}

/* now revert text colors to dropdowns*/
{if !empty($skin.pageTitle.color)}
.titrePage .switchBox {
	color: {$skin.BODY.color};
}
{/if}
{if !empty($skin.pageTitle.link.color)}
.titrePage .switchBox A {
	color: {$skin.A.color};
}
{/if}

{if !empty($skin.pageTitle.linkHover.color)}
.titrePage .switchBox A:hover {
	color: {$skin['A:hover'].color|default:$skin.A.color};
}
{/if}

{if !empty($skin.pageTitle.textShadowColor)}
.titrePage H2 A, #imageHeaderBar H2{
	text-shadow: 1px 1px 3px {$skin.pageTitle.textShadowColor};
}
{/if}

.titrePage H2 span.badge::before {
	content:'[';
}
.titrePage H2 span.badge::after {
	content:']';
}

.content .navigationBar, .content .additional_info, .content .calendarBar {
	margin: 8px 4px;
	text-align: center;
}

.content .pageNumberSelected {
	font-style: italic;
	font-weight: bold;
}

.content .additional_info {
	font-size: 110%;
}

.content .notification {
  padding:0 25px;
}

/* category and tag results paragraphs on a quick search */
.search_results {
  font-size: 16px;
  margin: 10px 16px;
}

/* actions */
.categoryActions {
	margin: 0 2px;
	width: auto;
	padding: 0;
	text-indent: 0;
	list-style: none;
	text-align: center;
	float: right;
}

.categoryActions LI {
	display: inline;
}

.switchBox {
	display: none;
	position: absolute;
	left: 0; top: 0; /*left, right set through js*/
	padding: 0.5em;
	z-index: 100;
	text-align:left;
	box-shadow: 2px 2px 5px gray;
	background-color: {$skin.dropdowns.backgroundColor};
}

.switchBoxTitle {
  border-bottom:1px solid gray;
  padding-bottom:5px;
  margin-bottom:5px;
}

#copyright {
	clear: both;
	font-size: 83%;
	text-align: center;
	margin: 0 0 10px 0;
}

A.wiki { cursor:help; }

/* Loader gif new in 2.5 */
.loader {
  display: none;
  position: fixed;
  right: 0;
  bottom: 0;
}

/* User comments */
#comments {
	padding-left: 5px;
	padding-right: 5px;
	clear: both; /*the main image and info table might float on picture page for large screens*/
}

.commentsList {
	margin: 5px;
	padding: 0;
	list-style: none;
}

.commentElement {
	border-radius: 5px;
	margin: 5px 0;
	padding: 2px 0 0 2px;
	width: 100%;
{if !empty($skin.comment.backgroundColor)}
	background-color: {$skin.comment.backgroundColor};
{/if}
}

.commentElement .description {
	overflow: auto;
	/*width: inherit;*/
}

#comments input[type="text"],
#comments TEXTAREA {
	max-width: 100%;
	width: 100%;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.commentAuthor {
	font-weight: bold;
}

.commentDate {
	font-style: italic
}

#comments FORM P	 {
	margin: 5px 0;
}


/**
 * Filter forms are displayed label by label with the input (or select...)
 * below the label. Use an UL to make a group (radiobox for instance).
 * Use a SPAN to group objects in line
 */

.filter UL {
	display: block;
	float: left;
	margin: 0 1em 0 0;
	padding: 0;
}

.filter LI {
	list-style: none;
	margin-bottom: 0.5em;
}

.filter P {
	line-height: 2em;
	margin-bottom: 0.1em;
}

.filter input[name="search_allwords"] {
	width: 50%;
	min-width: 240px;
	max-width: 500px;
}

.filter>P {
	margin-left: 1.5em;
}

.properties UL {
	list-style: none;
	margin: 0;
	padding: 0;
}

.properties LI {
	margin-bottom: 0.5em;
	padding: 0;
	line-height: 1.8em;
	clear: left;
}

.properties SPAN.property {
	font-weight: bold;
	float: left;
	width: 50%;
	text-align: right;
	margin: 0;
	padding: 0 0.5em 0 0;
}

.properties .fields,
.plugins.fields {
  width: 100%;
	display: flex;
	flex-direction: column;
	gap: 15px;
}

.properties div.property,
.plugins.fields > div {
  display: grid;
  grid-template-columns: 1fr 1fr;
	gap: 10px;
	align-items: center;
}

.plugins.fields > div {
	margin-left: 5px;
}

.properties div.property.radio {
	margin: 5px 0;
}

.properties div.property :first-child,
.plugins.fields > div > :first-child {
  text-align: right;
	font-weight: bold;
}

.properties div.property :last-child,
.plugins.fields > div > :last-child {
	width: fit-content;
}

.properties P {
	text-align: center;
	margin-top: 2em;
	margin-bottom: 2em;
}

.bottomButtons {
	margin-top: 50px !important;
}




/* jQuery datepicker */
/*IMG.ui-datepicker-trigger {
	cursor : pointer;
}*/


/**
 * Default colors
 */

 /* So that non-links are slightly greyed out */
.content .navigationBar, SPAN.calItem, TD.calDayCellEmpty {
	color: gray;
}


.errors { /* Errors display */
	color: red;
	font-weight: bold;
	margin: 5px;
	border: 1px solid red;
	background: #ffe1e1 url(../../default/icon/errors.png) no-repeat center right;
	padding: 10px 50px 10px 10px;
}

/* Informations box */
.infos {
	color: #002000;
	background: #98fb98 url(../../default/icon/infos.png) no-repeat center right;
	margin: 5px;
	padding: 10px 50px 10px 10px;
}

/* Header message like upgrade*/
.header_msgs {
	text-align:center;
	font-weight: bold;
	color:#696969;	/* dimgray */
	background-color: #d3d3d3;
	margin: 1px;
	padding: 1px;
}

.message {
  color:white;
  background-color:#666;
  margin-bottom:1em;
  padding: 12px;
  border-radius: 3px;
}

/* image comments rules */

#commentAdd {
	float: left;
	padding: 0 1%;
	width: 48%;
}

#pictureCommentList {
	float: right;
	width: 50%;
}

/*#pictureCommentList .commentsOrder	{ float: left; margin-bottom: 5px;}
#pictureCommentList .navigationBar	{ float: right; margin-bottom: 5px;}*/
#pictureComments h4					{ margin: 0;}



@media screen and (max-width:480px) {
	SELECT,INPUT { /*controls in fieldset should not trigger "scroll"*/
		max-width: 270px;
	}
}

div.token-input-dropdown{
	color:black;
}

ul.token-input-list{
	width:auto!important;
}

#albumActionsSwitcher {
	display: none;
}

@media screen and (max-width:640px) {
	#albumActionsSwitcher {
		display: block;
		width: 42px;
		padding-top: 2px;
		text-align: right;
		float: right;
	}

	#albumActionsSwitcher + .categoryActions {
		display: none;
		position: absolute;
		z-index: 1;
		background-color: {$skin.dropdowns.backgroundColor};
		padding: 10px 5px 5px;
		box-shadow: 2px 2px 5px gray;
		opacity: 0.95;
		text-align: left;
		min-width: 180px;
	}

	#albumActionsSwitcher + .categoryActions LI{
		display: block;
	}

	#albumActionsSwitcher + .categoryActions .pwg-button{
		display: block;
	}

	#albumActionsSwitcher + .categoryActions .pwg-button-text{
		display: inline;
		margin-left: 5px;
		text-transform: capitalize;
	}
}

#TagsGroupRemoveTag img{
  display:none;
}

#TagsGroupRemoveTag span{
  display:inline-block;
}

{* Css for search in set button *}
.mcs-side-results {
  display: flex;
  flex-direction: row;
  gap: 5px;
  margin: 15px 0 0 15px;
}

.mcs-side-results > div {
  background: #fafafa;
  box-shadow: 0px 2px #00000024;
  position: relative;
  padding: 4px 10px;
  border-radius: 5px;
  font-weight: 600;
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-right: 10px;
  color: #777;
  width: fit-content;
}

.mcs-side-results > div:hover {
  background: #eee;
  color: #777;
}

.mcs-side-results > div:active {
  transform: translateY(2px);
  box-shadow: none;
}

.mcs-side-results > div p{
  margin: 0 0 0 10px;
  white-space: nowrap;
  font-size: 15px;
}

.mcs-side-results .mcs-side-badge {
  border-radius: 25px;
  font-weight: 700;
  color: #fafafa;
  margin-left: 5px;
  padding: 2px 5px!important;
  font-size: 10px;
  background: #777;
}

.mcs-side-results.search-in-set-button {
  margin-bottom: 30px;
}

.mcs-side-results.search-in-set-button p {
  margin:0;
}

.mcs-side-results.search-in-set-button a {
  color:#777;
  font-weight:600;
}

.mcs-side-results.search-in-set-button a::before {
  margin-right:10px;
}

.mcs-side-results.search-in-set-button a:hover {
  color:#777;
  font-weight:600;
  text-decoration: none;
}

{if !empty($skin.searchResultItem)}
.search-result-item {
  background-color: {$skin.searchResultItem.backgroundColor};
  color: {$skin.searchResultItem.color};
}
{/if}

{if !empty($skin.formAlbumSelector.backgroundColor)}
.linkedAlbumPopInContainer {
  background-color: {$skin.formAlbumSelector.backgroundColor};
}
{/if}

{if !empty($current.formats)}
#downloadSwitchLink::after,
{/if}
#derivativeSwitchLink::after,
#sortOrderLink::after{
  display: inline-block;
  margin-left: .255em;
  vertical-align: .255em;
  content: "";
  border-top: .3em solid;
  border-right: .3em solid transparent;
  border-bottom: 0;
  border-left: .3em solid transparent;
}