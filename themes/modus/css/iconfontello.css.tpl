@import "fontello/css/modus.css";

A:hover .pwg-icon:before{
	text-decoration: none !important; /* IE 8,9*/
}

.pwg-icon {
{if isset($loaded_plugins['language_switch']) || isset($loaded_plugins['BatchDownloader'])}
	display: inline-block;
{/if}
	font-size: 24px
}


.pwg-button-text{
	display:none;
}

.pwg-state-disabled .pwg-icon {
	opacity: .5;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
	filter: alpha(opacity=50);
}

.pwg-button {
	display: inline-block;
	vertical-align: top;
	cursor:pointer;
}

.pwg-icon-slideshow:before { content: '\e832';}
.pwg-icon-favorite-del:before { content: '\e831';}
.pwg-icon-category-edit:before { content: '\E84F';}
.pwg-icon-edit:before { content: '\E84F';}
.pwg-icon-caddie-add:before { content: '\E812';}
.pwg-icon-representative { content: '\E80B'; }

{if $conf.index_posted_date_icon}
  {if $conf.index_created_date_icon}
.pwg-icon-calendar:before { content: '\E81B'; }
.pwg-icon-camera-calendar:before { content: '\E81C'; }
  {else}
.pwg-icon-calendar:before { content: '\E81B'; }
  {/if}
{/if}

{if  isset($loaded_plugins['BatchDownloader'])}
  .batch-downloader-icon {
    background:none!important
    {* width: 26px *}
  }
  
  .batch-downloader-icon:before { 
    font-family: "modus";
    font-style: normal;
    font-weight: normal;
    speak: never;
    display: inline-block;
    text-decoration: inherit;
    width: 1em;
    margin-right: .2em;
    text-align: center;
    font-variant: normal;
    text-transform: none;
    line-height: 1em;
    margin-left: .2em;
    -webkit-font-smoothing: antialiased;
    content:'\E834'; 
  }

{/if}

{if  isset($loaded_plugins['UserCollections'])}
.user-collections-icon, .user-collections-share-icon, .user-collections-clear-icon, .user-collections-delete-icon, .user-collections-mail-icon {
	display: inline-block;
	height: 26px;
	width: 26px;
}
{/if}