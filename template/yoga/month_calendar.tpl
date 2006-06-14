<!-- BEGIN calendar -->
<!-- $Id:$ -->
<!-- BEGIN thumbnails -->
<table class="calMonth">
<!-- BEGIN head -->
 <thead><tr>
 <!-- BEGIN col -->
   <td class="calDayHead">{calendar.thumbnails.head.col.LABEL}</td>
 <!-- END col -->
 </tr></thead>
<!-- END head -->
<!-- BEGIN row -->
 <tr>
 <!-- BEGIN col -->
 <!-- BEGIN blank -->
 <td class="calDayCellBlank" style="width:{calendar.thumbnails.WIDTH}px;height:{calendar.thumbnails.HEIGHT}px;">
 <!-- END blank -->
 <!-- BEGIN empty -->
 <td class="calDayCellEmpty" style="width:{calendar.thumbnails.WIDTH}px;height:{calendar.thumbnails.HEIGHT}px;">{calendar.thumbnails.row.col.empty.LABEL}
 <!-- END empty -->
 <!-- BEGIN full -->
 <td class="calDayCellFull"><div class="calBackDate">{calendar.thumbnails.row.col.full.LABEL}</div><div class="calForeDate">{calendar.thumbnails.row.col.full.LABEL}</div>
	<div class="calImg" style="width:{calendar.thumbnails.WIDTH}px;height:{calendar.thumbnails.HEIGHT}px;"><a href="{calendar.thumbnails.row.col.full.U_IMG_LINK}">
	  <img style="{calendar.thumbnails.row.col.full.STYLE}" {calendar.thumbnails.row.col.full.IMG_WIDTH} {calendar.thumbnails.row.col.full.IMG_HEIGHT}
	       src="{calendar.thumbnails.row.col.full.IMAGE}" alt="{calendar.thumbnails.row.col.full.IMAGE_ALT}"
	    title="{calendar.thumbnails.row.col.full.IMAGE_ALT}">
	</a></div>
 <!-- END full -->
 </td>
 <!-- END col -->
 </tr>
<!-- END row -->
</table>
<!-- END thumbnails -->
<!-- END calendar -->
