<table style="width:100%;height:100%">
  <tr align="center" valign="middle">
	<td>
	  {T_START}1px{T_BEGIN}
		<div class="titrePage">{L_TITLE}</div>
	  {T_END}
	  <div style="margin-bottom:5px;">&nbsp;</div>
	  {T_START}50%{T_BEGIN}
		<div style="margin-left:auto;margin-right:auto;text-align:center;">
		  <form method="post" action="{F_ACTION}">
			<table style="width:80%;margin-left:auto;margin-right:auto;">
			  <!-- BEGIN errors -->
			  <tr>
				<td colspan="2">
				  <div class="errors">
					<ul>
					  <!-- BEGIN error -->
					  <li>{errors.error.ERROR}</li>
					  <!-- END error -->
					</ul>
				  </div>
				</td>
			  </tr>
			  <!-- END errors -->
			  <tr>
				<td colspan="2">
				  <div style="margin-bottom:10px;">&nbsp;</div>
				</td>
			  </tr>
			  <tr>
				<td class="menu">{L_SEARCH}</td>
				<td class="menu">
				  <input type="text" name="search" size="40" value="{F_TEXT_VALUE}" /><br />
				  <input class="radio" type="radio" name="mode" value="OR" checked="checked" /> {L_SEARCH_OR}
				  <input class="radio" type="radio" name="mode" value="AND" /> {L_SEARCH_AND}
				</td>
			  </tr>
			  <tr>
				<td align="center" colspan="2">
				  <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" style="margin:10px;" />
				</td>
			  </tr>
			  <tr>
				<td align="center" colspan="2">
				  <a href="{U_HOME}">[ {L_RETURN} ]</a>
				</td>
			  </tr>
			  <tr>
				<td colspan="2">
				  <div style="margin-top:10px;">* : {L_COMMENTS}</div>
				</td>
			  </tr>
			</table>
		  </form>
		</div>
	  {T_END}
	</td>
  </tr>
</table>