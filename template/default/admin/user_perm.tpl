<div class="admin">{L_SELECT_USERNAME}</div>
<form method="post" name="post" action="{F_SEARCH_USER_ACTION}">
  <input type="text" name="username" maxlength="50" size="20" />
  <input type="submit" name="submituser" value="{L_LOOKUP_USER}" class="bouton" /> 
  <input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="bouton" onClick="window.open('{U_SEARCH_USER}', '_phpwgsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;" />
</form>