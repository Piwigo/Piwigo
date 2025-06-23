{include file='../../default/template/month_calendar.tpl'}
{if isset($chronology_calendar.month_view)}
{html_style}
.calMonth TH{
	max-width:{$chronology_calendar.month_view.CELL_WIDTH}px;
	overflow:hidden;
	text-overflow:ellipsis
}
.calImg IMG{
	max-height:100%;
	width:auto
}

{$crt_width=$chronology_calendar.month_view.CELL_WIDTH}
@media (max-width:{7*($crt_width+3)}px){
{$crt_width=($crt_width/1.5)|intval}
	.calMonth TH{
		max-width:{$crt_width}px
	}
	.calMonth TD,.calMonth .calImg{
		width:{$crt_width}px;
		height:{$crt_width}px
	}
}

@media (max-width:{7*($crt_width+3)}px){
{$crt_width=($chronology_calendar.month_view.CELL_WIDTH/2)|intval}
	.calMonth TH{
		max-width:{$crt_width}px
	}
	.calMonth TD,.calMonth .calImg{
		font-size:16px;
		width:{$crt_width}px;
		height:{$crt_width}px
	}
}

{if 7*($crt_width+3)>320}
@media (max-width:360px){
{$crt_width=(320/7-1)|intval}
	.calMonth TH{
		max-width:{$crt_width-2}px
	}
	.calMonth TD,.calMonth .calImg{
		font-size:12px;
		padding:0;
		width:{$crt_width}px;
		height:{$crt_width}px
	}
}
{/if}
{/html_style}
{/if}