<div class="titrePage">{L_TITLE}</div>
<BR />
<form method="post" action="{F_ACTION}" style="text-align:center">
<!-- BEGIN errors -->
<div class="errors">
	<ul>
	  <!-- BEGIN error -->
	  <li>{errors.error.ERROR}</li>
	  <!-- END error -->
	</ul>
  </div>
<!-- END errors -->
{L_SEARCH}
<input type="text" name="search" size="40" value="{F_TEXT_VALUE}" /><br />
<input class="radio" type="radio" name="mode" value="OR" checked="checked" /> {L_SEARCH_OR}
<input class="radio" type="radio" name="mode" value="AND" /> {L_SEARCH_AND} <BR />
<input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" /><BR />
<a href="{U_HOME}">[ {L_RETURN} ]</a><BR />
<div style="margin-top:10px;">* : {L_COMMENTS}</div>
</form>