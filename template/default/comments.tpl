<table style="width:100%;">
  <tr align="center" valign="middle">
	<td>
	  {T_START}1px{T_BEGIN}
		<div class="titrePage">{L_TITLE}</div>
	  {T_END}
	  <div style="margin-bottom:20px"></div>
	  {T_START}90%{T_BEGIN}
<table style="width:100%;">
  <tr>
    <th>
      [
      <!-- BEGIN last_day_option -->
      <a href="{last_day_option.U_OPTION}" style="{last_day_option.T_STYLE}">{last_day_option.OPTION}</a>{T_SEPARATION}
      <!-- END last_day_option -->
      {L_STATS}
      ]
      [ <a href="{U_HOME}">{L_RETURN}</a> ]
    </th>
  </tr>
</table>
<!-- BEGIN picture -->
<div style="border:2px solid gray;margin:2px;padding:2px;">
  <table style="width:100%;">
    <tr>
     <td valign="top" style="width:15%;">
       <!-- the thumbnail of the picture, linked to the full size page -->
       <a href="{picture.U_THUMB}" title="{picture.TITLE_IMG}">
         <img src="{picture.I_THUMB}" class="imgLink" alt="{picture.THUMB_ALT_IMG}"/>
       </a>
     </td>
     <td style="padding:10px;width:85%;">
       <div style="font-weight:bold;padding-left:10px;text-align:left;">{picture.TITLE_IMG}</div>
       <!-- BEGIN comment -->
             <table class="tableComment">
               <tr>
                 <td rowspan="2" valign="top" class="cellAuthor">
                   <div class="commentsAuthor">{picture.comment.AUTHOR}</div>
                 </td>
                 <td class="cellInfo">
                   <div class="commentsInfos">
                     {picture.comment.DATE}
                   </div>
                 </td>
               </tr>
               <tr>
                 <td>
                   <div class="commentsContent">{picture.comment.CONTENT}</div>
                 </td>
               </tr>
             </table>
       <!-- END comment -->
     </td>
    </tr>
  </table>
</div>
<!-- END picture -->
	  {T_END}
	</td>
  </tr>
</table>