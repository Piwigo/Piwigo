<!-- BEGIN search -->
<div class="admin">{L_SELECT_USERNAME}</div>
<form method="post" name="post" action="{F_SEARCH_USER_ACTION}">
  <input type="text" name="username" maxlength="50" size="20" />
  <input type="hidden" name="mode" value="edit" />{S_HIDDEN_FIELDS}
  <input type="submit" name="submituser" value="{L_LOOKUP_USER}" class="bouton" /> 
  <input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="bouton" onClick="window.open('{U_SEARCH_USER}', '_phpwgsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
</form>
<!-- END search -->
<!-- BEGIN permission -->
<div class="admin">{L_AUTH_USER}</div>
<form action="{F_AUTH_ACTION}" method="POST">
  <ul class="menu">
    <!-- BEGIN category -->
        <li> <a href="{permission.category.CAT_URL}"><span style="color:{#color}">{permission.category.CAT_NAME}</span></a>
        <!-- BEGIN parent_forbidden -->
        {L_PARENT_FORBIDDEN}&nbsp;-&nbsp;
        <!-- END parent_forbidden -->
        <input type="radio" name="{permission.category.CAT_ID}" value="0" {permission.category.AUTH_YES}/>{L_AUTHORIZED}
        <input type="radio" name="{permission.category.CAT_ID}" value="1" {permission.category.AUTH_NO}/>{L_FORBIDDEN}
		</li>
    <!-- END category -->
	</ul>
	<input type="submit" name="submit" class="bouton" value="{L_SUBMIT}"/>
</form>
<!-- END permission -->